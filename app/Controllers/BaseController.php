<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class BaseController extends Controller
{
    
    protected $lpConfig;
    protected $categories;
    protected $mainBranch;
    
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        // Cargar configuración de LP Store
        $this->lpConfig = config('LpStore');
        
        // Cargar categorías con cache
        $cache = \Config\Services::cache();
        $this->categories = $cache->get('active_categories');
        if ($this->categories === null) {
            $categoryModel = new \App\Models\CategoryModel();
            $this->categories = $categoryModel->getActiveCategories();
            $cache->save('active_categories', $this->categories, 3600);
        }
        
        // Obtener sucursal principal (de la forma más simple)
        $this->mainBranch = null;
        if ($this->lpConfig->branches) {
            foreach ($this->lpConfig->branches as $branch) {
                if (isset($branch['isMain']) && $branch['isMain'] === true) {
                    $this->mainBranch = $branch;
                    break;
                }
            }
        }
    }
    
    /**
     * Obtiene la configuración completa de LP Store
     * @return \Config\LpStore
     */
    protected function getLpConfig()
    {
        return $this->lpConfig;
    }
    
    /**
     * Obtiene las categorías activas
     * @return array
     */
    protected function getCategories()
    {
        return $this->categories;
    }
    
    /**
     * Obtiene la sucursal principal
     * @return array|null
     */
    protected function getMainBranch()
    {
        return $this->mainBranch;
    }
    
    /**
     * Obtiene todos los datos base necesarios para las vistas
     * @return array
     */
    protected function getBaseData(): array
    {
        return [
            'lpConfig' => $this->lpConfig,
            'categories' => $this->categories,
            'mainBranch' => $this->mainBranch
        ];
    }
    
    /**
     * Obtiene el carrito (si existe)
     * @return array
     */
    protected function getCartData()
    {
        if (!session()->has('cart')) {
            session()->set('cart', []);
            session()->set('cartCount', 0);
            session()->set('cartTotal', 0);
        }
        
        return [
            'cart' => session('cart'),
            'cartCount' => session('cartCount'),
            'cartTotal' => session('cartTotal')
        ];
    }
}