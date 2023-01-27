<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * バッチログ モデル BatchControl
 */
class BatchControl extends ExModel
{
    // use SoftDeletes;
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'batch_control';
    protected $fillable = [
        'batch_name',
        'created_at'
    ];

    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = false;

}
