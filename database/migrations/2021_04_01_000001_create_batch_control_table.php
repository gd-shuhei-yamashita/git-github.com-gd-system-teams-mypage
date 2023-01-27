<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateBatchControlTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_control', function (Blueprint $table) {
            $table->string('batch_name',20)->comment("バッチ名");
            // 日時周り定型句
            $table->timestamp('created_at')->nullable()->comment("作成時刻");
        });
        // ALTER 文を実行しテーブルにコメントを設定
        DB::statement("ALTER TABLE batch_control COMMENT 'バッチ制御'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batch_control');
    }
}
