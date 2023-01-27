<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organizers extends ExModel
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'organizers';
    protected $guarded = array('id');
    
    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = false;
    
}
