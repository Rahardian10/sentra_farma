<?php

namespace App\Models;

use CodeIgniter\Model;

class Cart extends Model
{
    protected $table            = 'cart';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'medicine_id',
        'medicine_name',
        'price',
        'medicine_image',
        'qty',
        'created_at',
        'updated_at'
    ];
}
