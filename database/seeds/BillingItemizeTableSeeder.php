<?php

//use Illuminate\Database\Seeder;
use App\Extensions\ExSeeder;

class BillingItemizeTableSeeder extends ExSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 181026 
        // 番号振り
        $nn = ( $this->db_type() == 3 ) ? "1" : "0";

        // 内訳データ billing_itemize
        // $l_spcode = [ '9999999999999999999000', '9999999999999999999001', '9999999999999999999002', '9999999999999999999003', '9999999999999999999004', '9999999999999999999005' ];
        $l_bcode  = [ 'DENKIT9000027200810' , 'DENKIT9000027200811' , 'DENKIT9000027200812' , 'DENKIT9000027200813' , 'DENKIT9000027200814' , 'DENKIT9000027200815' ];
        // $l_ccode  = [ "ADMN001003", "ADMN001003", "ADMN001004", "ADMN001005", "ADMN001003", "ADMN001004" ];

        // contract
        for ($contract = 1; $contract <= count($l_bcode); $contract++) {
            // year
            for ($year = 2017; $year<=2019; $year++) {
                
                // month
                for ($month = 1; $month <= 12; $month++) {
                    $temp_date_pd = mktime( 0, 0, 0 , $month  , 26, $year); // 当月+1日

                    $billing_code = $l_bcode[$contract-1]; //'DENKIT9000027200810';
                    // $billing_date = '181026';
                    $billing_date       = date("ymd", $temp_date_pd); // 181026
                    
                    $l_name = [ '基本料金' , '電力量　１段料金' , '燃料費調整額', '再エネ発電促進賦課金' ];
                    $l_bill = [ "758.16" , "1483.52" , "-72.96", "220.00" ];
                    
                    // order
                    for ($order = 1; $order <= 4; $order++) {
                        DB::table('billing_itemize')->insert([
                            'billing_code'  => $billing_code,
                            'itemize_code'  => $billing_code . '030011100118322230403100081220' . $billing_date,
                            'itemize_order' => $order, // 並び順

                            'itemize_name'  => $l_name[$order-1], //  
                            'itemize_bill'  => $l_bill[$order-1] ,   // 
                            'note'          => 'note' ,   // 
                        ]);
                    }
                    // order
                }
                // month
            }
            // year
        }
        // contract
    }
}
