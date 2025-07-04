<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicineLocation extends Model
{
    protected $table            = 'medicine_location';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'name',
        'status',
        'created_at',
        'updated_at'
    ];
}
