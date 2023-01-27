<?php

namespace App\Models\Mallie;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Mallie\MallieModel;
use App\Models\Mallie\HalueneContract;


/**
 * 契約 モデル
 */
class PaymentOrdered extends MallieModel
{

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'PaymentOrdered';


    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 支払情報を取得する
     * @param string $customerCode
     * @return mixed
     */
    public static function get($customerCode)
    {
        $query = HalueneContract::select('HalueneContract.customer_id', 'HalueneContract.payment_id', 'po.payment_type')
            ->join('CustomerOrdered AS co', 'co.id', '=', 'HalueneContract.customer_id')
            ->join('PaymentOrdered AS po', 'po.id', '=', 'HalueneContract.payment_id')
            ->where('co.code', $customerCode)
            ->where('HalueneContract.status', '!=', 2)
        ;

        if ($query->count() < 1) {
            return false;
        } else {
            return $query->first();
        }
    }

}
