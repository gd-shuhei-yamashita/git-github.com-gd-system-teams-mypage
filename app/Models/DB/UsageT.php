<?php

namespace App\Models\DB;

use App\Models\DB\BaseModel;

/**
 * 使用率モデル UsageT
 */
class UsageT extends BaseModel
{
    // ex. Laravel5.7における複合プライマリキーを持つテーブルへの挿入と更新
    //      https://qiita.com/hidea/items/968c1013a7663de8d9cc
    use \LaravelTreats\Model\Traits\HasCompositePrimaryKey;

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'usage_t';
    //protected $guarded = array('id');

    protected $primaryKey = ['supplypoint_code', 'usage_date', 'customer_code'];
    // increment無効化
    public $incrementing = false;
    protected $fillable = ['supplypoint_code', 'usage_date', 'customer_code'];


    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 年間の使用量データを取得する
     * @param string $customerCode 顧客コード
     * @param string $supplypointCode 供給地点特定番号
     * @param int $year 対象年
     * @return array
     */
    public static function getUsages($customerCode, $supplypointCode, $year) {
        $result = self::where('supplypoint_code', $supplypointCode)
            ->where('customer_code', $customerCode)
            ->whereBetween('usage_date', [ $year.'01', $year.'12'])
            ->orderBy('usage_date', 'asc')
            ->get();
        $list = [];
        foreach($result as $k => $usage) {
            $list[$usage->usage_date] = $usage->usage;
        }
        return $list;
    }
}
