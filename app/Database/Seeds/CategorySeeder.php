<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // Limpiar tabla antes de insertar (opcional)
        $this->db->table('categories')->truncate();
        
        // Categorías principales
        $categories = [
            [
                'name' => 'Electrónica',
                'slug' => 'electronica',
                'parent_id' => null,
                'description' => 'Productos electrónicos, celulares, computadoras y más',
                'image' => 'electronica.jpg',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Ropa y Accesorios',
                'slug' => 'ropa-accesorios',
                'parent_id' => null,
                'description' => 'Indumentaria para toda la familia, calzado y accesorios',
                'image' => 'ropa.jpg',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Hogar y Decoración',
                'slug' => 'hogar-decoracion',
                'parent_id' => null,
                'description' => 'Muebles, decoración, textiles y artículos para el hogar',
                'image' => 'hogar.jpg',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Deportes y Aire Libre',
                'slug' => 'deportes-aire-libre',
                'parent_id' => null,
                'description' => 'Equipamiento deportivo, camping, pesca y más',
                'image' => 'deportes.jpg',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Libros y Música',
                'slug' => 'libros-musica',
                'parent_id' => null,
                'description' => 'Libros, discos, instrumentos musicales',
                'image' => 'libros.jpg',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Celulares',
                'slug' => 'celulares',
                'parent_id' => 1, // Electrónica
                'description' => 'Smartphones, accesorios y repuestos',
                'image' => 'celulares.jpg',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Computadoras',
                'slug' => 'computadoras',
                'parent_id' => 1, // Electrónica
                'description' => 'Laptops, PCs, monitores y periféricos',
                'image' => 'computadoras.jpg',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Ropa Hombre',
                'slug' => 'ropa-hombre',
                'parent_id' => 2, // Ropa y Accesorios
                'description' => 'Camisas, pantalones, chaquetas y más para caballero',
                'image' => 'ropa-hombre.jpg',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Ropa Mujer',
                'slug' => 'ropa-mujer',
                'parent_id' => 2, // Ropa y Accesorios
                'description' => 'Vestidos, blusas, faldas y más para dama',
                'image' => 'ropa-mujer.jpg',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Muebles',
                'slug' => 'muebles',
                'parent_id' => 3, // Hogar y Decoración
                'description' => 'Sillones, mesas, camas y armarios',
                'image' => 'muebles.jpg',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        
        foreach ($categories as $category) {
            $this->db->table('categories')->insert($category);
        }
        
        echo "Categorías insertadas: " . count($categories) . "\n";
    }
}