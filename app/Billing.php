<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * 請求データモデル Billing
 */
class Billing extends ExModel
{
    // ex. Laravel5.7における複合プライマリキーを持つテーブルへの挿入と更新
    //      https://qiita.com/hidea/items/968c1013a7663de8d9cc
    use \LaravelTreats\Model\Traits\HasCompositePrimaryKey;

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'billing';
    //protected $guarded = array('id');
    
    protected $primaryKey = ['supplypoint_code', 'customer_code', 'billing_code', 'itemize_code'];
    // increment無効化
    public $incrementing = false;
    
    protected $guarded = array();
    // protected $fillable = ['supplypoint_code', 'usage_date', 'customer_code'];

    
    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = true;


    // belongsTo設定
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
