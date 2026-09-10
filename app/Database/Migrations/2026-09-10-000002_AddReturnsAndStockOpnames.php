<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddReturnsAndStockOpnames extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('return_qty', 'transaction_details')) {
            $this->forge->addColumn('transaction_details', [
                'return_qty' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'default'    => 0,
                    'after'      => 'qty',
                ],
                'return_reason' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'purchase_cost',
                ],
                'returned_at' => [
                    'type'   => 'DATETIME',
                    'null'   => true,
                    'after'  => 'return_reason',
                ],
            ]);
        }

        $this->forge->addField([
            'opname_id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'opname_date' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'completed',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('opname_id', true);
        $this->forge->addKey(['user_id', 'opname_date']);
        $this->forge->createTable('stock_opnames', true);

        $this->forge->addField([
            'opname_item_id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'opname_id' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
            ],
            'product_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'system_qty' => [
                'type' => 'INT',
            ],
            'physical_qty' => [
                'type' => 'INT',
            ],
            'difference' => [
                'type' => 'INT',
            ],
            'notes' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('opname_item_id', true);
        $this->forge->addKey(['opname_id', 'product_id']);
        $this->forge->createTable('stock_opname_items', true);
    }

    public function down()
    {
        $this->forge->dropTable('stock_opname_items', true);
        $this->forge->dropTable('stock_opnames', true);
        if ($this->db->fieldExists('return_qty', 'transaction_details')) {
            $this->forge->dropColumn('transaction_details', ['return_qty', 'return_reason', 'returned_at']);
        }
    }
}
