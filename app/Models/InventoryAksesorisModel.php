<?php
namespace App\Models;
use CodeIgniter\Model;

class InventoryAksesorisModel extends Model
{
    protected $table = 'inventory_aksesoris';
    protected $primaryKey = 'id';
    protected $allowedFields = ['product_id', 'qty', 'purchase_price'];
}