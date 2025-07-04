<?php

namespace App\Models;

use CodeIgniter\Model;

class LogStatusTrx extends Model
{
    protected $table            = 'log_status_trx';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'trans_h_orderid',
        'status_id',
        'created_at',
        'updated_at',
        'updated_by'
    ];
}
