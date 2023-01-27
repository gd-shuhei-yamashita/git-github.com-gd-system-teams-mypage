<?php

// use Illuminate\Database\Seeder;
use App\Extensions\ExSeeder;
use Illuminate\Support\Facades\DB;

class ContractTableSeeder extends ExSeeder
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
        DB::table('contract')->insert([
            'customer_code' => 'ADMN0'.$nn.'1003',
            'supplypoint_code' => '9999999999999999999000',
            'contract_name' => '架空の契約名義 1',
            'address'       => '架空の住所 1',
            'plan'          => '架空のプラン名 1',
            'shop_name'     => '架空の店舗名 1',
        ]);

        DB::table('contract')->insert([
            'customer_code' => 'ADMN0'.$nn.'1003',
            'supplypoint_code' => '9999999999999999999001',
            'contract_name' => '架空の契約名義 2',
            'address'       => '架空の住所 2',
            'plan'          => '架空のプラン名 2',
            'shop_name'     => '架空の店舗名 2',
        ]);

        DB::table('contract')->insert([
            'customer_code' => 'ADMN0'.$nn.'1004',
            'supplypoint_code' => '9999999999999999999002',
            'contract_name' => '架空の契約名義 3',
            'address'       => '架空の住所 3',
            'plan'          => '架空のプラン名 3',
            'shop_name'     => '架空の店舗名 3',
        ]);

        DB::table('contract')->insert([
            'customer_code' => 'ADMN0'.$nn.'1005',
            'supplypoint_code' => '9999999999999999999003',
            'contract_name' => '架空の契約名義 4',
            'address'       => '架空の住所 4',
            'plan'          => '架空のプラン名 4',
            'shop_name'     => '架空の店舗名 4',
        ]);

        DB::table('contract')->insert([
            'customer_code' => 'ADMN0'.$nn.'1003',
            'supplypoint_code' => '9999999999999999999004',
            'contract_name' => '架空の契約名義 5',
            'address'       => '架空の住所 5',
            'plan'          => '架空のプラン名 5',
            'shop_name'     => '架空の店舗名 5',
        ]);
        
        
        DB::table('contract')->insert([
            'customer_code' => 'ADMN0'.$nn.'1004',
            'supplypoint_code' => '9999999999999999999005',
            'contract_name' => '架空の契約名義 6',
            'address'       => '架空の住所 6',
            'plan'          => '架空のプラン名 6',
            'shop_name'     => '架空の店舗名 6',
        ]);
        

        // 9999999999999999999999 消してはいけない 一時使用可能な場所を確保しておく。
        DB::table('contract')->insert([
            'customer_code' => 'ADMN0'.$nn.'0001',
            'supplypoint_code' => '9999999999999999999999',
            'contract_name' => '架空の契約名義 X',
            'address'       => '架空の住所 X',
            'plan'          => '架空のプラン名 X',
            'shop_name'     => '架空の店舗名 X',
        ]);

    }
}
