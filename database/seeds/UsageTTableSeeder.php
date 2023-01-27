<?php

use Illuminate\Database\Seeder;

class UsageTTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // --
        $db_placement = config("const.DBPlacement"); // test  
        $db_placement_type = 0;
        if ($db_placement == "single") {
            // single
            $db_placement_type = 1;
        } else if ($db_placement == "multi_master") {
            // multi_master
            $db_placement_type = 2;
        } else if ($db_placement == "multi_slave") {
            // multi_slave
            $db_placement_type = 3;
        }
        // 番号振り
        $nn = ($db_placement_type==3) ? "1" : "0";
        
        // 使用率 
        // ADMN001003 / 9999999999999999999000
        for ($i = 201901; $i <= 201905; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999000',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001003",
                'usage'            => intval(68 + sin(($i + 2)/3.1)*30),
            ]);
        }

        for ($i = 201801; $i <= 201812; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999000',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001003",
                'usage'            => intval(68 + sin($i/3.1)*30),
            ]);
        }

        for ($i = 201707; $i <= 201712; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999000',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001003",
                'usage'            => intval(68 + sin(($i + 1)/3.1)*30),
            ]);
        }

        // ADMN001003 / 9999999999999999999001
        for ($i = 201901; $i <= 201905; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999001',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001003",
                'usage'            => intval(68 + sin(($i + 2)/3.1)*30),
            ]);
        }

        for ($i = 201801; $i <= 201812; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999001',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001003",
                'usage'            => intval(68 + sin($i/3.1)*30),
            ]);
        }

        for ($i = 201707; $i <= 201712; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999001',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001003",
                'usage'            => intval(68 + sin(($i + 1)/3.1)*30),
            ]);
        }

        // ADMN001004 / 9999999999999999999002
        for ($i = 201901; $i <= 201905; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999002',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001004",
                'usage'            => intval(68 + sin(($i + 2)/3.1)*30),
            ]);
        }

        for ($i = 201801; $i <= 201812; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999002',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001004",
                'usage'            => intval(68 + sin($i/3.1)*30),
            ]);
        }

        for ($i = 201707; $i <= 201712; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999002',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001004",
                'usage'            => intval(68 + sin(($i + 1)/3.1)*30),
            ]);
        }

        // ADMN001005 / 9999999999999999999003
        for ($i = 201901; $i <= 201905; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999003',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001005",
                'usage'            => intval(68 + sin(($i + 2)/3.1)*30),
            ]);
        }

        for ($i = 201801; $i <= 201812; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999003',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001005",
                'usage'            => intval(68 + sin($i/3.1)*30),
            ]);
        }

        for ($i = 201707; $i <= 201712; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999003',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001005",
                'usage'            => intval(68 + sin(($i + 1)/3.1)*30),
            ]);
        }

        // ADMN001003 / 9999999999999999999004
        for ($i = 201901; $i <= 201905; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999004',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001003",
                'usage'            => intval(68 + sin(($i + 2)/3.1)*30),
            ]);
        }

        for ($i = 201801; $i <= 201812; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999004',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001003",
                'usage'            => intval(68 + sin($i/3.1)*30),
            ]);
        }

        for ($i = 201707; $i <= 201712; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999004',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001003",
                'usage'            => intval(68 + sin(($i + 1)/3.1)*30),
            ]);
        }

        
        // ADMN001004 / 9999999999999999999005
        for ($i = 201901; $i <= 201905; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999005',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001004",
                'usage'            => intval(68 + sin(($i + 2)/3.1)*30),
            ]);
        }

        for ($i = 201801; $i <= 201812; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999005',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001004",
                'usage'            => intval(68 + sin($i/3.1)*30),
            ]);
        }

        for ($i = 201707; $i <= 201712; $i++) {
            DB::table('usage_t')->insert([
                'supplypoint_code' => '9999999999999999999005',
                'usage_date'       => $i , // ex. 201803
                'customer_code'    => "ADMN001004",
                'usage'            => intval(68 + sin(($i + 1)/3.1)*30),
            ]);
        }

        
    }
}
