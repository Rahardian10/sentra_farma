<?php

namespace App\Models;

use CodeIgniter\Model;

class MasterSubstance extends Model
{
    protected $table            = 'master_substance';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'name',
        'status',
        'created_at',
        'updated_at'
    ];
}
