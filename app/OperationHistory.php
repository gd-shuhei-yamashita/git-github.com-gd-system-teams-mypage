<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class OperationHistory extends ExModel
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'operation_history';
    protected $guarded = array('id');
    
    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = false;


}
