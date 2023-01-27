<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Mallie モデル CustomerOrdered
 */
class CustomerOrdered extends ExModel
{
    // ex. Laravel5.7における複合プライマリキーを持つテーブルへの挿入と更新
    //      https://qiita.com/hidea/items/968c1013a7663de8d9cc
    use \LaravelTreats\Model\Traits\HasCompositePrimaryKey;

    /**
     * Mallieのテーブル
     */
    protected $connection = 'mysql_mallie';

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'CustomerOrdered';
    
    protected $primaryKey = ['id'];
    
    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = false;
}
