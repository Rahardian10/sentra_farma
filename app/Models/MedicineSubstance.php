<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicineSubstance extends Model
{
    protected $table            = 'medicinal_substances';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'medicine_id',
        'master_substance_id',
        'status',
        'created_at',
        'updated_at'
    ];
}
