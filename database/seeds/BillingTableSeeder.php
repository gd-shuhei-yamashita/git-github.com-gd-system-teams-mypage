<?php

//use Illuminate\Database\Seeder;
use App\Extensions\ExSeeder;

class BillingTableSeeder extends ExSeeder
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

        // ここで生成しているものは、それらしく見える仮データであって正しいデータではありません。
        $l_spcode = [ '9999999999999999999000', '9999999999999999999001', '9999999999999999999002', '9999999999999999999003', '9999999999999999999004', '9999999999999999999005' ];
        $l_bcode  = [ 'DENKIT9000027200810' , 'DENKIT9000027200811' , 'DENKIT9000027200812' , 'DENKIT9000027200813' , 'DENKIT9000027200814' , 'DENKIT9000027200815' ];
        $l_ccode  = [ "ADMN0".$nn."1003", "ADMN0".$nn."1003", "ADMN0".$nn."1004", "ADMN0".$nn."1005", "ADMN0".$nn."1003", "ADMN0".$nn."1004" ];

        // contract
        for ($contract = 1; $contract <= count($l_bcode); $contract++) {
            // year
            //  supplypoint_code  9999999999999999999000 / 請求データ 2017-2019  
            for ($year = 2017; $year<=2019; $year++) {
                // month
                for ($month = 1; $month <= 12; $month++) {
                    // month date year
                    $temp_date_m1 = mktime( 0, 0, 0 , $month-1, 25, $year); // 前月
                    $temp_date    = mktime( 0, 0, 0 , $month  , 25, $year); // 当月
                    $temp_date_pd = mktime( 0, 0, 0 , $month  , 26, $year); // 当月+1日
                    $temp_date_p1 = mktime( 0, 0, 0 , $month+1, 26, $year); // 翌月

                    $start_date         = date("Y/m/d", $temp_date_m1);  // 2018/09/26
                    $end_date           = date("Y/m/d", $temp_date);     // 2018/10/25 + 30日
                    $billing_date       = floor(date("Ym", $temp_date)); // 201811
                    $metering_date      = date("Y/m/d", $temp_date_pd);  // 2018/10/26
                    $next_metering_date = date("Y/m/d", $temp_date_p1);  // 2018/11/21
                    $usage_date         = floor(date("Ym", $temp_date)); // 201810

                    // DENKIT9000027200810030011100118322230403100081220181026 末尾6 181026
                    $billing_code       = $l_bcode[$contract-1];  //'DENKIT9000027200810';
                    $monthtemize_code   = $billing_code . "030011100118322230403100081220" . date("ymd", $temp_date_pd);
                    
                    // 擬似的に
                    $billing_amount = (2370 + mt_rand(0,40)); // 2388 (内税)
                    $tax            = floor($billing_amount * 0.08 / 1.08); // 176

                    DB::table('billing')->insert([
                        'supplypoint_code' => $l_spcode[$contract-1], // '9999999999999999999000',
                        'customer_code' =>  $l_ccode[$contract-1],    // "ADMN001003",
                        'billing_code'  => $billing_code,

                        'itemize_code'  => $monthtemize_code,
                        'start_date'    => $start_date,
                        'end_date'      => $end_date,
                        'billing_date'  => $billing_date,
                        'billing_amount'=> $billing_amount,
                        'tax'           => $tax,
                        'payment_type'  => '2',
                        'power_percentage'  => '',
                        'metering_date'     => $metering_date ,
                        'next_metering_date'=> $next_metering_date,

                        'main_indicator'=> '2076.4',
                        'main_indicator_last_month'=>'2000.1',
                        'meter_multiply'=> null,
                        'difference'    => '76.3',
                        'payment_date'  => 'ご契約のクレジットカード会社に準拠',
                        'usage_date'    => $usage_date,
                    ]);
                }
                // month
            }
            // year
        }
        // contract
    }
}
