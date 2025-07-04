<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicineCategory extends Model
{
    protected $table            = 'medicine_category';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'name',
        'status',
        'created_at',
        'updated_at'
    ];
}
