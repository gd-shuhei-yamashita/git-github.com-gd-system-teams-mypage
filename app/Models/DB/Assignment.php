<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DB\BaseModel;
use Illuminate\Support\Facades\Log;

/**
 * 譲渡データ
 */
class Assignment extends BaseModel
{
    use SoftDeletes;

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'assignment';
    protected $guarded = array('id');

    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * 譲渡後顧客IDで供給地点特定番号を取得する
     * @param string $customerCode
     * @return array
     */
    public static function getSupplypointList($customerCode)
    {
        // 譲渡データをもとに
        // ToDo:譲渡データの一覧を元に除外、追加を実施する
        // 一覧取得 （アルゴリズムは同様）
        $results = self::select('supplypoint_code')
            ->where('assignment_after_customer_code', $customerCode)
            ->orderBy('assignment_date', 'asc')->get();

        $supplypoint = [];
        foreach ($results as $assignment) {
            $supplypoint[] = $assignment['supplypoint_code'];
        }
        Log::debug('譲渡データ:'. implode(',', $supplypoint));
        return $supplypoint;
    }

}
