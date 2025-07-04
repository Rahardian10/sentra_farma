<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicineStockOut extends Model
{
    protected $table            = 'medicine_stockout';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'exit_number',
        'title',
        'trx_type',
        'preparation_by',
        'trx_purpose',
        'date_of_public',
        'desc',
        'envidence',
        'created_at',
        'updated_at',
        'updated_by'
    ];
}
