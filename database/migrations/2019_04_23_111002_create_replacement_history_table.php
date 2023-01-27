<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReplacementHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replacement_history', function (Blueprint $table) {
            Schema::defaultStringLength(191);
            $table->increments('id');
            $table->smallInteger('type')->default(0)->comment("変更種別:(1:顧客ID紐付変更、2:供特紐付変更)");
            $table->string('old_code',64)->comment("旧番号");
            $table->string('new_code',64)->comment("新番号");
            $table->text('df_contract')->comment("契約データ差分");
            $table->text('df_billing')->comment("内訳データ差分");
            $table->text('df_usage_t')->comment("使用率差分");

            // 日時周り定型句
            $table->timestamp('created_at')->useCurrent()->comment("作成時刻");
            $table->string('created_user_id',64)->default('undefined')->comment("作成ユーザID");
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment("更新時刻");
            $table->string('updated_user_id',64)->default('undefined')->comment("変更ユーザID");
            $table->softDeletes(); // ソフトデリート

            // $table->timestamps();
        });
        // ALTER 文を実行しテーブルにコメントを設定
        DB::statement("ALTER TABLE replacement_history COMMENT '置換履歴'");       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replacement_history');
    }
}
