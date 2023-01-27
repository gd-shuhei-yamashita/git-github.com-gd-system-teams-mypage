<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateOperationHistoryTable extends Migration
{
    const TABLE_NAME = 'operation_history';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ex. LaravelでPrimaryKeyが複数で片方がIncrementのテーブルを作るmigration  
        // https://qiita.com/t_mitarai/items/42b6e1b6c00c7ce27e73  
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            Schema::defaultStringLength(191);
            $table->unsignedBigInteger('id')->comment("ID");
            // $table->increments('id')->comment("ID");
            $table->string('user_id',64)->default('undefined')->comment("操作ユーザID");
            // $table->integer('detail_id')->comment("詳細ID");
            $table->string('customer_code',16)->nullable()->comment("マイページID");
            $table->string('supplypoint_code',24)->nullable()->comment("供給地点特定番号");
            $table->string('page_name',40)->nullable()->comment("操作ページ名");
            $table->string('file_name',100)->nullable()->comment("アップロードファイル名");
 
            // ex. Laravel5.6 で操作ログを自動で記録する  
            // https://qiita.com/nobu-maple/items/88bd6620d98bb38413bc

            $table->string('route')->nullable()->comment("設定名");
            $table->string('url')->nullable()->comment("要求パス");
            $table->string('method')->nullable()->comment("要求メソッド(GET/POST/etc..)");
            $table->integer('status')->unsigned()->nullable()->comment("要求結果(200/500/etc..)");
            $table->text('message')->nullable()->comment("要求内容");
            $table->string('remote_addr')->nullable()->comment("クライアントIPアドレス");
            $table->string('user_agent')->nullable()->comment("ブラウザ名");
 
            // 日時周り定型句(変形)
            $table->datetime('created_at')->useCurrent()->comment("作成時刻");
            //$table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment("更新時刻");
            
            // ALTER TABLE test1 DROP PRIMARY KEY;

            $table->primary(['id','created_at','user_id']);
            // $table->index(['created_at', 'user_id']);
            // $table->index(['id', 'detail_id', 'customer_code']);
        });

        Schema::table(self::TABLE_NAME, function (Blueprint $table) {
            // 1個目の引数がカラム名、2個目がインクリメント、3個目がunsigned flag
            $table->BigInteger('id', true, true)->change();
        });
        
        // ALTER 文を実行しテーブルにコメントを設定
        DB::statement("ALTER TABLE operation_history COMMENT '操作履歴'");              
        //  パーティションの追加設定(2024年末まで)   
        // それ以降の日時になったら、パーティションの再構成をお願いします。  
        $sql = <<<_SQL_
ALTER TABLE operation_history PARTITION BY RANGE COLUMNS(created_at)
(PARTITION p201903 VALUES LESS THAN ('2019-03-01') ENGINE = InnoDB,
PARTITION p201904 VALUES LESS THAN ('2019-04-01') ENGINE = InnoDB,
PARTITION p201905 VALUES LESS THAN ('2019-05-01') ENGINE = InnoDB,
PARTITION p201906 VALUES LESS THAN ('2019-06-01') ENGINE = InnoDB,
PARTITION p201907 VALUES LESS THAN ('2019-07-01') ENGINE = InnoDB,
PARTITION p201908 VALUES LESS THAN ('2019-08-01') ENGINE = InnoDB,
PARTITION p201909 VALUES LESS THAN ('2019-09-01') ENGINE = InnoDB,
PARTITION p201910 VALUES LESS THAN ('2019-10-01') ENGINE = InnoDB,
PARTITION p201911 VALUES LESS THAN ('2019-11-01') ENGINE = InnoDB,
PARTITION p201912 VALUES LESS THAN ('2019-12-01') ENGINE = InnoDB,

PARTITION p202001 VALUES LESS THAN ('2020-01-01') ENGINE = InnoDB,
PARTITION p202002 VALUES LESS THAN ('2020-02-01') ENGINE = InnoDB,
PARTITION p202003 VALUES LESS THAN ('2020-03-01') ENGINE = InnoDB,
PARTITION p202004 VALUES LESS THAN ('2020-04-01') ENGINE = InnoDB,
PARTITION p202005 VALUES LESS THAN ('2020-05-01') ENGINE = InnoDB,
PARTITION p202006 VALUES LESS THAN ('2020-06-01') ENGINE = InnoDB,
PARTITION p202007 VALUES LESS THAN ('2020-07-01') ENGINE = InnoDB,
PARTITION p202008 VALUES LESS THAN ('2020-08-01') ENGINE = InnoDB,
PARTITION p202009 VALUES LESS THAN ('2020-09-01') ENGINE = InnoDB,
PARTITION p202010 VALUES LESS THAN ('2020-10-01') ENGINE = InnoDB,
PARTITION p202011 VALUES LESS THAN ('2020-11-01') ENGINE = InnoDB,
PARTITION p202012 VALUES LESS THAN ('2020-12-01') ENGINE = InnoDB,

PARTITION p202101 VALUES LESS THAN ('2021-01-01') ENGINE = InnoDB,
PARTITION p202102 VALUES LESS THAN ('2021-02-01') ENGINE = InnoDB,
PARTITION p202103 VALUES LESS THAN ('2021-03-01') ENGINE = InnoDB,
PARTITION p202104 VALUES LESS THAN ('2021-04-01') ENGINE = InnoDB,
PARTITION p202105 VALUES LESS THAN ('2021-05-01') ENGINE = InnoDB,
PARTITION p202106 VALUES LESS THAN ('2021-06-01') ENGINE = InnoDB,
PARTITION p202107 VALUES LESS THAN ('2021-07-01') ENGINE = InnoDB,
PARTITION p202108 VALUES LESS THAN ('2021-08-01') ENGINE = InnoDB,
PARTITION p202109 VALUES LESS THAN ('2021-09-01') ENGINE = InnoDB,
PARTITION p202110 VALUES LESS THAN ('2021-10-01') ENGINE = InnoDB,
PARTITION p202111 VALUES LESS THAN ('2021-11-01') ENGINE = InnoDB,
PARTITION p202112 VALUES LESS THAN ('2021-12-01') ENGINE = InnoDB,

PARTITION p202201 VALUES LESS THAN ('2022-01-01') ENGINE = InnoDB,
PARTITION p202202 VALUES LESS THAN ('2022-02-01') ENGINE = InnoDB,
PARTITION p202203 VALUES LESS THAN ('2022-03-01') ENGINE = InnoDB,
PARTITION p202204 VALUES LESS THAN ('2022-04-01') ENGINE = InnoDB,
PARTITION p202205 VALUES LESS THAN ('2022-05-01') ENGINE = InnoDB,
PARTITION p202206 VALUES LESS THAN ('2022-06-01') ENGINE = InnoDB,
PARTITION p202207 VALUES LESS THAN ('2022-07-01') ENGINE = InnoDB,
PARTITION p202208 VALUES LESS THAN ('2022-08-01') ENGINE = InnoDB,
PARTITION p202209 VALUES LESS THAN ('2022-09-01') ENGINE = InnoDB,
PARTITION p202210 VALUES LESS THAN ('2022-10-01') ENGINE = InnoDB,
PARTITION p202211 VALUES LESS THAN ('2022-11-01') ENGINE = InnoDB,
PARTITION p202212 VALUES LESS THAN ('2022-12-01') ENGINE = InnoDB,

PARTITION p202301 VALUES LESS THAN ('2023-01-01') ENGINE = InnoDB,
PARTITION p202302 VALUES LESS THAN ('2023-02-01') ENGINE = InnoDB,
PARTITION p202303 VALUES LESS THAN ('2023-03-01') ENGINE = InnoDB,
PARTITION p202304 VALUES LESS THAN ('2023-04-01') ENGINE = InnoDB,
PARTITION p202305 VALUES LESS THAN ('2023-05-01') ENGINE = InnoDB,
PARTITION p202306 VALUES LESS THAN ('2023-06-01') ENGINE = InnoDB,
PARTITION p202307 VALUES LESS THAN ('2023-07-01') ENGINE = InnoDB,
PARTITION p202308 VALUES LESS THAN ('2023-08-01') ENGINE = InnoDB,
PARTITION p202309 VALUES LESS THAN ('2023-09-01') ENGINE = InnoDB,
PARTITION p202310 VALUES LESS THAN ('2023-10-01') ENGINE = InnoDB,
PARTITION p202311 VALUES LESS THAN ('2023-11-01') ENGINE = InnoDB,
PARTITION p202312 VALUES LESS THAN ('2023-12-01') ENGINE = InnoDB,

PARTITION p202401 VALUES LESS THAN ('2024-01-01') ENGINE = InnoDB,
PARTITION p202402 VALUES LESS THAN ('2024-02-01') ENGINE = InnoDB,
PARTITION p202403 VALUES LESS THAN ('2024-03-01') ENGINE = InnoDB,
PARTITION p202404 VALUES LESS THAN ('2024-04-01') ENGINE = InnoDB,
PARTITION p202405 VALUES LESS THAN ('2024-05-01') ENGINE = InnoDB,
PARTITION p202406 VALUES LESS THAN ('2024-06-01') ENGINE = InnoDB,
PARTITION p202407 VALUES LESS THAN ('2024-07-01') ENGINE = InnoDB,
PARTITION p202408 VALUES LESS THAN ('2024-08-01') ENGINE = InnoDB,
PARTITION p202409 VALUES LESS THAN ('2024-09-01') ENGINE = InnoDB,
PARTITION p202410 VALUES LESS THAN ('2024-10-01') ENGINE = InnoDB,
PARTITION p202411 VALUES LESS THAN ('2024-11-01') ENGINE = InnoDB,
PARTITION p202412 VALUES LESS THAN ('2024-12-01') ENGINE = InnoDB,

PARTITION pmax VALUES LESS THAN MAXVALUE ENGINE = InnoDB);        
_SQL_;
        DB::statement($sql);
        // 関連：
        // - Mysqlでログ系テーブルを運用するときやっておきたいこと  
        // https://masayuki14.hatenablog.com/entry/20120717/1342482553
        //   "パーティションを使う" 項目を確認。  
        // - DATE PartitionにTO_DAYS()を使うのはやめよう  
        // https://qiita.com/DianthuDia/items/b4cc039b742ad0123275
        //  上記のものを、さらにPARTITION BY RANGE COLUMNS()を使うように書き換えておく。  
        // - MySQLのテーブルにPartitionを追加/削除/確認する
        // https://blog.katsubemakito.net/mysql/partition_confirm_add_remove

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists(self::TABLE_NAME);
        Schema::drop(self::TABLE_NAME);
        /**
         * SEQUENCEを削除する。
         * 自動生成では {テーブル名}_{インクリメントされたカラム}_
         */
        //\DB::statement('DROP SEQUENCE '.self::TABLE_NAME.'_id_seq');        
    }
}
