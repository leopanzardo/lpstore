<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'category_id', 'name', 'slug', 'description', 
        'base_price', 'stock', 'is_active', 'is_featured'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[191]',
        'slug' => 'required|is_unique[products.slug,id,{id}]',
        'base_price' => 'permit_empty|numeric',
        'stock' => 'permit_empty|integer',
    ];

    // Obtener productos activos con su categoría
    public function getActiveProductsWithCategory($limit = null, $offset = 0)
    {
        $builder = $this->select('products.*, categories.name as category_name, categories.slug as category_slug')
                         ->join('categories', 'categories.id = products.category_id', 'left')
                         ->where('products.is_active', true)
                         ->orderBy('products.created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->findAll();
    }

    // Obtener productos destacados
    public function getFeaturedProducts($limit = 8)
    {
        return $this->select('products.*, categories.name as category_name')
                    ->join('categories', 'categories.id = products.category_id', 'left')
                    ->where('products.is_active', true)
                    ->where('products.is_featured', true)
                    ->orderBy('products.created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    // Obtener productos por categoría
    public function getProductsByCategory($categoryId, $limit = null, $offset = 0)
    {
        $builder = $this->where('category_id', $categoryId)
                        ->where('is_active', true)
                        ->orderBy('name', 'ASC');
        
        if ($limit) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->findAll();
    }

    // Obtener producto por slug
    public function getBySlug($slug)
    {
        return $this->select('products.*, categories.name as category_name, categories.slug as category_slug')
                    ->join('categories', 'categories.id = products.category_id', 'left')
                    ->where('products.slug', $slug)
                    ->where('products.is_active', true)
                    ->first();
    }

    // Buscar productos
    public function search($term, $limit = null)
    {
        $builder = $this->select('products.*, categories.name as category_name')
                        ->join('categories', 'categories.id = products.category_id', 'left')
                        ->where('products.is_active', true)
                        ->groupStart()
                            ->like('products.name', $term)
                            ->orLike('products.description', $term)
                        ->groupEnd()
                        ->orderBy('products.name', 'ASC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    // Obtener productos relacionados (misma categoría)
    public function getRelatedProducts($productId, $categoryId, $limit = 4)
    {
        return $this->where('category_id', $categoryId)
                    ->where('id !=', $productId)
                    ->where('is_active', true)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}