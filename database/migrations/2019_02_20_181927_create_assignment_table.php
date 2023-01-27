<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAssignmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignment', function (Blueprint $table) {
            Schema::defaultStringLength(191);
            $table->increments('id')->comment("ID");
            $table->string('supplypoint_code',24)->comment("供給地点特定番号");
            $table->string('assignment_before_customer_code',16)->comment("譲渡元顧客ID");
            $table->string('assignment_after_customer_code',16)->comment("譲渡後顧客ID");
            $table->string('assignment_after_contract_name',40)->comment("譲渡後顧客");
            $table->text('assignment_after_address')->comment("譲渡後顧客アドレス");
            $table->text('assignment_after_plan')->comment("譲渡後顧客プラン");
            $table->text('assignment_shop_name')->nullable()->comment("譲渡後顧客店舗名");
            $table->date('assignment_date')->comment("譲渡日");

            $table->integer('before_customer_billing_end')->comment("顧客への請求後");
            $table->integer('after_customer_billing_start')->comment("顧客への請求前");
            $table->tinyInteger('type')->comment("種別  "); // TINYINT
            
            // 日時周り定型句
            $table->timestamp('created_at')->useCurrent()->comment("作成時刻");
            $table->string('created_user_id',64)->default('undefined')->comment("作成ユーザID");
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment("更新時刻");
            $table->string('updated_user_id',64)->default('undefined')->comment("変更ユーザID");
            $table->softDeletes(); // ソフトデリート
            
            $table->index(['id', 'supplypoint_code', 'assignment_date']);
        });
        // ALTER 文を実行しテーブルにコメントを設定
        DB::statement("ALTER TABLE assignment COMMENT '譲渡データ'");        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assignment');
    }
}
