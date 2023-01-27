<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract', function (Blueprint $table) {
            Schema::defaultStringLength(191);            
            // $table->increments('id')->comment("ID");
            $table->string('customer_code',16)->comment("マイページID");
            $table->string('supplypoint_code',24)->comment("供給地点特定番号");
            $table->string('contract_name',128)->comment("契約名義");
            $table->text('address')->comment("住所");
            $table->text('plan')->comment("プラン名");
            $table->string('shop_name',128)->comment("店舗名");

            // 日時周り定型句
            $table->timestamp('created_at')->useCurrent()->comment("作成時刻");
            $table->string('created_user_id',64)->default('undefined')->comment("作成ユーザID");
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment("更新時刻");
            $table->string('updated_user_id',64)->default('undefined')->comment("変更ユーザID");

            // $table->index(['id', 'supplypoint_code']);
            $table->primary(['customer_code', 'supplypoint_code' ]);
            // $table->index(['customer_code']);
        });

        // ALTER 文を実行しテーブルにコメントを設定
        DB::statement("ALTER TABLE contract COMMENT '契約データ'");           
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract');
    }
}
