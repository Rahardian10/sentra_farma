<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicineStockIn extends Model
{
    protected $table            = 'medicine_stockin';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'po_number',
        'title',
        'supplier',
        'supplier_address',
        'supplier_contact',
        'receiver',
        'date_of_receipt',
        'invoice',
        'total_price',
        'created_at',
        'updated_at',
        'updated_by'
    ];
}
