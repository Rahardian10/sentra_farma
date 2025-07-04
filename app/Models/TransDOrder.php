<?php

namespace App\Models;

use CodeIgniter\Model;

class TransDOrder extends Model
{
    protected $table            = 'trans_d_order';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'trans_h_orderid',
        'medicine_id',
        'medicine_name',
        'price',
        'medicine_image',
        'qty',
        'created_at',
        'updated_at'
    ];
}
