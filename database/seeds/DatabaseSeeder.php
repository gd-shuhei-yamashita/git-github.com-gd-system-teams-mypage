<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 初期データ、テストデータ
        // 下にシーディングを行う対象を追加する
        $this->call(UsersTableSeeder::class);
        $this->call(NoticeTableSeeder::class);
        $this->call(ContractTableSeeder::class);
        $this->call(ParentChildTableSeeder::class);
        
        $this->call(AssignmentTableSeeder::class);
        $this->call(BillingTableSeeder::class);
        $this->call(BillingItemizeTableSeeder::class);
        $this->call(UsageTTableSeeder::class);
        $this->call(BrandTableSeeder::class);
    }
}
