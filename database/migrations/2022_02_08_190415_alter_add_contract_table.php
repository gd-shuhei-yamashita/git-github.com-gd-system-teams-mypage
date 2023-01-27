<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddContractTable extends Migration
{

    public function up()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->date('switching_scheduled_date')
            ->after('shop_name')
            ->nullable()
            ->default(NULL)
            ->comment('スイッチング予定日');
        });
    }

    public function down()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->dropColumn('switching_scheduled_date');
          });
    }
}
