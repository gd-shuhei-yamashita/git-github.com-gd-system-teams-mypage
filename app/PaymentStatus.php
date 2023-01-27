<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * 支払い状況 PaymentStatus
 */
class PaymentStatus extends ExModel
{
    use \LaravelTreats\Model\Traits\HasCompositePrimaryKey;

    protected $table = 'payment_status';
    
    protected $primaryKey = ['supplypoint_code', 'billing_date'];
    public $incrementing = false;
    
    protected $guarded = array();
    
    public $timestamps = true;

}
