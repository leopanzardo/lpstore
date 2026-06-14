<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;

class Shop extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    
    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }
    
    public function index()
    {
        $viewData = array_merge($this->getBaseData(), [
            'title' => 'Bienvenido',
            'featuredProducts' => $this->productModel->getFeaturedProducts(6),
            'latestProducts' => $this->productModel->getActiveProductsWithCategory(12)
        ]);
        
        return view('shop/index', $viewData);
    }
    
    public function category($slug)
    {
        if ($slug === 'todas') {
            $category = (object) [
                'name' => 'Todos los productos',
                'slug' => 'todas',
                'description' => 'Todos los productos disponibles en nuestra tienda'
            ];
            $products = $this->productModel->getActiveProductsWithCategory(12);
        } else {
            $category = $this->categoryModel->getBySlug($slug);
            $products = $this->productModel->getProductsByCategory($category->id);
        }
        
        if (!$category) {
            return redirect()->to('/')->with('error', 'Categoría no encontrada');
        }
        
        $viewData = array_merge($this->getBaseData(), [
            'title' => $category->name,
            'category' => $category,
            'products' => $products
        ]);
        
        return view('shop/category', $viewData);
    }
    
    public function product($slug)
    {
        $product = $this->productModel->getBySlug($slug);
        
        if (!$product) {
            return redirect()->to('/')->with('error', 'Producto no encontrado');
        }
        
        $imageModel = new \App\Models\ProductImageModel();
        $variationModel = new \App\Models\ProductVariationModel();
        
        $viewData = array_merge($this->getBaseData(), [
            'title' => $product->name,
            'product' => $product,
            'images' => $imageModel->getImagesByProduct($product->id),
            'variations' => $variationModel->getGroupedVariations($product->id),
            'relatedProducts' => $this->productModel->getRelatedProducts($product->id, $product->category_id)
        ]);
        
        return view('shop/product', $viewData);
    }
    
    public function search()
    {
        $term = $this->request->getGet('q');
        
        if (!$term) {
            return redirect()->to('/');
        }
        
        $viewData = array_merge($this->getBaseData(), [
            'title' => 'Búsqueda: ' . esc($term),
            'searchTerm' => $term,
            'products' => $this->productModel->search($term)
        ]);
        
        return view('shop/search', $viewData);
    }

}