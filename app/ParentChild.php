<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 親子データ モデル ParentChild
 */
class ParentChild extends ExModel
{
    use SoftDeletes;
    
    // ex. Laravel5.7における複合プライマリキーを持つテーブルへの挿入と更新
    //      https://qiita.com/hidea/items/968c1013a7663de8d9cc
    use \LaravelTreats\Model\Traits\HasCompositePrimaryKey;

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'parent_child';
    //protected $guarded = array('id');
    
    protected $primaryKey = ['id', 'parent_customer_code', 'child_customer_code'];
    // // increment無効化
    // public $incrementing = false;
    // protected $fillable = ['supplypoint_code', 'parent_seq', 'child_seq'];

    protected $guarded = array('id');
    
    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = true;

    // belongsTo設定
    public function user()
    {
        return $this->belongsTo('App\User', 'child_customer_code', 'customer_code');
    }

}
