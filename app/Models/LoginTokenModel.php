<?php

namespace App\Models;

use CodeIgniter\Model;

class LoginTokenModel extends Model
{
    protected $table = 'login_tokens';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['user_id', 'code', 'expires_at', 'used'];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';

    /**
     * Genera un código de 6 dígitos para un usuario
     */
    public function generateCode($userId)
    {
        // Limpiar códigos expirados (solo de este usuario para no afectar otros)
        $this->where('expires_at <', date('Y-m-d H:i:s'))
             ->where('user_id', $userId)
             ->delete();
        
        // Generar código de 6 dígitos
        $code = sprintf('%06d', mt_rand(0, 999999));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // Insertar el código
        $result = $this->insert([
            'user_id' => $userId,
            'code' => $code,
            'expires_at' => $expiresAt,
            'used' => 0
        ]);
        
        if (!$result) {
            log_message('error', 'LoginTokenModel: Error al insertar código - ' . print_r($this->errors(), true));
            return false;
        }
        
        return $code;
    }

    /**
     * Valida un código y retorna el user_id si es válido
     */
    public function validateCode($code)
    {
        $token = $this->where('code', $code)
                      ->where('used', false)
                      ->where('expires_at >', date('Y-m-d H:i:s'))
                      ->first();
        
        if ($token) {
            // Marcar como usado
            $this->update($token->id, ['used' => true]);
            return $token->user_id;
        }
        
        return null;
    }
}