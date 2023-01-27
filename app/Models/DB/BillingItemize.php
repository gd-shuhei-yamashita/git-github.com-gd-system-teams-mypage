<?php

namespace App\Models\DB;

use App\Models\DB\BaseModel;
use Illuminate\Support\Facades\Log;

/**
 * 使用率モデル BillingItemize
 */
class BillingItemize extends BaseModel
{
    // ex. Laravel5.7における複合プライマリキーを持つテーブルへの挿入と更新
    //      https://qiita.com/hidea/items/968c1013a7663de8d9cc
    use \LaravelTreats\Model\Traits\HasCompositePrimaryKey;

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'billing_itemize';
    //protected $guarded = array('id');
    protected $primaryKey = ['billing_code', 'itemize_code', 'itemize_order'];
    // increment無効化
    public $incrementing = false;
    protected $fillable = ['billing_code', 'itemize_code', 'itemize_order'];

    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = true;


    /**
     * 詳細データを取得する
     * @param string $itemizeCode 内訳コード
     * @return array
     */
    public static function getByItemizeCode($itemizeCode)
    {
        return self::where('itemize_code', $itemizeCode)
            ->orderBy('itemize_code', 'asc')
            ->orderBy('itemize_order','asc')
            ->get()
            ->toArray();
    }

    /**
     * 詳細データを取得する
     * @param array $itemizeCodes 内訳コードの配列
     * @return array
     */
    public static function getByItemizeCodes($itemizeCodes)
    {
        return self::whereIn('itemize_code', $itemizeCodes)
            ->orderBy('itemize_code', 'asc')
            ->orderBy('itemize_order','asc')
            ->get()
            ->toArray();
    }
}
