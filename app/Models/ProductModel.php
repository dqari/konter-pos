<?php
namespace App\Models;
use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'product_id';
    protected $allowedFields = ['type', 'barcode', 'brand', 'model_name', 'base_selling_price'];
}