<?php
namespace App\Models;
use CodeIgniter\Model;

class TransactionDetailModel extends Model
{
    protected $table = 'transaction_details';
    protected $primaryKey = 'detail_id';
    protected $allowedFields = ['transaction_id', 'product_id', 'imei', 'qty', 'price_sold', 'purchase_cost'];
}