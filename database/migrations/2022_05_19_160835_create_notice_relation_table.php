<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNoticeRelationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notice_relation', function (Blueprint $table) {
            Schema::defaultStringLength(191);
            $table->bigIncrements('id');
            $table->integer('notice_id')->comment("お知らせID");
            $table->string('customer_code',16)->comment("マイページID");

            // 日時周り定型句
            $table->timestamp('created_at')->useCurrent()->comment("作成時刻");
            $table->string('created_user_id',64)->default('undefined')->comment("作成ユーザID");
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment("更新時刻");
            $table->string('updated_user_id',64)->default('undefined')->comment("変更ユーザID");
            $table->softDeletes(); // ソフトデリート

            $table->index(['id', 'notice_id']);
        });
        DB::statement("ALTER TABLE notice_relation COMMENT 'お知らせ公開'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notice_relation');
    }
}
