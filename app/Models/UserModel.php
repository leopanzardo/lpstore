<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'email', 'first_name', 'last_name', 'phone',
        'is_active', 'is_verified'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
    ];

    /**
     * Obtener usuario por email
     */
    public function getByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Crea un usuario invitado (sin contraseña, inactivo)
     */
    public function createGuestUser($email, $firstName = '', $lastName = '', $phone = '')
    {
        // Verificar si ya existe
        $existing = $this->where('email', $email)->first();
        if ($existing) {
            return $existing->id;
        }
        
        return $this->insert([
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'is_active' => false,
            'is_verified' => false
        ]);
    }

    /**
     * Activa un usuario (después de verificar email con OTP)
     */
    public function activateUser($userId)
    {
        return $this->update($userId, [
            'is_active' => true,
            'is_verified' => true
        ]);
    }
}