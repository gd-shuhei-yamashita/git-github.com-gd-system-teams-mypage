<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * 契約データ モデル Contract
 */
class Contract extends ExModel
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
    //protected $guarded = array('id');
    
    protected $primaryKey = ['supplypoint_code', 'customer_code'];
    // increment無効化
    public $incrementing = false;
    protected $fillable = ['supplypoint_code', 'customer_code'];

    
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
