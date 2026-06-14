<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAddressModel extends Model
{
    protected $table = 'user_addresses';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id', 'address_line1', 'address_line2', 'city', 
        'state', 'postal_code', 'country', 'is_default'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Obtener direcciones de un usuario
    public function getAddressesByUser($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('is_default', 'DESC')
                    ->findAll();
    }

    // Obtener dirección principal
    public function getDefaultAddress($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_default', true)
                    ->first();
    }

    // Establecer dirección como principal
    public function setDefaultAddress($userId, $addressId)
    {
        // Desmarcar todas las direcciones de este usuario
        $this->where('user_id', $userId)
             ->set(['is_default' => false])
             ->update();
        
        // Marcar la nueva dirección como principal
        return $this->update($addressId, ['is_default' => true]);
    }
}