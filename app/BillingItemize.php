<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * 使用率モデル BillingItemize
 */
class BillingItemize extends ExModel
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
    
}
