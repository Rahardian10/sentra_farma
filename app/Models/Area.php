<?php

namespace App\Models;

use CodeIgniter\Model;

class Area extends Model
{
    protected $table            = 'area';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'name',
        'price',
        'status',
        'created_at',
        'updated_at'
    ];
}
