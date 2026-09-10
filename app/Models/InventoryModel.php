<?php
namespace App\Models;
use CodeIgniter\Model;

class InventoryModel extends Model
{
    protected $table = 'inventory_imei';
    protected $primaryKey = 'imei';
    protected $allowedFields = ['imei', 'product_id', 'purchase_price', 'status'];
}