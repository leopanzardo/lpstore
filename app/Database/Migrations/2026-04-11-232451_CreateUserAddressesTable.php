<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserAddressesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'address_line1' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
                'null' => false,
            ],
            'address_line2' => [
                'type' => 'VARCHAR',
                'constraint' => 191,
                'null' => true,
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'state' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'postal_code' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'Uruguay',
            ],
            'is_default' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_addresses');
    }

    public function down()
    {
        $this->forge->dropTable('user_addresses');
    }
}
