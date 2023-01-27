<?php

namespace App\Models\Mallie;

use App\Models\Mallie\MallieModel;

/**
 * Mallie モデル CustomerOrdered
 */
class CustomerOrdered extends MallieModel
{
    // ex. Laravel5.7における複合プライマリキーを持つテーブルへの挿入と更新
    //      https://qiita.com/hidea/items/968c1013a7663de8d9cc
    use \LaravelTreats\Model\Traits\HasCompositePrimaryKey;

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'CustomerOrdered';
    protected $primaryKey = ['id'];
    const CREATED_AT = 'createdate';
    const UPDATED_AT = 'updatedate';

    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * 契約者情報取得（パスワードリマインダー用）
     * @param string $mobile_phone
     * @param string $birthday
     * @return 
     */
    public static function getForPasswordReminder($mobile_phone, $birthday) {
        return CustomerOrdered::limit(2)
            ->where('mobile_phone', $mobile_phone)
            ->where('birthday', $birthday)
            ->get();
    }
}
