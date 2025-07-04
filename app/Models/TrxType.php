<?php

namespace App\Models;

use CodeIgniter\Model;

class TrxType extends Model
{
    protected $table            = 'transaction_type';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'name',
        'status',
        'created_at',
        'updated_at'
    ];
}
