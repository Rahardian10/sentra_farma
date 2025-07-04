<?php

namespace App\Models;

use CodeIgniter\Model;

class StatusRefund extends Model
{
    protected $table            = 'status_refund';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'name',
        'status',
        'created_at',
        'updated_at'
    ];
}
