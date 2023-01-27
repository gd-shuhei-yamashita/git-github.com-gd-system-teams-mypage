<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * お知らせ公開 モデル
 */
class NoticeRelation extends ExModel
{
    use SoftDeletes;
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'notice_relation';
    protected $guarded = array('id');
    
    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = false;
    
}
