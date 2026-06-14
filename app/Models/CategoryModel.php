<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name', 'slug', 'parent_id', 'description', 'image', 'is_active'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[191]',
        'slug' => 'required|is_unique[categories.slug,id,{id}]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    // Obtener categorías activas para el frontend
    public function getActiveCategories()
    {
        return $this->where('is_active', true)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    // Obtener categorías con su jerarquía (para menús)
    public function getNestedCategories($parentId = null)
    {
        $categories = $this->where('parent_id', $parentId)
                           ->where('is_active', true)
                           ->orderBy('name', 'ASC')
                           ->findAll();
        
        foreach ($categories as $category) {
            $category->children = $this->getNestedCategories($category->id);
        }
        
        return $categories;
    }

    // Obtener categoría por slug (para URLs amigables)
    public function getBySlug($slug)
    {
        return $this->where('slug', $slug)
                    ->where('is_active', true)
                    ->first();
    }

    // Obtener todas las subcategorías de una categoría
    public function getSubcategories($categoryId)
    {
        return $this->where('parent_id', $categoryId)
                    ->where('is_active', true)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }
}