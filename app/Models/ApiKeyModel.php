<?php

namespace App\Models;

use CodeIgniter\Model;

class ApiKeyModel extends Model
{
    protected $table = 'api_keys';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'client_id', 'key', 'is_active', 'last_used_at', 'expires_at'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Generar una nueva API Key
    public function generateKey($clientId, $expiresInDays = null)
    {
        $key = bin2hex(random_bytes(32)); // 64 caracteres hexadecimales
        
        $data = [
            'client_id' => $clientId,
            'key' => $key,
            'is_active' => true
        ];
        
        if ($expiresInDays) {
            $data['expires_at'] = date('Y-m-d H:i:s', strtotime("+{$expiresInDays} days"));
        }
        
        $this->insert($data);
        
        return $key;
    }

    // Validar una API Key
    public function validateKey($key)
    {
        $apiKey = $this->where('key', $key)
                       ->where('is_active', true)
                       ->first();
        
        if (!$apiKey) {
            return false;
        }
        
        // Verificar si expiró
        if ($apiKey->expires_at && strtotime($apiKey->expires_at) < time()) {
            return false;
        }
        
        // Actualizar última fecha de uso
        $this->update($apiKey->id, ['last_used_at' => date('Y-m-d H:i:s')]);
        
        return $apiKey;
    }

    // Revocar una API Key
    public function revokeKey($keyId)
    {
        return $this->update($keyId, ['is_active' => false]);
    }
}