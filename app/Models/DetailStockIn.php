<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailStockIn extends Model
{
    protected $table            = 'detail_md_stockin';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'stockin_id',
        'medicine_id',
        'stock_qty',
        'unit_price',
        'expire_date',
        'created_at',
        'updated_at'
    ];
}
