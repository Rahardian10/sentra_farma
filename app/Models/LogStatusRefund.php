<?php

namespace App\Models;

use CodeIgniter\Model;

class LogStatusRefund extends Model
{
    protected $table            = 'log_status_refund';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'trans_h_refund',
        'status_refund',
        'created_at',
        'updated_at',
        'updated_by'
    ];
}
