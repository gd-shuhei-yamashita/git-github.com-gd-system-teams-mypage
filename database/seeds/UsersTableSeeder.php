<?php

// use Illuminate\Database\Seeder;
use App\Extensions\ExSeeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends ExSeeder
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

        //$db_num = config("database.connections.mysql2.database") ;
        //
        DB::table('users')->insert([
            'name' => "組織 管理者",
            'email' => 'test'.$nn.'1@example.com',
            'password' => bcrypt('0o0PpVmbw8sR'),
            'customer_code' => 'ADMN0'.$nn.'0001',
            'role' => 1, // ユーザ区分:1:システム管理者\r\n2:主催者\r\n3:SA\r\n9:一般
            'demouser' => 1, // 1:検証用ユーザ\r\n9:一般
            'zip_code' => '111-1111',
            'phone' => '090-9999-9999',
            'email_verified_at'=> DB::raw('now()'),
        ]);

        DB::table('users')->insert([
            'name' => "運用 主催者",
            'email' => 'test'.$nn.'2@example.com',
            'password' => bcrypt('lFOe8b7pWAVT'),
            'customer_code' => 'ADMN0'.$nn.'1002',
            'role' => 2, // ユーザ区分:1:システム管理者\r\n2:主催者\r\n3:SA\r\n9:一般
            'demouser' => 1, // 1:検証用ユーザ\r\n9:一般
            'zip_code' => '112-2222',
            'phone' => '090-9999-9999',
            'email_verified_at'=> DB::raw('now()'),
        ]);

        DB::table('users')->insert([
            'name' => "御利用 遊座",
            'email' => 'test'.$nn.'3@example.com',
            'password' => bcrypt('Aaaa123456'),
            'customer_code' => 'ADMN0'.$nn.'1003',
            'role' => 9, // ユーザ区分:1:システム管理者\r\n2:主催者\r\n3:SA\r\n9:一般
            'demouser' => 1, // 1:検証用ユーザ\r\n9:一般
            'zip_code' => '113-3333',
            'phone' => '090-9999-9999',
            'email_verified_at'=> DB::raw('now()'),
        ]);

        DB::table('users')->insert([
            'name' => "御利用 子遊座",
            'email' => 'test'.$nn.'4@example.com',
            'password' => bcrypt('Aaaa123456'),
            'customer_code' => 'ADMN0'.$nn.'1004',
            'role' => 9, // ユーザ区分:1:システム管理者\r\n2:主催者\r\n3:SA\r\n9:一般
            'demouser' => 1, // 1:検証用ユーザ\r\n9:一般
            'zip_code' => '114-4444',
            'phone' => '090-9999-9999',
            'email_verified_at'=> DB::raw('now()'),
        ]);

        DB::table('users')->insert([
            'name' => "御利用 子連携用",
            'email' => 'test'.$nn.'5@example.com',
            'password' => bcrypt('Aaaa123456'),
            'customer_code' => 'ADMN0'.$nn.'1005',
            'role' => 9, // ユーザ区分:1:システム管理者\r\n2:主催者\r\n3:SA\r\n9:一般
            'demouser' => 1, // 1:検証用ユーザ\r\n9:一般
            'zip_code' => '115-5555',
            'phone' => '090-9999-9999',
            'email_verified_at'=> DB::raw('now()'),
        ]);
        
    }
}
