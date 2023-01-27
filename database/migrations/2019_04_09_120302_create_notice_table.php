<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateNoticeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notice', function (Blueprint $table) {
            Schema::defaultStringLength(191);
            $table->increments('id');

            $table->text('notice_comment')->comment("お知らせコメント");
            $table->text('url')->nullable()->comment("URL");
            $table->date('notice_date')->comment("表示日");

            // 日時周り定型句
            $table->timestamp('created_at')->useCurrent()->comment("作成時刻");
            $table->string('created_user_id',64)->default('undefined')->comment("作成ユーザID");
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment("更新時刻");
            $table->string('updated_user_id',64)->default('undefined')->comment("変更ユーザID");
            $table->softDeletes(); // ソフトデリート
            
            $table->index(['id', 'notice_date']);
        });
        // ALTER 文を実行しテーブルにコメントを設定
        DB::statement("ALTER TABLE notice COMMENT 'お知らせ'");           
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notice');
    }
}
