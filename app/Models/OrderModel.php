<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'order_number', 'status', 'total_amount', 
        'shipping_address', 'payment_method', 'payment_status', 
        'mercadopago_payment_id', 'notes'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Generar número de orden único
    public function generateOrderNumber()
    {
        $prefix = 'LP-' . date('Ymd');
        $lastOrder = $this->like('order_number', $prefix, 'after')
                          ->orderBy('id', 'DESC')
                          ->first();
        
        if ($lastOrder) {
            $lastNumber = intval(substr($lastOrder->order_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . '-' . $newNumber;
    }

    // Obtener pedidos de un usuario
    public function getOrdersByUser($userId, $limit = null)
    {
        $builder = $this->where('user_id', $userId)
                        ->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    // Obtener pedido con sus items
    public function getOrderWithItems($orderId)
    {
        $order = $this->find($orderId);
        
        if ($order) {
            $orderItemModel = new OrderItemModel();
            $order->items = $orderItemModel->getItemsByOrder($orderId);
        }
        
        return $order;
    }

    // Actualizar estado del pedido
    public function updateStatus($orderId, $status)
    {
        $allowedStatuses = ['En carrito', 'Cancelada', 'Confirmada', 'Lista para enviar', 'En tránsito', 'Entregada'];
        
        if (in_array($status, $allowedStatuses)) {
            return $this->update($orderId, ['status' => $status]);
        }
        
        return false;
    }
}