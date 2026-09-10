<?php
namespace App\Models;
use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'transaction_id';
    protected $allowedFields = ['invoice_number', 'user_id', 'customer_name', 'customer_phone', 'total_amount', 'payment_method', 'status'];
}