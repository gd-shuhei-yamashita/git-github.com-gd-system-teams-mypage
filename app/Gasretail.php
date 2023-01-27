<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class Gasretail extends Authenticatable
{

    public static function get_gas_retail(){

        $customer_code = session('user_now.customer_code');

        $result = DB::connection('mysql_mallie')->table('CustomerOrdered AS CO')
        ->join('HalueneContract AS HC', 'CO.id', '=', 'HC.customer_id')
        ->join('GasRetailContract AS GRC', 'GRC.contract_code', '=', 'HC.code')
        ->where('CO.code', $customer_code)
        ->where('HC.status', '!=', 2)
        ->select(
            'HC.pps_business_number',
            'GRC.gas_area',
        )
        ->get()
        ->toArray();

        foreach($result AS $key => $value){
            $gas_retail_data[$key]['pps_business_number'] = $value->pps_business_number;
            $gas_retail_data[$key]['gas_area'] = $value->gas_area;
        }

        if(isset($gas_retail_data)){
            return $gas_retail_data;   
        }

        return;
        
    }

}
