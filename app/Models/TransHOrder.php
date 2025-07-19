<?php

namespace App\Models;

use CodeIgniter\Model;

class TransHOrder extends Model
{
    protected $table            = 'trans_h_order';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'no_trx',
        'userid',
        'username',
        'recipient_name',
        'phone_number',
        'address',
        'city',
        'area',
        'notes',
        'payment_file',
        'total_price',
        'status',
        'platform',
        'shipping_cost',
        'ppn',
        'created_at',
        'updated_at',
        'updated_by'
    ];
}
