<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Database\Migrations\Migration;

use App\Extensions\ExMigration;

class CreateViewUserUnion extends ExMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        # ビューは本番環境のようなDBが別サーバの場合は使えません  
        # if ($this->db_type() !== 2) {
        if (true) {
            return 0;
        }
        // Schema::create('view_user_union', function (Blueprint $table) {
        //     $table->increments('id');
        //     $table->timestamps();
        // });
        $db1 = config("database.connections.mysql.database") ;
        $db2 = config("database.connections.mysql2.database") ;
        $sql = <<<EOT
CREATE VIEW
`user_union`
AS
SELECT 
*,
1 as `db_from`
FROM
`{$db1}`.`users`
UNION
SELECT 
*,
2 as `db_from`
FROM
`{$db2}`.`users`
EOT;
    
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // if ($this->db_type() !== 2) {
        if (true) {
            return 0;
        }
        DB::statement('DROP VIEW user_union');
        // Schema::dropIfExists('view_user_union');
    }
}
