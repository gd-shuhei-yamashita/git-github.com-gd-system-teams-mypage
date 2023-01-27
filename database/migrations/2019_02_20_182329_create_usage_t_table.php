<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUsageTTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usage_t', function (Blueprint $table) {
            Schema::defaultStringLength(191);
            //$table->increments('id');
            $table->string('supplypoint_code',24)->comment("供給地点特定番号");
            $table->integer('usage_date')->comment("利用年月");
            $table->string('customer_code',16)->comment("マイページID");
            $table->integer('usage')->comment("使用量");
            
            // 日時周り定型句
            $table->timestamp('created_at')->useCurrent()->comment("作成時刻");
            $table->string('created_user_id',64)->default('undefined')->comment("作成ユーザID");
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment("更新時刻");
            $table->string('updated_user_id',64)->default('undefined')->comment("変更ユーザID");
            
            $table->primary(['customer_code', 'supplypoint_code', 'usage_date'],'usage_t_su_us_cu');
            // $table->index(['customer_code']);
        });
        // ALTER 文を実行しテーブルにコメントを設定
        DB::statement("ALTER TABLE usage_t COMMENT '使用率'");          
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('usage_t');
    }
}
