<?php

namespace App\Models\Mallie;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Mallie\MallieModel;


/**
 * 契約 モデル
 */
class HalueneContract extends MallieModel
{

    protected $table = 'HalueneContract';
    const CREATED_AT = 'createdate';
    const UPDATED_AT = 'updatedate';

    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = true;


}
