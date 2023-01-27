<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateParentChildTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parent_child', function (Blueprint $table) {
            Schema::defaultStringLength(191);
            $table->increments('id')->comment("ID");
            $table->string('parent_customer_code',16)->comment("親_顧客ID");
            $table->string('child_customer_code',16)->comment("子_顧客ID");
            $table->text('change_result')->comment("変更詳細");
            // $table->timestamps();
            $table->timestamp('created_at')->useCurrent()->comment("作成時刻");
            $table->string('created_user_id',64)->default('undefined')->comment("作成ユーザID");
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment("更新時刻");
            $table->string('updated_user_id',64)->default('undefined')->comment("変更ユーザID");
            $table->softDeletes(); // ソフトデリート

            $table->index(['id', 'parent_customer_code', 'child_customer_code']);
        });
        // ALTER 文を実行しテーブルにコメントを設定
        DB::statement("ALTER TABLE parent_child COMMENT '親子顧客関係'");              
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parent_child');
    }
}
