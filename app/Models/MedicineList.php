<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicineList extends Model
{
    protected $table            = 'medicine_list';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'code',
        'name',
        'status',
        'md_category',
        'manufactur',
        'location',
        'md_unit',
        'convertion_value',
        'md_chronic',
        'vaccine',
        'cover_bpjs',
        'medicine_pict',
        'other_data',
        'price',
        'discount_price',
        'ecatalog',
        'created_at',
        'updated_at',
        'updated_by'
    ];
}
