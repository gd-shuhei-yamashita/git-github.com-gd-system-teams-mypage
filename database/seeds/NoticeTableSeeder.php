<?php

// use Illuminate\Database\Seeder;
use App\Extensions\ExSeeder;

class NoticeTableSeeder extends ExSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 番号振り
        $tnn = ( $this->db_type() == 3 ) ? "db2/" : "";

        //
        DB::table('notice')->insert([
            'notice_comment' => $tnn."コメントテスト01",
            'url'         => '',
            'notice_date' => '2018/01/01',
        ]);
        DB::table('notice')->insert([
            'notice_comment' => $tnn."コメントテスト02",
            'url'         => '',
            'notice_date' => '2018/02/01',
        ]);
        DB::table('notice')->insert([
            'notice_comment' => $tnn."コメントテスト03",
            'url'         => '',
            'notice_date' => '2018/03/01',
        ]);
        DB::table('notice')->insert([
            'notice_comment' => $tnn."コメントテスト04",
            'url'         => '',
            'notice_date' => '2018/04/01',
        ]);
        DB::table('notice')->insert([
            'notice_comment' => $tnn."コメントテスト05",
            'url'         => '',
            'notice_date' => '2018/05/01',
        ]);
        DB::table('notice')->insert([
            'notice_comment' => $tnn."コメントテスト06",
            'url'         => '',
            'notice_date' => '2018/06/01',
        ]);
        DB::table('notice')->insert([
            'notice_comment' => $tnn."コメントテスト07",
            'url'         => '',
            'notice_date' => '2018/07/01',
        ]);
        DB::table('notice')->insert([
            'notice_comment' => $tnn."コメントテスト08",
            'url'         => '',
            'notice_date' => '2018/08/01',
        ]);
        DB::table('notice')->insert([
            'notice_comment' => $tnn."コメントテスト09",
            'url'         => '',
            'notice_date' => '2018/09/01',
        ]);
        DB::table('notice')->insert([
            'notice_comment' => $tnn."コメントテスト10",
            'url'         => '',
            'notice_date' => '2018/10/01',
        ]);
        DB::table('notice')->insert([
            'notice_comment' => $tnn."コメントテスト11",
            'url'         => '',
            'notice_date' => '2018/11/01',
        ]);
        DB::table('notice')->insert([
            'notice_comment' => $tnn."コメントテスト12",
            'url'         => '',
            'notice_date' => '2018/12/01',
        ]);
        DB::table('notice')->insert([
            'notice_comment' => $tnn."2018/12/18 にメンテナンスを 0:00-5:00 に行います。",
            'url'         => 'https://himawari-denki.co.jp/',
            'notice_date' => '2018/12/15',
        ]);
        DB::table('notice')->insert([
            'notice_comment' => $tnn."2018/12/18 のメンテナンス完了いたしました。XXX機能が追加されました。\\n xxx機能はyyyです。",
            'url'         => '',
            'notice_date' => '2018/12/18',
        ]);
    }
}
