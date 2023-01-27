<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\DB\BaseModel;

/**
 * お知らせ公開 モデル
 */
class NoticeRelation extends BaseModel
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
