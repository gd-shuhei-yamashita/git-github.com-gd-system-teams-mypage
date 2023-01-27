<?php
namespace App\Extensions;

use Illuminate\Database\Seeder;

/**
 * ExSeeder 継承用
 */
class ExSeeder extends Seeder
{
    /**
     * 増設 コンストラクタ
     * 
     * セッションに主DB２に接続するとあったら２に接続させるように変える
     * Illuminate\Database\Eloquent\Model の __construct と互換があるよう書き換え
     */
    // public function __construct(array $attributes = [])	{
    //     parent::__construct($attributes);
    // }

    /**
     *  追加メソッド データベースの種別
     */
    public function db_type() {
        $db_placement = config("const.DBPlacement"); // test  
        $db_placement_type = 0;
        if ($db_placement == "single") {
            // single
            $db_placement_type = 1;
        } else if ($db_placement == "multi_master") {
            // multi_master
            $db_placement_type = 2;
        } else if ($db_placement == "multi_slave") {
            // multi_slave
            $db_placement_type = 3;
        }
        return $db_placement_type;
    }

}
