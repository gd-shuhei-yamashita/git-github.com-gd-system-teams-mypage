<?php

//use Illuminate\Database\Seeder;
use App\Extensions\ExSeeder;

class AssignmentTableSeeder extends ExSeeder
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

        // 譲渡データー
        // pattern A
        DB::table('assignment')->insert([
            'supplypoint_code' => "9999999999999999999002",
            'assignment_before_customer_code' => "ADMN0".$nn."1004",
            'assignment_after_customer_code' => "ADMN0".$nn."1005",
            'assignment_after_contract_name' => "架空の契約名義 3",
            'assignment_after_address' => "架空の住所 3",
            'assignment_after_plan' => "架空のプラン名 3",
            'assignment_date' => "2019/2/1",
            'before_customer_billing_end' => "201902",
            'after_customer_billing_start' => "201903",
            'type' => "1",
        ]);

        // pattern B
        DB::table('assignment')->insert([
            'supplypoint_code' => "9999999999999999999003",
            'assignment_before_customer_code' => "ADMN0".$nn."1005",
            'assignment_after_customer_code' => "ADMN0".$nn."1003",
            'assignment_after_contract_name' => "架空の契約名義 4",
            'assignment_after_address' => "架空の住所 4",
            'assignment_after_plan' => "架空のプラン名 4",
            'assignment_date' => "2019/2/1",
            'before_customer_billing_end' => "201902",
            'after_customer_billing_start' => "201903",
            'type' => "1",
        ]);

        // pattern C
        DB::table('assignment')->insert([
            'supplypoint_code' => "9999999999999999999004",
            'assignment_before_customer_code' => "ADMN0".$nn."1004",
            'assignment_after_customer_code' => "ADMN0".$nn."1003",
            'assignment_after_contract_name' => "架空の契約名義 5",
            'assignment_after_address' => "架空の住所 5",
            'assignment_after_plan' => "架空のプラン名 5",
            'assignment_date' => "2018/2/1",
            'before_customer_billing_end' => "201802",
            'after_customer_billing_start' => "201803",
            'type' => "1",
        ]);
        DB::table('assignment')->insert([
            'supplypoint_code' => "9999999999999999999004",
            'assignment_before_customer_code' => "ADMN0".$nn."1003",
            'assignment_after_customer_code' => "ADMN0".$nn."1004",
            'assignment_after_contract_name' => "架空の契約名義 5",
            'assignment_after_address' => "架空の住所 5",
            'assignment_after_plan' => "架空のプラン名 5",
            'assignment_date' => "2018/10/1",
            'before_customer_billing_end' => "201810",
            'after_customer_billing_start' => "201811",
            'type' => "1",
        ]);

        // pattern D
        DB::table('assignment')->insert([
            'supplypoint_code' => "9999999999999999999005",
            'assignment_before_customer_code' => "ADMN0".$nn."1004",
            'assignment_after_customer_code' => "ADMN0".$nn."1003",
            'assignment_after_contract_name' => "架空の契約名義 6",
            'assignment_after_address' => "架空の住所 6",
            'assignment_after_plan' => "架空のプラン名 6",
            'assignment_date' => "2019/2/1",
            'before_customer_billing_end' => "201902",
            'after_customer_billing_start' => "201903",
            'type' => "1",
        ]);
        DB::table('assignment')->insert([
            'supplypoint_code' => "9999999999999999999005",
            'assignment_before_customer_code' => "ADMN0".$nn."1003",
            'assignment_after_customer_code' => "ADMN0".$nn."1004",
            'assignment_after_contract_name' => "架空の契約名義 6",
            'assignment_after_address' => "架空の住所 6",
            'assignment_after_plan' => "架空のプラン名 6",
            'assignment_date' => "2018/3/1",
            'before_customer_billing_end' => "201803",
            'after_customer_billing_start' => "201804",
            'type' => "1",
        ]);

    }
}
