<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_status', function (Blueprint $table) {
            $table->increments('id')->comment("ID");
            $table->string('supplypoint_code',24)->comment("供給地点特定番号");
            $table->integer('billing_date')->comment("請求年月");
            $table->integer('payment_amount')->comment("入金金額");
            $table->string('payment_type',128)->comment("支払区分");

            // 日時周り定型句
            $table->timestamp('created_at')->useCurrent()->comment("作成時刻");
            $table->string('created_user_id',64)->default('undefined')->comment("作成ユーザID");
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment("更新時刻");
            $table->string('updated_user_id',64)->default('undefined')->comment("変更ユーザID");
            $table->timestamp('deleted_at')->nullable()->comment("削除時刻");
        });
        DB::statement("ALTER TABLE payment_status COMMENT '入金状況データ'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_status');
    }
}
