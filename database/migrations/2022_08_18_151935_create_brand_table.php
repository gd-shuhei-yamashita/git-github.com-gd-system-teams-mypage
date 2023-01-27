<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand', function (Blueprint $table) {
            Schema::defaultStringLength(191);
            $table->increments('id')->comment("ID");
            $table->string('code',20)->comment("ブランドコード");
            $table->string('name',50)->comment("名称");
            $table->string('name_printed',25)->comment("名称（表示・印刷用）");
            $table->string('explanation_url',255)->nullable()->comment("重説用URL");
            $table->string('contact_url',255)->nullable()->comment("お問い合わせURL");
            $table->string('phone',13)->nullable()->comment("電話番号");
            $table->integer('status')->default('0')->comment("状態");

            // 日時周り定型句
            $table->timestamp('created_at')->useCurrent()->comment("作成時刻");
            $table->string('created_user_id',64)->default('undefined')->comment("作成ユーザID");
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment("更新時刻");
            $table->string('updated_user_id',64)->default('undefined')->comment("変更ユーザID");
            $table->softDeletes(); // ソフトデリート
        });
        DB::statement("ALTER TABLE brand COMMENT 'ブランド'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('brand');
    }
}
