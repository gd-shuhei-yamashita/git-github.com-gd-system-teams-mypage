<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateBillingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing', function (Blueprint $table) {
            Schema::defaultStringLength(191);
            // $table->increments('id')->comment("ID");
            $table->string('supplypoint_code',24)->comment("供給地点特定番号");
            $table->string('customer_code',16)->comment("マイページID");
            $table->string('billing_code',30)->comment("請求番号");
            $table->string('itemize_code',60)->comment("内訳コード");
            $table->date('start_date')->comment("利用開始年月日");
            $table->date('end_date')->comment("利用終了年月日");
            $table->integer('billing_date')->comment("請求年月");
            $table->integer('billing_amount')->comment("請求額");
            $table->integer('tax')->comment("消費税相当額");
            $table->smallInteger('payment_type')->comment("支払い種別(1:口座振替、2:クレジットカード、3:コンビニ払い 4(未使用) 5:銀行窓口)");
            $table->text('power_percentage')->comment("力率")->nullable();
            $table->date('metering_date')->comment("検針月日");
            $table->date('next_metering_date')->comment("次回検針予定日")->nullable();
            $table->decimal('main_indicator', 8, 1)->comment("当月指示数");
            $table->decimal('main_indicator_last_month', 8, 1)->comment("前月指示数");
            $table->integer('meter_multiply')->comment("計器乗率")->nullable();
            $table->decimal('difference', 8, 1)->comment("差引");
            $table->text('payment_date')->comment("当月お支払い予定日");
            $table->integer('usage_date')->comment("利用年月");

            // 日時周り定型句
            $table->timestamp('created_at')->useCurrent()->comment("作成時刻");
            $table->string('created_user_id',64)->default('undefined')->comment("作成ユーザID");
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment("更新時刻");
            $table->string('updated_user_id',64)->default('undefined')->comment("変更ユーザID");

            // $table->index(['id', 'supplypoint_code', 'customer_code']);
            $table->primary(['supplypoint_code', 'customer_code', 'billing_code', 'itemize_code' ], 'key_supplypoint_code');
        });
        // ALTER 文を実行しテーブルにコメントを設定
        DB::statement("ALTER TABLE billing COMMENT '請求データ'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing');
    }
}
