<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * ExModel 継承用
 */
class ExModel extends Model
{
    /**
     * 増設 コンストラクタ
     * 
     * セッションに主DB２に接続するとあったら２に接続させるように変える
     * Illuminate\Database\Eloquent\Model の __construct と互換があるよう書き換え
     */
    public function __construct(array $attributes = [])	{
        parent::__construct($attributes);
        if (session()->get('db_accesspoint_now', '0') == 2) {
            // 初期DBを定義 DB_DATABASE
            // $this->connection = config('database.connections.mysql2.database');
            $this->connection = 'mysql2';
            Log::debug( "db-database2:" . config('database.connections.mysql2.database') );
        }
    }

}
