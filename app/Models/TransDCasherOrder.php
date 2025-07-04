<?php

namespace App\Models;

use CodeIgniter\Model;

class TransDCasherOrder extends Model
{
    protected $table            = 'trans_d_casher_order';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'trans_h_orderid',
        'payment_method',
        'paid_amount',
        'change_amount',
        'created_at',
        'updated_at',
        'updated_by'
    ];
}
