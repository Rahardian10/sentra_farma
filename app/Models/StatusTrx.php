<?php

namespace App\Models;

use CodeIgniter\Model;

class StatusTrx extends Model
{
    protected $table            = 'status_trx';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'name',
        'status',
        'created_at',
        'updated_at'
    ];
}
