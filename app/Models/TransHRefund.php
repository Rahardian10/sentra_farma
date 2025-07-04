<?php

namespace App\Models;

use CodeIgniter\Model;

class TransHRefund extends Model
{
    protected $table            = 'trans_h_refund';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'trans_h_orderid',
        'no_trx',
        'status',
        'created_at',
        'updated_at',
        'updated_by'
    ];
}
