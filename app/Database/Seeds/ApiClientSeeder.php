<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ApiClientSeeder extends Seeder
{
    public function run()
    {
        // Limpiar tablas
        $this->db->table('api_clients')->truncate();
        $this->db->table('api_keys')->truncate();
        
        // Cliente de API para la aplicación central
        $clients = [
            [
                'name' => 'LP Central',
                'description' => 'Aplicación central de administración de LP Store',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Cliente de Prueba',
                'description' => 'Cliente para pruebas de desarrollo',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        $clientIds = [];
        foreach ($clients as $client) {
            $this->db->table('api_clients')->insert($client);
            $clientIds[] = $this->db->insertID();
        }
        
        echo "API Clients insertados: " . count($clients) . "\n";
        
        // API Keys
        $keys = [
            [
                'client_id' => $clientIds[0],
                'key' => 'lp_central_' . bin2hex(random_bytes(16)),
                'is_active' => true,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+1 year')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'client_id' => $clientIds[1],
                'key' => 'test_' . bin2hex(random_bytes(16)),
                'is_active' => true,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+30 days')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($keys as $key) {
            $this->db->table('api_keys')->insert($key);
        }
        
        echo "API Keys insertadas: " . count($keys) . "\n";
        
        // Mostrar las API Keys generadas para referencia
        echo "\n--- API Keys generadas ---\n";
        $generatedKeys = $this->db->table('api_keys')->get()->getResult();
        foreach ($generatedKeys as $key) {
            $client = $this->db->table('api_clients')->where('id', $key->client_id)->get()->getRow();
            echo "Cliente: {$client->name}\n";
            echo "API Key: {$key->key}\n";
            echo "Expira: {$key->expires_at}\n";
            echo "------------------------\n";
        }
    }
}