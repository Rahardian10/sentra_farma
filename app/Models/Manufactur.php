<?php

namespace App\Models;

use CodeIgniter\Model;

class Manufactur extends Model
{
    protected $table            = 'manufactur';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'name',
        'status',
        'created_at',
        'updated_at'
    ];
}
