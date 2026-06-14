<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\ProductVariationModel;

class Cart extends BaseController
{
    protected $productModel;
    protected $variationModel;
    
    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->variationModel = new ProductVariationModel();
        
        if (!session()->has('cart')) {
            session()->set('cart', []);
            session()->set('cartCount', 0);
            session()->set('cartTotal', 0);
        }
    }
    
    public function index()
    {
        $cart = session('cart');
        $cartItems = [];
        $total = 0;
        
        if (!empty($cart)) {
            foreach ($cart as $item) {
                $product = $this->productModel->find($item['product_id']);
                if ($product) {
                    $variation = null;
                    if (isset($item['variation_id']) && $item['variation_id']) {
                        $variation = $this->variationModel->find($item['variation_id']);
                    }
                    
                    $itemTotal = $item['price'] * $item['quantity'];
                    $total += $itemTotal;
                    
                    $cartItems[] = [
                        'id' => $item['id'],
                        'product_id' => $item['product_id'],
                        'product_name' => $product->name,
                        'product_slug' => $product->slug,
                        'variation_id' => $item['variation_id'] ?? null,
                        'variation_name' => $variation ? $variation->attribute . ': ' . $variation->value : null,
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $itemTotal
                    ];
                }
            }
        }
        
        $viewData = array_merge($this->getBaseData(), [
            'title' => 'Mi Carrito',
            'cartItems' => $cartItems,
            'total' => $total
        ]);
        
        return view('cart/index', $viewData);
    }
    
    public function add()
    {
        $productId = $this->request->getPost('product_id');
        $quantity = max(1, (int) $this->request->getPost('quantity'));
        $variationId = $this->request->getPost('variation_id');
        
        $product = $this->productModel->find($productId);
        if (!$product || !$product->is_active) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Producto no disponible'
            ]);
        }
        
        $price = $product->base_price;
        $variation = null;
        
        if ($variationId) {
            $variation = $this->variationModel->find($variationId);
            if ($variation && $variation->is_active) {
                $price = $variation->price;
                if ($variation->stock > 0 && $quantity > $variation->stock) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'No hay suficiente stock de esta variación'
                    ]);
                }
            }
        } else {
            if ($product->stock > 0 && $quantity > $product->stock) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No hay suficiente stock'
                ]);
            }
        }
        
        $cartItemId = $productId;
        if ($variationId) {
            $cartItemId .= '_' . $variationId;
        }
        
        $cart = session('cart');
        
        if (isset($cart[$cartItemId])) {
            $cart[$cartItemId]['quantity'] += $quantity;
        } else {
            $cart[$cartItemId] = [
                'id' => $cartItemId,
                'product_id' => $productId,
                'variation_id' => $variationId,
                'quantity' => $quantity,
                'price' => $price
            ];
        }
        
        session()->set('cart', $cart);
        $this->updateCartTotals();
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'cartCount' => session('cartCount')
        ]);
    }
    
    public function update()
    {
        log_message('info', print_r($_POST, true));
        $itemId = $this->request->getPost('item_id');
        log_message('info', 'Item ID: ' . $itemId);
        $quantity = (int)$this->request->getPost('quantity');
        log_message('info', 'Quantity: ' . $quantity);
        
        if ($quantity <= 0) {
            return $this->remove();
        }
        
        $cart = session('cart');
        log_message('info', 'Contenido del carrito: ' . print_r($cart, true));
        
        if (isset($cart[$itemId])) {
            $cart[$itemId]['quantity'] = $quantity;
            session()->set('cart', $cart);
            $this->updateCartTotals();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Carrito actualizado',
                'cartCount' => session('cartCount'),
                'cartTotal' => session('cartTotal')
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Item no encontrado'
        ]);
    }
    
    public function remove()
    {
        $itemId = $this->request->getPost('item_id');
        
        $cart = session('cart');
        
        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            session()->set('cart', $cart);
            $this->updateCartTotals();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Producto eliminado del carrito',
                'cartCount' => session('cartCount'),
                'cartTotal' => session('cartTotal')
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Item no encontrado'
        ]);
    }
    
    public function clear()
    {
        session()->set('cart', []);
        $this->updateCartTotals();
        
        return redirect()->to('/carrito')->with('success', 'Carrito vaciado correctamente');
    }
    
    public function count()
    {
        return $this->response->setJSON([
            'count' => session('cartCount')
        ]);
    }
    
    private function updateCartTotals()
    {
        $cart = session('cart');
        $count = 0;
        $total = 0;
        
        foreach ($cart as $item) {
            $count += $item['quantity'];
            $total += $item['price'] * $item['quantity'];
        }
        
        session()->set('cartCount', $count);
        session()->set('cartTotal', $total);
    }
}