<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'order_id', 'product_id', 'variation_id', 'quantity', 
        'unit_price', 'subtotal', 'product_name_snapshot', 'variation_snapshot'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = ''; // No necesitamos updated_at para items

    // Obtener items de un pedido
    public function getItemsByOrder($orderId)
    {
        return $this->where('order_id', $orderId)
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    // Calcular total de un pedido (suma de subtotales)
    public function calculateOrderTotal($orderId)
    {
        $result = $this->select('SUM(subtotal) as total')
                       ->where('order_id', $orderId)
                       ->first();
        
        return $result ? $result->total : 0;
    }
}