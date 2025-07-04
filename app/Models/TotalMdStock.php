<?php

namespace App\Models;

use CodeIgniter\Model;

class TotalMdStock extends Model
{
    protected $table            = 'total_md_stock';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'Medicine_id',
        'qty',
        'created_at',
        'updated_at'
    ];
}
