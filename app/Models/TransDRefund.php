<?php

namespace App\Models;

use CodeIgniter\Model;

class TransDRefund extends Model
{
    protected $table            = 'trans_d_refund';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'trans_h_refund',
        'name',
        'phone',
        'email',
        'bank_id',
        'bank_account',
        'reason',
        'created_at',
        'updated_at'
    ];
}
