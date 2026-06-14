<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserAddressModel;
use App\Models\OrderModel;
use App\Models\FavoriteModel;
use App\Models\ProductModel;
use App\Models\LoginTokenModel;

class Auth extends BaseController
{
    protected $userModel;
    protected $addressModel;
    protected $orderModel;
    protected $favoriteModel;
    protected $productModel;
    protected $tokenModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->addressModel = new UserAddressModel();
        $this->orderModel = new OrderModel();
        $this->favoriteModel = new FavoriteModel();
        $this->productModel = new ProductModel();
        $this->tokenModel = new LoginTokenModel();
    }
    
    // ============================ LOGIN CON OTP ============================
    
    public function login()
    {
        if (session('isLoggedIn')) {
            return redirect()->to('/perfil');
        }
        
        $viewData = array_merge($this->getBaseData(), [
            'title' => 'Iniciar Sesión'
        ]);
        
        return view('auth/login', $viewData);
    }
    
    public function sendOtp()
    {
        $email = $this->request->getPost('email');
        
        if (!$email) {
            return redirect()->back()->with('error', 'Ingresa tu email');
        }
        
        // Buscar o crear usuario (invitado/inactivo)
        $user = $this->userModel->where('email', $email)->first();
        
        if (!$user) {
            // Crear usuario inactivo sin contraseña
            $userId = $this->userModel->createGuestUser($email);
            $user = $this->userModel->find($userId);
        }
        
        // Generar código OTP de 6 dígitos
        $code = $this->tokenModel->generateCode($user->id);
        
        // Enviar email con el código
        $this->sendOtpEmail($email, $code, $user->first_name);
        
        // Guardar email en sesión para el siguiente paso
        session()->set('otp_email', $email);
        
        return redirect()->to('/auth/verify')->with('success', 'Te hemos enviado un código de 6 dígitos a tu email');
    }
    
    public function verifyOtpForm()
    {
        if (is_null(session('otp_email'))) {
            return redirect()->to('/login');
        }
        
        $viewData = array_merge($this->getBaseData(), [
            'title' => 'Verificar código'
        ]);
        
        return view('auth/otp_verify', $viewData);
    }
    
    public function verifyOtp()
    {
        $code = $this->request->getPost('code');
        $email = session('otp_email');
        
        if (!$code || !$email) {
            return redirect()->to('/login')->with('error', 'Datos inválidos');
        }
        
        $user = $this->userModel->where('email', $email)->first();
        
        if (!$user) {
            return redirect()->to('/login')->with('error', 'Usuario no encontrado');
        }
        
        $userId = $this->tokenModel->validateCode($code);
        
        if ($userId && $userId == $user->id) {
            // Activar usuario si era invitado/inactivo
            if (!$user->is_active) {
                $this->userModel->activateUser($user->id);
            }
            
            // Iniciar sesión
            session()->set([
                'isLoggedIn' => true,
                'userId' => $user->id,
                'userEmail' => $user->email,
                'userName' => $user->first_name . ' ' . $user->last_name,
                'userFirstName' => $user->first_name,
                'userLastName' => $user->last_name
            ]);
            
            // Limpiar sesión OTP
            session()->remove('otp_email');
            
            // Redirigir a la página que intentaba o al perfil
            $redirectTo = session('redirect_after_login') ?? '/perfil';
            session()->remove('redirect_after_login');
            
            return redirect()->to($redirectTo)->with('success', 'Has iniciado sesión correctamente');
        }
        
        return redirect()->back()->with('error', 'Código inválido o expirado');
    }
    
    // ============================ REGISTRO ============================
    
    public function register()
    {
        if (session('isLoggedIn')) {
            return redirect()->to('/perfil');
        }
        
        $viewData = array_merge($this->getBaseData(), [
            'title' => 'Registrarse'
        ]);
        
        return view('auth/register', $viewData);
    }
    
    public function attemptRegister()
    {
        $rules = [
            'email' => 'required|valid_email|is_unique[users.email]',
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'phone' => 'permit_empty'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Crear usuario inactivo (sin contraseña, se activará con OTP)
        $data = [
            'email' => $this->request->getPost('email'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'is_active' => false,
            'is_verified' => false
        ];
        
        $userId = $this->userModel->insert($data);
        
        if ($userId) {
            // Generar y enviar código OTP
            $code = $this->tokenModel->generateCode($userId);
            $this->sendOtpEmail($data['email'], $code, $data['first_name']);
            
            // Guardar email en sesión para verificar OTP
            session()->set('otp_email', $data['email']);
            
            return redirect()->to('/auth/verify')->with('success', 'Cuenta creada. Te hemos enviado un código de verificación a tu email.');
        }
        
        return redirect()->back()->with('error', 'Error al registrar. Intenta nuevamente.');
    }
    
    // === OTROS MÉTODOS (perfil, pedidos, favoritos, direcciones, etc.) ===
    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/')->with('success', 'Has cerrado sesión correctamente.');
    }
    
    public function profile()
    {
        if (!session('isLoggedIn')) {
            return redirect()->to('/login');
        }
        
        $userId = session('userId');
        
        $viewData = array_merge($this->getBaseData(), [
            'title' => 'Mi Perfil',
            'user' => $this->userModel->find($userId),
            'addresses' => $this->addressModel->getAddressesByUser($userId),
            'defaultAddress' => $this->addressModel->getDefaultAddress($userId)
        ]);
        
        return view('auth/profile', $viewData);
    }
    
    public function updateProfile()
    {
        if (!session('isLoggedIn')) {
            return redirect()->to('/login');
        }
        
        $userId = session('userId');
        
        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'phone' => 'permit_empty'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone')
        ];
        
        if ($this->userModel->update($userId, $data)) {
            session()->set([
                'userName' => $data['first_name'] . ' ' . $data['last_name'],
                'userFirstName' => $data['first_name'],
                'userLastName' => $data['last_name']
            ]);
            
            return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
        }
        
        return redirect()->back()->with('error', 'Error al actualizar el perfil.');
    }
    
    public function orders()
    {
        if (!session('isLoggedIn')) {
            return redirect()->to('/login');
        }
        
        $userId = session('userId');
        
        $viewData = array_merge($this->getBaseData(), [
            'title' => 'Mis Pedidos',
            'orders' => $this->orderModel->getOrdersByUser($userId)
        ]);
        
        return view('auth/orders', $viewData);
    }
    
    public function orderDetail($orderId)
    {
        if (!session('isLoggedIn')) {
            return redirect()->to('/login');
        }
        
        $order = $this->orderModel->getOrderWithItems($orderId);
        
        if ($order->user_id != session('userId')) {
            return redirect()->to('/mis-pedidos')->with('error', 'Pedido no encontrado.');
        }
        
        $viewData = array_merge($this->getBaseData(), [
            'title' => 'Detalle del Pedido #' . $order->order_number,
            'order' => $order
        ]);
        
        return view('auth/order_detail', $viewData);
    }
    
    public function favorites()
    {
        if (!session('isLoggedIn')) {
            return redirect()->to('/login');
        }
        
        $userId = session('userId');
        
        $viewData = array_merge($this->getBaseData(), [
            'title' => 'Mis Favoritos',
            'favorites' => $this->favoriteModel->getFavoritesByUser($userId)
        ]);
        
        return view('auth/favorites', $viewData);
    }
    
    public function addFavorite()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Debes iniciar sesión']);
        }
        
        $productId = $this->request->getPost('product_id');
        $userId = session('userId');
        
        if ($this->favoriteModel->addFavorite($userId, $productId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Agregado a favoritos']);
        }
        
        return $this->response->setJSON(['success' => false, 'message' => 'Ya está en favoritos']);
    }
    
    public function removeFavorite()
    {
        if (!session('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Debes iniciar sesión']);
        }
        
        $productId = $this->request->getPost('product_id');
        $userId = session('userId');
        
        if ($this->favoriteModel->removeFavorite($userId, $productId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Removido de favoritos']);
        }
        
        return $this->response->setJSON(['success' => false, 'message' => 'Error al remover']);
    }

    public function addresses()
    {
        if (!session('isLoggedIn')) {
            return redirect()->to('/login');
        }
        
        $userId = session('userId');
        
        $viewData = array_merge($this->getBaseData(), [
            'title' => 'Mis Direcciones',
            'addresses' => $this->addressModel->getAddressesByUser($userId),
            'defaultAddress' => $this->addressModel->getDefaultAddress($userId)
        ]);
        
        return view('auth/addresses', $viewData);
    }
    
    public function addAddress()
    {
        if (!session('isLoggedIn')) {
            return redirect()->to('/login');
        }
        
        $rules = [
            'address_line1' => 'required',
            'city' => 'required',
            'country' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'user_id' => session('userId'),
            'address_line1' => $this->request->getPost('address_line1'),
            'address_line2' => $this->request->getPost('address_line2'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'postal_code' => $this->request->getPost('postal_code'),
            'country' => $this->request->getPost('country'),
            'is_default' => $this->request->getPost('is_default') ? true : false
        ];
        
        if ($this->addressModel->insert($data)) {
            if ($data['is_default']) {
                $this->addressModel->setDefaultAddress($data['user_id'], $this->addressModel->getInsertID());
            }
            
            return redirect()->back()->with('success', 'Dirección agregada correctamente.');
        }
        
        return redirect()->back()->with('error', 'Error al agregar la dirección.');
    }

    public function updateAddress($addressId)
    {
        if (!session('isLoggedIn')) {
            return redirect()->to('/login');
        }
        
        $address = $this->addressModel->find($addressId);
        
        if (!$address || $address->user_id != session('userId')) {
            return redirect()->back()->with('error', 'Dirección no encontrada');
        }
        
        $rules = [
            'address_line1' => 'required',
            'city' => 'required',
            'country' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'address_line1' => $this->request->getPost('address_line1'),
            'address_line2' => $this->request->getPost('address_line2'),
            'city' => $this->request->getPost('city'),
            'state' => $this->request->getPost('state'),
            'postal_code' => $this->request->getPost('postal_code'),
            'country' => $this->request->getPost('country')
        ];
        
        if ($this->addressModel->update($addressId, $data)) {
            return redirect()->back()->with('success', 'Dirección actualizada correctamente.');
        }
        
        return redirect()->back()->with('error', 'Error al actualizar la dirección.');
    }
    
    public function deleteAddress($addressId)
    {
        if (!session('isLoggedIn')) {
            return redirect()->to('/login');
        }
        
        $address = $this->addressModel->find($addressId);
        
        if ($address && $address->user_id == session('userId')) {
            $this->addressModel->delete($addressId);
            return redirect()->back()->with('success', 'Dirección eliminada.');
        }
        
        return redirect()->back()->with('error', 'Dirección no encontrada.');
    }

    public function setDefaultAddress($addressId)
    {
        if (!session('isLoggedIn')) {
            return redirect()->to('/login');
        }
        
        $address = $this->addressModel->find($addressId);
        
        // Verificar que la dirección pertenece al usuario
        if (!$address || $address->user_id != session('userId')) {
            return redirect()->back()->with('error', 'Dirección no encontrada');
        }
        
        if ($this->addressModel->setDefaultAddress(session('userId'), $addressId)) {
            return redirect()->back()->with('success', 'Dirección principal actualizada.');
        }
        
        return redirect()->back()->with('error', 'Error al actualizar la dirección principal.');
    }
    
    // ============================ MÉTODOS PRIVADOS ============================
    
    private function sendOtpEmail($email, $code, $name = '')
    {
        $emailService = service('email');

        $mailConfig['protocol'] = 'smtp';
        $mailConfig['SMTPHost'] = getenv('email.SMTPHost');
        $mailConfig['SMTPUser'] = getenv('email.SMTPUser');
        $mailConfig['SMTPPass'] = getenv('email.SMTPPass');
        $mailConfig['SMTPPort'] = (int) getenv('email.SMTPPort');
        $mailConfig['SMTPCrypto'] = getenv('email.SMTPCrypto');

        $emailService->initialize($mailConfig);
        
        $subject = 'Tu código de acceso - ' . $this->lpConfig->storeName;
        
        $message = view('emails/otp_code', [
            'code' => $code,
            'name' => $name,
            'storeName' => $this->lpConfig->storeName
        ]);
        
        $emailService->setTo($email);
        $emailService->setFrom($this->lpConfig->storeEmail, $this->lpConfig->storeName);
        $emailService->setSubject($subject);
        $emailService->setMessage($message);
        $emailService->setMailType('html');
        
        return $emailService->send();
    }
}