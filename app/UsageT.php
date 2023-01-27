<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * 使用率モデル UsageT
 */
class UsageT extends ExModel
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
    
}
