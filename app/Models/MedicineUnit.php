<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicineUnit extends Model
{
    protected $table            = 'medicine_unit';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'name',
        'status',
        'created_at',
        'updated_at'
    ];
}
