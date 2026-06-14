<?php

namespace App\Models;

use CodeIgniter\Model;

class FavoriteModel extends Model
{
    protected $table = 'favorites';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['user_id', 'product_id'];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';

    // Obtener favoritos de un usuario
    public function getFavoritesByUser($userId)
    {
        return $this->select('favorites.*, products.name, products.slug, products.base_price')
                    ->join('products', 'products.id = favorites.product_id')
                    ->where('favorites.user_id', $userId)
                    ->where('products.is_active', true)
                    ->orderBy('favorites.created_at', 'DESC')
                    ->findAll();
    }

    // Verificar si un producto está en favoritos
    public function isFavorite($userId, $productId)
    {
        return $this->where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->countAllResults() > 0;
    }

    // Agregar a favoritos
    public function addFavorite($userId, $productId)
    {
        if (!$this->isFavorite($userId, $productId)) {
            return $this->insert([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
        }
        return false;
    }

    // Remover de favoritos
    public function removeFavorite($userId, $productId)
    {
        return $this->where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->delete();
    }
}