<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\UserAddressModel;
use App\Models\ProductModel;
use App\Models\ProductVariationModel;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;

class Checkout extends BaseController
{
    protected $orderModel;
    protected $orderItemModel;
    protected $addressModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->addressModel = new UserAddressModel();
        $this->userModel = new \App\Models\UserModel();
        
        // Configurar Mercado Pago (versión 3.x)
        $accessToken = getenv('MERCADOPAGO_ACCESS_TOKEN');
        if ($accessToken) {
            MercadoPagoConfig::setAccessToken($accessToken);
            // Para pruebas locales (opcional)
            MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
        }
    }
    
    public function index()
    {
        $cart = session('cart');
        
        if (empty($cart)) {
            return redirect()->to('/carrito')->with('error', 'Tu carrito está vacío');
        }
        
        $cartItems = $this->getCartItems();
        
        $viewData = array_merge($this->getBaseData(), [
            'title' => 'Finalizar Compra',
            'cartItems' => $cartItems,
            'total' => session('cartTotal'),
            'isLoggedIn' => session('isLoggedIn')
        ]);
        
        if (session('isLoggedIn')) {
            $userId = session('userId');
            $viewData['addresses'] = $this->addressModel->getAddressesByUser($userId);
            $viewData['defaultAddress'] = $this->addressModel->getDefaultAddress($userId);
        }
        
        return view('checkout/index', $viewData);
    }
    
    public function process()
    {
        $cart = session('cart');
        
        if (empty($cart)) {
            return redirect()->to('/carrito')->with('error', 'Tu carrito está vacío');
        }
        
        $shippingAddress = null;
        $userId = null;
        $email = null;
        $firstName = null;
        $lastName = null;
        $phone = null;
        
        if (session('isLoggedIn')) {
            $userId = session('userId');
            $addressId = $this->request->getPost('address_id');
            
            if ($addressId && $addressId != 'new') {
                $address = $this->addressModel->find($addressId);
                if ($address && $address->user_id == $userId) {
                    $shippingAddress = $address->address_line1 . "\n" .
                                      ($address->address_line2 ? $address->address_line2 . "\n" : '') .
                                      $address->city . ", " . $address->state . "\n" .
                                      $address->postal_code . " - " . $address->country;
                }
            } else {
                $rules = [
                    'address_line1' => 'required',
                    'city' => 'required',
                    'country' => 'required'
                ];
                
                if (!$this->validate($rules)) {
                    return redirect()->back()->with('errors', $this->validator->getErrors());
                }
                
                $shippingAddress = $this->request->getPost('address_line1') . "\n" .
                                  ($this->request->getPost('address_line2') ? $this->request->getPost('address_line2') . "\n" : '') .
                                  $this->request->getPost('city') . ", " . $this->request->getPost('state') . "\n" .
                                  $this->request->getPost('postal_code') . " - " . $this->request->getPost('country');
            }
        } else {
            $rules = [
                'email' => 'required|valid_email',
                'first_name' => 'required',
                'last_name' => 'required',
                'address_line1' => 'required',
                'city' => 'required',
                'country' => 'required'
            ];
            
            if (!$this->validate($rules)) {
                return redirect()->back()->with('errors', $this->validator->getErrors());
            }

            $email = $this->request->getPost('email');
            $firstName = $this->request->getPost('first_name');
            $lastName = $this->request->getPost('last_name');
            $phone = $this->request->getPost('phone') ?? '';
            
            $shippingAddress = $this->request->getPost('address_line1') . "\n" .
                              ($this->request->getPost('address_line2') ? $this->request->getPost('address_line2') . "\n" : '') .
                              $this->request->getPost('city') . ", " . $this->request->getPost('state') . "\n" .
                              $this->request->getPost('postal_code') . " - " . $this->request->getPost('country');
            
            // Buscar o crear usuario invitado
            $existingUser = $this->userModel->where('email', $email)->first();
            
            if ($existingUser) {
                $userId = $existingUser->id;
            } else {
                $userId = $this->userModel->createGuestUser($email, $firstName, $lastName, $phone);
            }
            
            // Guardar datos en sesión para el webhook/éxito
            session()->set([
                'isGuest' => true,
                'guestUserId' => $userId,
                'guestEmail' => $email,
                'guestName' => $firstName . ' ' . $lastName
            ]);
            
            // Enviar email de bienvenida con código OTP
            $this->sendWelcomeOtp($email, $userId, $firstName);
        }

        if (empty($shippingAddress)) {
            return redirect()->back()->with('error', 'Debes proporcionar una dirección de envío');
        }
        
        $orderNumber = $this->orderModel->generateOrderNumber();
        $totalAmount = session('cartTotal');
        
        $orderData = [
            'order_number' => $orderNumber,
            'status' => 'Confirmada',
            'total_amount' => $totalAmount,
            'shipping_address' => $shippingAddress,
            'payment_method' => 'Mercado Pago',
            'payment_status' => 'pendiente',
            'user_id' => $userId
        ];
        
        $orderId = $this->orderModel->insert($orderData);
        
        if (!$orderId) {
            return redirect()->back()->with('error', 'Error al crear el pedido');
        }
        
        $cartItems = $this->getCartItems();

        foreach ($cartItems as $item) {
            $this->orderItemModel->insert([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'variation_id' => $item['variation_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'subtotal' => $item['subtotal'],
                'product_name_snapshot' => $item['product_name'],
                'variation_snapshot' => $item['variation_name']
            ]);
        }
        
        // Crear preferencia de Mercado Pago
        $preference = $this->createMercadoPagoPreference($orderId, $cartItems);

        if (!$preference) {
            // Si falla la creación, borramos el pedido para no tener datos huérfanos
            $this->orderModel->delete($orderId);
            return redirect()->back()->with('error', 'No se pudo iniciar el proceso de pago. Intenta de nuevo.');
        }

        // Guardar el preference_id en el pedido
        $this->orderModel->update($orderId, [
            'mercadopago_payment_id' => $preference->id
        ]);

        // Redirigir a Mercado Pago
        return redirect()->to($preference->init_point);
    }

    /**
     * Crea la preferencia de pago en Mercado Pago
     */
    private function createMercadoPagoPreference($orderId, $items)
    {
        // Construir los datos de la preferencia
        $mpItems = [];
        foreach ($items as $item) {
            $mpItems[] = [
                "id" => (string) $item['product_id'],
                "title" => $item['product_name'] . ($item['variation_name'] ? ' - ' . $item['variation_name'] : ''),
                "quantity" => (int) $item['quantity'],
                "unit_price" => (float) $item['price'],
                "currency_id" => "UYU"
            ];
        }
        
        $payer = [];
        if (session('isLoggedIn')) {
            $payer['email'] = session('userEmail');
        } elseif (session('guest_data')) {
            $guest = session('guest_data');
            $payer['email'] = $guest['email'];
            $payer['name'] = $guest['first_name'];
            $payer['surname'] = $guest['last_name'];
        }
        
        $backUrls = [
            'success' => base_url('checkout/exito/' . $orderId),
            'failure' => base_url('checkout/error'),
            'pending' => base_url('checkout/pendiente')
        ];
        
        $request = [
            "items" => $mpItems,
            "payer" => $payer,
            "back_urls" => $backUrls,
            "auto_return" => "approved",
            "external_reference" => (string) $orderId,
            "statement_descriptor" => $this->lpConfig->storeName ?? "LP Store"
        ];
        
        // Usar cURL directamente
        $accessToken = getenv('MERCADOPAGO_ACCESS_TOKEN');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/checkout/preferences");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $accessToken,
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($httpCode !== 200 && $httpCode !== 201) {
            log_message('error', 'MercadoPago API Error - HTTP ' . $httpCode . ': ' . $response);
            if ($curlError) {
                log_message('error', 'MercadoPago cURL Error: ' . $curlError);
            }
            return null;
        }
        
        $preference = json_decode($response);
        
        if (!isset($preference->init_point)) {
            log_message('error', 'MercadoPago Response without init_point: ' . $response);
            return null;
        }
        
        return $preference;
    }

    public function processPayment()
    {
        // Verificar que la petición es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acceso no permitido']);
        }
        
        // Obtener datos del Bricks
        $paymentData = $this->request->getJSON(true);
        
        log_message('debug', 'Payment Data recibido: ' . json_encode($paymentData));
        
        // Validar datos mínimos
        if (empty($paymentData['token']) || empty($paymentData['transaction_amount'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Datos de pago incompletos'
            ]);
        }
        
        // Verificar que hay un carrito activo
        $cart = session('cart');
        if (empty($cart)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El carrito está vacío'
            ]);
        }
        
        try {
            // Configurar Mercado Pago
            \MercadoPago\MercadoPagoConfig::setAccessToken(getenv('MERCADOPAGO_ACCESS_TOKEN'));
            
            // Crear cliente de pagos
            $client = new \MercadoPago\Client\Payment\PaymentClient();
            
            // Datos del pagador (prioridad: datos del Bricks, luego sesión)
            $payer = [];
            
            // Email: primero del Bricks, luego sesión
            if (!empty($paymentData['payer']['email'])) {
                $payer['email'] = $paymentData['payer']['email'];
            } else {
                $payer['email'] = session('userEmail') ?? session('guestEmail') ?? 'cliente@example.com';
            }
            
            // Identificación: viene dentro de payer.identification
            if (!empty($paymentData['payer']['identification']['type']) && !empty($paymentData['payer']['identification']['number'])) {
                $payer['identification'] = [
                    'type' => $paymentData['payer']['identification']['type'],
                    'number' => $paymentData['payer']['identification']['number']
                ];
            }
            
            // Construir el pago usando snake_case (como viene del frontend)
            $paymentRequest = [
                'token' => $paymentData['token'],
                'issuer_id' => $paymentData['issuer_id'] ?? null,
                'payment_method_id' => $paymentData['payment_method_id'],
                'transaction_amount' => (float) $paymentData['transaction_amount'],
                'installments' => $paymentData['installments'] ?? 1,
                'payer' => $payer,
                'description' => 'Compra en ' . $this->lpConfig->storeName,
                'external_reference' => (string) uniqid('lp_')
            ];
            
            log_message('debug', 'Payment Request: ' . json_encode($paymentRequest));
            
            // Crear el pago
            $payment = $client->create($paymentRequest);
            
            // Registrar el resultado
            log_message('info', 'MercadoPago Payment Response: ' . json_encode($payment));
            
            if ($payment->status === 'approved') {
                // Pago aprobado, crear el pedido
                
                // Obtener dirección de envío del checkout
                $shippingAddress = session('checkout_shipping_address');
                
                // Crear el pedido
                $orderNumber = $this->orderModel->generateOrderNumber();
                $totalAmount = session('cartTotal');
                
                $orderData = [
                    'order_number' => $orderNumber,
                    'status' => 'Confirmada',
                    'total_amount' => $totalAmount,
                    'shipping_address' => $shippingAddress,
                    'payment_method' => 'Mercado Pago',
                    'payment_status' => 'aprobado',
                    'mercadopago_payment_id' => $payment->id
                ];
                
                if (session('isLoggedIn')) {
                    $orderData['user_id'] = session('userId');
                } elseif (session('guestUserId')) {
                    $orderData['user_id'] = session('guestUserId');
                }
                
                $orderId = $this->orderModel->insert($orderData);
                
                if ($orderId) {
                    // Guardar items del pedido
                    $cartItems = $this->getCartItems();
                    foreach ($cartItems as $item) {
                        $this->orderItemModel->insert([
                            'order_id' => $orderId,
                            'product_id' => $item['product_id'],
                            'variation_id' => $item['variation_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['price'],
                            'subtotal' => $item['subtotal'],
                            'product_name_snapshot' => $item['product_name'],
                            'variation_snapshot' => $item['variation_name']
                        ]);
                    }
                    
                    // Vaciar carrito
                    session()->set('cart', []);
                    session()->set('cartCount', 0);
                    session()->set('cartTotal', 0);
                    
                    // Limpiar dirección de envío guardada
                    session()->remove('checkout_shipping_address');
                    
                    return $this->response->setJSON([
                        'success' => true,
                        'redirect' => base_url('checkout/exito/' . $orderId),
                        'payment_id' => $payment->id
                    ]);
                }
            }
            
            // Si el pago no fue aprobado
            $statusDetail = $payment->status_detail ?? 'sin detalle';
            return $this->response->setJSON([
                'success' => false,
                'message' => "El pago no fue aprobado. Estado: {$payment->status} - {$statusDetail}"
            ]);
            
        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            $apiResponse = $e->getApiResponse();
            $content = $apiResponse ? $apiResponse->getContent() : [];
            log_message('error', 'MercadoPago API Error: ' . $e->getMessage());
            log_message('error', 'Response: ' . json_encode($content));
            
            $errorMessage = $content['message'] ?? $content['cause'][0]['description'] ?? $e->getMessage();
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $errorMessage
            ]);
        } catch (\Exception $e) {
            log_message('error', 'MercadoPago Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error interno al procesar el pago: ' . $e->getMessage()
            ]);
        }
    }

    public function saveShipping()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }
        
        $data = $this->request->getJSON(true);
        
        // Si el usuario no está logueado, guardar datos de invitado en sesión
        if (!session('isLoggedIn')) {
            if (isset($data['email'])) {
                session()->set('guest_data', [
                    'email' => $data['email'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone']
                ]);
            }
        }
        
        // Guardar dirección de envío en sesión
        session()->set('checkout_shipping_address', 
            $data['address_line1'] . "\n" .
            ($data['address_line2'] ?? '') . "\n" .
            $data['city'] . ", " . ($data['state'] ?? '') . "\n" .
            ($data['postal_code'] ?? '') . " - " . $data['country']
        );
        
        return $this->response->setJSON(['success' => true]);
    }
    
    public function success($orderId)
    {
        $order = $this->orderModel->find($orderId);

        if (!$order) {
            return redirect()->to('/')->with('error', 'Pedido no encontrado');
        }

        // Verificar el estado desde la URL (payment_status puede venir por GET)
        $paymentStatus = $this->request->getGet('payment_status');
        $paymentId = $this->request->getGet('payment_id');
        
        if ($paymentStatus === 'approved') {
            // Actualizar estado del pedido
            $this->orderModel->update($orderId, [
                'payment_status' => 'aprobado',
                'status' => 'Confirmada',
                'mercadopago_payment_id' => $paymentId
            ]);
            
            // Vaciar el carrito
            session()->set('cart', []);
            session()->set('cartCount', 0);
            session()->set('cartTotal', 0);
            
            // Limpiar datos de invitado
            if (session('isGuest')) {
                session()->remove('isGuest');
                session()->remove('guestUserId');
                session()->remove('guestEmail');
                session()->remove('guestName');
            }
            
            $viewData = array_merge($this->getBaseData(), [
                'title' => 'Compra Exitosa',
                'order' => $order
            ]);
            return view('checkout/success', $viewData);
        } else {
            return redirect()->to('/carrito')->with('warning', 'Estamos verificando el estado de tu pago. Te llegará un email de confirmación.');
        }
    }
    
    public function error()
    {
        return redirect()->to('/carrito')->with('error', 'Hubo un problema con el pago. Intenta nuevamente.');
    }
    
    public function pending()
    {
        return redirect()->to('/carrito')->with('warning', 'Tu pago está siendo procesado. Te contactaremos cuando esté confirmado.');
    }
    
    /**
     * Webhook para recibir notificaciones de Mercado Pago
     */
    public function webhook()
    {
        $json = $this->request->getBody();
        $notification = json_decode($json);
        
        log_message('info', 'MercadoPago Webhook recibido: ' . $json);
        
        if (!$notification || !isset($notification->data->id)) {
            log_message('error', 'MercadoPago Webhook: Datos inválidos');
            return $this->response->setStatusCode(400)->setBody('Invalid data');
        }
        
        $paymentId = $notification->data->id;
        
        // Aquí podrías consultar la API de Mercado Pago para verificar el estado
        // y actualizar el pedido correspondiente.
        // Por simplicidad, actualizamos por el external_reference que deberías guardar.
        
        log_message('info', 'MercadoPago Webhook: Pago ID ' . $paymentId);
        
        return $this->response->setStatusCode(200)->setBody('OK');
    }
    
    private function getCartItems()
    {
        $cart = session('cart');
        $items = [];
        
        $productModel = new ProductModel();
        $variationModel = new ProductVariationModel();
        
        foreach ($cart as $item) {
            $product = $productModel->find($item['product_id']);
            if ($product) {
                $variation = null;
                if (isset($item['variation_id']) && $item['variation_id']) {
                    $variation = $variationModel->find($item['variation_id']);
                }
                
                $items[] = [
                    'product_id' => $item['product_id'],
                    'variation_id' => $item['variation_id'] ?? null,
                    'product_name' => $product->name,
                    'variation_name' => $variation ? $variation->attribute . ': ' . $variation->value : null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity']
                ];
            }
        }
        
        return $items;
    }

    /**
     * Envía un email de bienvenida al usuario invitado con código OTP para activar su cuenta
     */
    private function sendWelcomeOtp($email, $userId, $name = '')
    {
        $tokenModel = new \App\Models\LoginTokenModel();
        $code = $tokenModel->generateCode($userId);
        
        $emailService = \Config\Services::email();
        
        $subject = 'Activa tu cuenta - ' . $this->lpConfig->storeName;
        
        $message = view('emails/welcome_guest', [
            'code' => $code,
            'name' => $name,
            'storeName' => $this->lpConfig->storeName,
            'loginUrl' => base_url('/login')
        ]);
        
        $emailService->setTo($email);
        $emailService->setFrom($this->lpConfig->storeEmail, $this->lpConfig->storeName);
        $emailService->setSubject($subject);
        $emailService->setMessage($message);
        $emailService->setMailType('html');
        
        return $emailService->send();
    }
}