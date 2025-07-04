<?php

namespace App\Models;

use CodeIgniter\Model;

class Bank extends Model
{
    protected $table            = 'bank';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'name',
        'status',
        'created_at',
        'updated_at'
    ];
}
