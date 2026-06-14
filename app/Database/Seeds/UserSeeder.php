<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Limpiar tablas relacionadas
        $this->db->table('users')->truncate();
        $this->db->table('user_addresses')->truncate();
        $this->db->table('favorites')->truncate();
        
        // Usuario de prueba
        $users = [
            [
                'email' => 'cliente@test.com',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'first_name' => 'Cliente',
                'last_name' => 'Prueba',
                'phone' => '099123456',
                'is_active' => true,
                'is_verified' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'email' => 'juan.perez@email.com',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'first_name' => 'Juan',
                'last_name' => 'Pérez',
                'phone' => '098765432',
                'is_active' => true,
                'is_verified' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'email' => 'maria.garcia@email.com',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'first_name' => 'María',
                'last_name' => 'García',
                'phone' => '097112233',
                'is_active' => true,
                'is_verified' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        $userIds = [];
        foreach ($users as $user) {
            $this->db->table('users')->insert($user);
            $userIds[] = $this->db->insertID();
        }
        
        echo "Usuarios insertados: " . count($users) . "\n";
        
        // Direcciones para el primer usuario (cliente@test.com)
        $addresses = [
            [
                'user_id' => $userIds[0],
                'address_line1' => 'Av. 18 de Julio 1234',
                'address_line2' => 'Apto 501',
                'city' => 'Montevideo',
                'state' => 'Montevideo',
                'postal_code' => '11200',
                'country' => 'Uruguay',
                'is_default' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'user_id' => $userIds[0],
                'address_line1' => 'Rambla Sur 567',
                'address_line2' => null,
                'city' => 'Punta del Este',
                'state' => 'Maldonado',
                'postal_code' => '20100',
                'country' => 'Uruguay',
                'is_default' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($addresses as $address) {
            $this->db->table('user_addresses')->insert($address);
        }
        
        echo "Direcciones insertadas: " . count($addresses) . "\n";
        
        // Favoritos para el primer usuario
        $productModel = new \App\Models\ProductModel();
        $products = $productModel->findAll();
        
        if (!empty($products)) {
            $favorites = [
                [
                    'user_id' => $userIds[0],
                    'product_id' => $products[0]->id, // iPhone
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => $userIds[0],
                    'product_id' => $products[2]->id, // MacBook
                    'created_at' => date('Y-m-d H:i:s')
                ],
                [
                    'user_id' => $userIds[0],
                    'product_id' => $products[4]->id, // Camisa
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            
            foreach ($favorites as $favorite) {
                // Verificar si ya existe para evitar duplicados
                $exists = $this->db->table('favorites')
                    ->where('user_id', $favorite['user_id'])
                    ->where('product_id', $favorite['product_id'])
                    ->get()
                    ->getRow();
                
                if (!$exists) {
                    $this->db->table('favorites')->insert($favorite);
                }
            }
            
            echo "Favoritos insertados: " . count($favorites) . "\n";
        }
    }
}