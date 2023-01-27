<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\DB\BaseModel;
use App\Models\DB\NoticeRelation;
use App\Models\DB\Assignment;

/**
 * 契約 モデル
 */
class Contract extends BaseModel
{
    // ex. Laravel5.7における複合プライマリキーを持つテーブルへの挿入と更新
    //      https://qiita.com/hidea/items/968c1013a7663de8d9cc
    use \LaravelTreats\Model\Traits\HasCompositePrimaryKey;

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'contract';
    protected $primaryKey = ['supplypoint_code', 'customer_code'];
    protected $fillable = ['supplypoint_code', 'customer_code'];
    // increment無効化
    public $incrementing = false;

    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = true;

    // belongsTo設定
    public function user()
    {
        return $this->belongsTo('App\Models\DB\User');
    }

    // キャッシュ
    public static $cache = [];

    /**
     * 契約情報を取得する
     * TODO: 過去契約していたプランも取得できるようにする。
     * @param string $customerCode
     * @param date $yearMonth 未実装
     * @return array
     */
    public static function getContracts($customerCode, $yearMonth)
    {
        $cacheKey = $customerCode.'-'.$yearMonth;
        if (isset(self::$cache[$cacheKey])) {
            return self::$cache[$cacheKey];
        }
        $supplypoint = Assignment::getSupplypointList($customerCode);

        $ownSupplypointResult = self::select('supplypoint_code')
            ->where('customer_code', $customerCode)
            ->orderBy('supplypoint_code', 'asc')
            ->get();
        $ownSupplypoint = [];
        foreach ($ownSupplypointResult as $temp) {
            $ownSupplypoint[] = $temp['supplypoint_code'];
        }

        $query = self::select('*')
            ->where(function($query) use ( $customerCode, $supplypoint, $ownSupplypoint ) {
                $query
                    ->where('customer_code', '<>', $customerCode)
                    ->whereIn('supplypoint_code', $supplypoint)
                    ->whereNotIn('supplypoint_code', $ownSupplypoint);
            })
            ->orWhere('customer_code', $customerCode)
            ->orderBy('supplypoint_code', 'asc')
        ;
        $result = $query->get()->toArray();
        self::$cache[$cacheKey] = $result;
        return $result;
    }


    /**
     * 新規登録
     * @param array $params
     * @return bool
     */
    public static function create($params) {
        $contract = new self();
        $contract->customer_code = $params['customer_code'];
        $contract->supplypoint_code = $params['supplypoint_code'];
        if ($params['contract_code']) $contract->contract_code = $params['contract_code'];
        if ($params['pps_type']) $contract->pps_type = $params['pps_type'];
        $contract->contract_name = $params['contract_name'];
        $contract->address = $params['address'];
        $contract->plan = $params['plan'];
        $contract->shop_name = $params['shop_name'];
        $contract->switching_scheduled_date = $params['switching_scheduled_date'];
        $contract->created_user_id = BaseModel::getOperatorId();
        return $contract->save();
    }

}
