<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductImageModel extends Model
{
    protected $table = 'product_images';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'product_id', 'image_path', 'is_primary', 'sort_order'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Obtener imágenes de un producto
    public function getImagesByProduct($productId)
    {
        return $this->where('product_id', $productId)
                    ->orderBy('is_primary', 'DESC')
                    ->orderBy('sort_order', 'ASC')
                    ->findAll();
    }

    // Obtener imagen principal
    public function getPrimaryImage($productId)
    {
        return $this->where('product_id', $productId)
                    ->where('is_primary', true)
                    ->first();
    }

    // Establecer una imagen como principal (y desmarcar las demás)
    public function setPrimaryImage($productId, $imageId)
    {
        // Desmarcar todas las imágenes de este producto
        $this->where('product_id', $productId)
             ->set(['is_primary' => false])
             ->update();
        
        // Marcar la nueva imagen como principal
        return $this->update($imageId, ['is_primary' => true]);
    }
}