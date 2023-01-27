<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            Schema::defaultStringLength(191);
            $table->increments('id')->comment("ID");
            $table->string('name',40)->comment("お客様名");
            $table->string('email',128)->comment("email");
            $table->string('password')->comment("パスワード");
            $table->rememberToken()->comment("Laravel5 ユーザーログイン維持用");
            $table->tinyInteger('role')->default(9)->comment("ユーザ区分:1:システム管理者\r\n2:主催者\r\n3:SA\r\n9:一般"); // 追加
            $table->tinyInteger('demouser')->default(9)->comment("データ区分:1:検証用ユーザ\r\n9:一般"); // 追加

            $table->string('customer_code',20)->unique()->comment("マイページID");
            $table->string('zip_code')->nullable()->comment("郵便番号");
            $table->string('phone')->nullable()->comment("ご連絡先電話番号");

            // 日時周り定型句
            $table->timestamp('created_at')->useCurrent()->comment("作成時刻");
            $table->string('created_user_id',64)->default('undefined')->comment("作成ユーザID");
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment("更新時刻");
            $table->string('updated_user_id',64)->default('undefined')->comment("変更ユーザID");
            $table->timestamp('email_verified_at')->nullable()->comment("email 初回確認日時");
            $table->timestamp('password_verified_at')->nullable()->comment("パスワード 初回確認日時");
            $table->timestamp('reminder_expired_at')->nullable()->comment("リマインダー有効期限");
            $table->string('password_reminder',70)->nullable()->comment("パスワードリマインダー確認用");
            $table->string('email_new',128)->nullable()->comment("email_new");
            $table->softDeletes(); // ソフトデリート
            
            $table->index(['customer_code', 'email', 'id', 'created_at']);
            // $table->primary(['id', 'code']);

        });
        // ALTER 文を実行しテーブルにコメントを設定
        DB::statement("ALTER TABLE users COMMENT 'ユーザー'");          
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
