<?php

namespace App\Models\DB;

use App\Models\DB\BaseModel;
use Illuminate\Support\Facades\Log;

/**
 * 支払い状況 PaymentStatus
 */
class PaymentStatus extends BaseModel
{
    use \LaravelTreats\Model\Traits\HasCompositePrimaryKey;

    protected $table = 'payment_status';

    protected $primaryKey = ['supplypoint_code', 'billing_date'];
    public $incrementing = false;

    protected $guarded = array();

    public $timestamps = true;

}
