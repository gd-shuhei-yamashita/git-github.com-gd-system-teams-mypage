<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAddContractTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->string('contract_code',20)
            ->after('supplypoint_code')
            ->nullable()
            ->default(NULL)
            ->comment('契約コード');
            $table->integer('pps_type')
            ->after('contract_code')
            ->nullable()
            ->default(NULL)
            ->comment('小売事業者区分');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract', function (Blueprint $table) {
            $table->dropColumn('contract_code');
            $table->dropColumn('pps_type');
        });
    }
}
