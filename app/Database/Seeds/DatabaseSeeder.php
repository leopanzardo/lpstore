<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Ejecutar seeds en orden (por dependencias)
        $this->call('CategorySeeder');
        $this->call('ProductSeeder');
        $this->call('UserSeeder');
        $this->call('ApiClientSeeder');
    }
}