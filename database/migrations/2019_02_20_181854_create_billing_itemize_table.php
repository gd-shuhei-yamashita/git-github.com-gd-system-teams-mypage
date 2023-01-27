<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateBillingItemizeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_itemize', function (Blueprint $table) {
            Schema::defaultStringLength(191);
            // $table->increments('id')->comment("ID");
            $table->string('billing_code',30)->comment("請求番号");
            $table->string('itemize_code',60)->comment("内訳コード");
            $table->integer('itemize_order')->default(0)->comment("明細表示順");
            $table->text('itemize_name')->nullable()->comment("内訳名");
            $table->decimal('itemize_bill', 8, 2)->default(0)->comment("内訳金額");
            $table->text('note')->nullable()->comment("ノート");

            // 日時周り定型句
            $table->timestamp('created_at')->useCurrent()->comment("作成時刻");
            $table->string('created_user_id',64)->default('undefined')->comment("作成ユーザID");
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment("更新時刻");
            $table->string('updated_user_id',64)->default('undefined')->comment("変更ユーザID");
 
            // $table->index(['id', 'billing_code', 'itemize_code']);
            $table->primary(['billing_code', 'itemize_code', 'itemize_order']);
        });
        // ALTER 文を実行しテーブルにコメントを設定
        DB::statement("ALTER TABLE billing_itemize COMMENT '内訳データ'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing_itemize');
    }
}
