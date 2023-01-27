<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;


/**
 * 共通して行う処理などはここに追加します。
 */
class BaseModel extends Model
{
    /**
     * 増設 コンストラクタ(ExModelのをとりあえず移行)
     *
     * セッションに主DB２に接続するとあったら２に接続させるように変える
     * Illuminate\Database\Eloquent\Model の __construct と互換があるよう書き換え
     */
    public function __construct(array $attributes = [])	{
        parent::__construct($attributes);
    }


    /**
     * save オーバーライド
     */
    public function save(array $options = []) {
        if (self::getOperatorId() !== self::getUserId()) {
            header('Location:/admin/save_error');
            exit;
        }
        return parent::save($options);
    }

    /**
     * getOperatorId
     */
    public static function getOperatorId() {
        return Session::get('user_login.id', 0);
    }

    /**
     * getUserId
     */
    public static function getUserId() {
        return Session::get('user_now.id', 0);
    }
}
