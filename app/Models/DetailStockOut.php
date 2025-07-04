<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailStockOut extends Model
{
    protected $table            = 'detail_md_stockout';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'stockout_id',
        'medicine_id',
        'stock_qty',
        'created_at',
        'updated_at'
    ];
}
