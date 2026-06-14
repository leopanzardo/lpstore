<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        // Limpiar tablas relacionadas
        $this->db->table('products')->truncate();
        $this->db->table('product_variations')->truncate();
        $this->db->table('product_images')->truncate();
        
        $products = [
            // Celulares
            [
                'category_id' => 6,
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'description' => 'El iPhone 15 Pro con cámara de 48 MP, chip A17 Pro y diseño en titanio.',
                'base_price' => 1299.00,
                'stock' => 10,
                'is_active' => true,
                'is_featured' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_id' => 6,
                'name' => 'Samsung Galaxy S24 Ultra',
                'slug' => 'samsung-galaxy-s24-ultra',
                'description' => 'El Galaxy S24 Ultra con pantalla AMOLED, S Pen y cámara de 200 MP.',
                'base_price' => 1199.00,
                'stock' => 15,
                'is_active' => true,
                'is_featured' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Computadoras
            [
                'category_id' => 7,
                'name' => 'MacBook Pro M3',
                'slug' => 'macbook-pro-m3',
                'description' => 'MacBook Pro con chip M3, 16GB RAM y 512GB SSD.',
                'base_price' => 1999.00,
                'stock' => 8,
                'is_active' => true,
                'is_featured' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_id' => 7,
                'name' => 'Monitor LG 27" 4K',
                'slug' => 'monitor-lg-27-4k',
                'description' => 'Monitor LG UltraFine 27 pulgadas 4K HDR10.',
                'base_price' => 399.00,
                'stock' => 20,
                'is_active' => true,
                'is_featured' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Ropa Hombre
            [
                'category_id' => 8,
                'name' => 'Camisa Oxford Hombre',
                'slug' => 'camisa-oxford-hombre',
                'description' => 'Camisa Oxford de algodón, disponible en varios colores.',
                'base_price' => 49.99,
                'stock' => 50,
                'is_active' => true,
                'is_featured' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_id' => 8,
                'name' => 'Jeans Slim Fit',
                'slug' => 'jeans-slim-fit',
                'description' => 'Jeans slim fit de mezclilla elástica.',
                'base_price' => 59.99,
                'stock' => 40,
                'is_active' => true,
                'is_featured' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Ropa Mujer
            [
                'category_id' => 9,
                'name' => 'Vestido Floral',
                'slug' => 'vestido-floral',
                'description' => 'Vestido largo estampado floral, ideal para primavera.',
                'base_price' => 79.99,
                'stock' => 30,
                'is_active' => true,
                'is_featured' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Muebles
            [
                'category_id' => 10,
                'name' => 'Sillón Relax',
                'slug' => 'sillon-relax',
                'description' => 'Sillón reclinable de cuero ecológico.',
                'base_price' => 299.99,
                'stock' => 12,
                'is_active' => true,
                'is_featured' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        $productIds = [];
        foreach ($products as $product) {
            $this->db->table('products')->insert($product);
            $productIds[] = $this->db->insertID();
        }
        
        echo "Productos insertados: " . count($products) . "\n";
        
        // Insertar variaciones para algunos productos
        $variations = [
            // iPhone 15 Pro (product_id 1)
            [
                'product_id' => $productIds[0],
                'attribute' => 'color',
                'value' => 'Titanio Negro',
                'price' => 0,
                'stock' => 5,
                'sku' => 'IP15PRO-BLK',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_id' => $productIds[0],
                'attribute' => 'color',
                'value' => 'Titanio Blanco',
                'price' => 0,
                'stock' => 3,
                'sku' => 'IP15PRO-WHT',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_id' => $productIds[0],
                'attribute' => 'almacenamiento',
                'value' => '256GB',
                'price' => 100,
                'stock' => 4,
                'sku' => 'IP15PRO-256',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Camisa Oxford (product_id 4)
            [
                'product_id' => $productIds[3],
                'attribute' => 'talle',
                'value' => 'S',
                'price' => 0,
                'stock' => 10,
                'sku' => 'CAM-S',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_id' => $productIds[3],
                'attribute' => 'talle',
                'value' => 'M',
                'price' => 0,
                'stock' => 15,
                'sku' => 'CAM-M',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_id' => $productIds[3],
                'attribute' => 'talle',
                'value' => 'L',
                'price' => 0,
                'stock' => 12,
                'sku' => 'CAM-L',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_id' => $productIds[3],
                'attribute' => 'color',
                'value' => 'Blanco',
                'price' => 0,
                'stock' => 20,
                'sku' => 'CAM-WHT',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_id' => $productIds[3],
                'attribute' => 'color',
                'value' => 'Azul',
                'price' => 0,
                'stock' => 18,
                'sku' => 'CAM-BLU',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($variations as $variation) {
            $this->db->table('product_variations')->insert($variation);
        }
        
        echo "Variaciones insertadas: " . count($variations) . "\n";
        
        // Insertar imágenes para algunos productos
        $images = [
            // iPhone 15 Pro
            [
                'product_id' => $productIds[0],
                'image_path' => 'uploads/products/iphone15pro-1.jpg',
                'is_primary' => true,
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'product_id' => $productIds[0],
                'image_path' => 'uploads/products/iphone15pro-2.jpg',
                'is_primary' => false,
                'sort_order' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // MacBook Pro
            [
                'product_id' => $productIds[2],
                'image_path' => 'uploads/products/macbookpro.jpg',
                'is_primary' => true,
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Camisa Oxford
            [
                'product_id' => $productIds[3],
                'image_path' => 'uploads/products/camisa-oxford.jpg',
                'is_primary' => true,
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($images as $image) {
            $this->db->table('product_images')->insert($image);
        }
        
        echo "Imágenes insertadas: " . count($images) . "\n";
    }
}