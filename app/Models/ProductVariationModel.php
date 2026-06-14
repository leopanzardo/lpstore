<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductVariationModel extends Model
{
    protected $table = 'product_variations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'product_id', 'attribute', 'value', 'price', 'stock', 'sku', 'is_active'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'product_id' => 'required|integer',
        'attribute' => 'required|max_length[191]',
        'value' => 'required|max_length[191]',
        'price' => 'permit_empty|numeric',
        'stock' => 'permit_empty|integer',
    ];

    // Obtener variaciones activas de un producto
    public function getVariationsByProduct($productId)
    {
        return $this->where('product_id', $productId)
                    ->where('is_active', true)
                    ->orderBy('attribute', 'ASC')
                    ->orderBy('value', 'ASC')
                    ->findAll();
    }

    // Agrupar variaciones por atributo (ej: talles, colores)
    public function getGroupedVariations($productId)
    {
        $variations = $this->getVariationsByProduct($productId);
        $grouped = [];
        
        foreach ($variations as $variation) {
            if (!isset($grouped[$variation->attribute])) {
                $grouped[$variation->attribute] = [];
            }
            $grouped[$variation->attribute][] = $variation;
        }
        
        return $grouped;
    }

    // Obtener variación por SKU
    public function getBySku($sku)
    {
        return $this->where('sku', $sku)->first();
    }
}