<?php

namespace App\Models;

use CodeIgniter\Model;

class TransDApprovalRefund extends Model
{
    protected $table            = 'trans_d_approval_refund';
    protected $primaryKey       = 'id';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'trans_h_refund',
        'evidence_refund',
        'reject_reason',
        'created_at',
        'updated_at',
        'updated_by'
    ];
}
