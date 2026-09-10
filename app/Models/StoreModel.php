<?php
namespace App\Models;
use CodeIgniter\Model;

class StoreModel extends Model
{
    protected $table = 'store_profile';
    protected $primaryKey = 'id';
    // PERHATIKAN BARIS INI: Kata 'logo' wajib ada di sini!
    protected $allowedFields = ['store_name', 'logo', 'address', 'phone', 'email', 'footer_nota'];
}