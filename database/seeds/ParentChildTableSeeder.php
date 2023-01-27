<?php

// use Illuminate\Database\Seeder;
use App\Extensions\ExSeeder;

class ParentChildTableSeeder extends ExSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 番号振り
        $nn = ( $this->db_type() == 3 ) ? "1" : "0";
        
        //
        DB::table('parent_child')->insert([
            'parent_customer_code' => 'ADMN0'.$nn.'1003',
            'child_customer_code'  => 'ADMN0'.$nn.'1004',
            'change_result'        => '管理画面で登録',
        ]);
    }
}
