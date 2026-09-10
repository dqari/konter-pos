<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTransactionCostsAndAuditLogs extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('status', 'transactions')) {
            $this->forge->addColumn('transactions', [
                'status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'completed',
                    'after'      => 'payment_method',
                ],
            ]);
        }

        if (! $this->db->fieldExists('purchase_cost', 'transaction_details')) {
            $this->forge->addColumn('transaction_details', [
                'purchase_cost' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                    'default'    => 0,
                    'after'      => 'price_sold',
                ],
            ]);
        }

        $this->forge->addField([
            'audit_id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => true,
            ],
            'action' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
            ],
            'reference_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
            ],
            'reference_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'details' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('audit_id', true);
        $this->forge->addKey(['user_id', 'created_at']);
        $this->forge->createTable('audit_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('audit_logs', true);
        if ($this->db->fieldExists('purchase_cost', 'transaction_details')) {
            $this->forge->dropColumn('transaction_details', 'purchase_cost');
        }
    }
}
