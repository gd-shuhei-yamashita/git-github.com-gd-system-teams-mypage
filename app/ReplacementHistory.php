<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 置換履歴 モデル ReplacementHistory
 */
class ReplacementHistory extends ExModel
{
    use SoftDeletes;
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'replacement_history';
    protected $guarded = array('id');
    
    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = true;
    
}
