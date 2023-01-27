<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\FileMakerController;

// eloquent
use App\Contract; 
use App\Billing;
use App\BillingItemize;
use App\UsageT;
use App\Assignment;
use App\User;

use App\Facades\GetInvoice;

use App\Consts\SupplierConsts;

/**
 *   使用量・請求金額画面
 */
class ConfirmUsagedataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // 初期画面表示
        return view('confirm_usagedata');
    }

    /**
     * 表示するプルダウン内容を取得
     */
    public function pulldown(Request $request)
    {
        Log::debug("ConfirmUsagedataController 1:pulldown:");    
        $req = $request->all();
        
        // 契約情報取得
        $sort = 'created_at';
        $order = 'desc';
        $contracts = GetInvoice::get_supplypoint_list(session('user_now.customer_code'), $sort, $order);

        // 通信契約
        $fileMaker = new FileMakerController();
        $local_file = $fileMaker->downloadForFileMaker('customer_info_read_file/','customer_info_read_file_' . session('user_now.customer_code') . '.csv');
        if (!empty($local_file)) {
            $mobile_contracts = $fileMaker->read_file($local_file);
            if (!empty($mobile_contracts)) {
                $contracts = array_merge($contracts, $fileMaker->contract_format($mobile_contracts));
            }
        }

        // 契約ごとの請求データの範囲を取得
        foreach ($contracts as $key => $contract) {
            $contracts[$key]['billing_range'] = $this->billing_range_by_contract($contract['supplypoint_code']);
        }

        $value = [];
        $value["status"]    = "200";
        $value["contracts"] = $contracts;
        $value["message"]   = "OK";
        return $value;    
    }
    
    /**
     * 2 対象年月日プルダウン
     */
    public function billings_pulldown(Request $request)
    {
        // 使用場所に従った情報の取得
        //  使用場所 詳細取得
        //  対象年月日一覧取得
        
        Log::debug("ConfirmUsagedataController 2:pulldown_billings:");
    
        $req = $request->all();

        // 必ずセッションのキーを元に出す
        Log::debug( session('user_now.customer_code') );
        Log::debug( $req['supplypoint_code'] );
        
        // 使用場所一覧詳細を取得する
        //$contracts = $this->get_supplypoint_list( session('user_now.customer_code') );
        $contracts = GetInvoice::get_supplypoint_list(session('user_now.customer_code'));
        // 通信契約
        $fileMaker = new FileMakerController();
        $local_file = $fileMaker->downloadForFileMaker('customer_info_read_file/','customer_info_read_file_' . session('user_now.customer_code') . '.csv');
        if (!empty($local_file)) {
            $mobile_contracts = $fileMaker->read_file($local_file);
            if (!empty($mobile_contracts)) {
                $contracts = array_merge($contracts, $fileMaker->contract_format($mobile_contracts));
            }
        }

        foreach($contracts as $temp_contract){
            if ($temp_contract['supplypoint_code'] == $req['supplypoint_code']) {
                $contract_query = DB::connection('mysql_mallie')->table('HalueneContract AS hc')
                ->join('CustomerOrdered AS co', 'co.id', '=', 'hc.customer_id')
                ->where('hc.power_customer_location_number', $temp_contract['supplypoint_code'])
                ->where('co.code', $temp_contract['customer_code'])
                ->orderBy('hc.createdate', 'desc')
                ->select('hc.status');

                if (!empty($temp_contract['service']) && $temp_contract['service'] == 'wifi') {
                    if ($temp_contract['mobile_status'] == '契約中') {
                        $temp_contract['status'] = '1';
                    } else {
                        $temp_contract['status'] = 0;
                    }
                } else {
                    if ($contract_query->count() < 1) {
                        $temp_contract['status'] = 0;
                    } else {
                        $contract_result = $contract_query->first();
                        $temp_contract['status'] = $contract_result->status;
                    }
                }

                $contract = $temp_contract;
            }
        }

        // 戻り値に割り当てられた 請求データ を渡す。
        $value = [];
        $value["status"]    = "200";
        $value["contract"]  = $contract;
        $value["message"]   = "OK";
        return $value;    
    }

    /**
     * 3 電力情報の取得
     */
    public function billings_getlist(Request $request)
    {
        $req = $request->all();

        // 検索条件
        Log::debug( session('user_now.customer_code') );    // 顧客ID
        Log::debug( $req['supplypoint_code'] ); // 供給地点番号
        Log::debug( $req['billing_date'] );     // 対象年

        // 対象年月日に従った電力情報一覧表示の取得
        // グラフの反映

        // 契約テーブルから対象顧客の供給地点番号一覧を取得
        $contract = [];
        $contracts = GetInvoice::get_supplypoint_list(session('user_now.customer_code'));
        // 通信契約
        $fileMaker = new FileMakerController();
        $local_file = $fileMaker->downloadForFileMaker('customer_info_read_file/','customer_info_read_file_' . session('user_now.customer_code') . '.csv');
        if (!empty($local_file)) {
            $mobile_contracts = $fileMaker->read_file($local_file);
            if (!empty($mobile_contracts)) {
                $contracts = array_merge($contracts, $fileMaker->contract_format($mobile_contracts));
            }
        }
        foreach($contracts as $temp_contract){
            if ($temp_contract['supplypoint_code'] == $req['supplypoint_code']) {
                $contract = $temp_contract;
            }
        }

        // 契約テーブル（一覧）に該当するデータがない場合
        if (count($contract) == 0) {
            $value = [];
            $value["status"]    = "500";
            $value["message"]   = "NG";
            return $value;    
        }

        // 譲渡データの一覧を元に除外、追加を実施する
        // 譲渡データをもとにusage_dateの期間条件を作成　
        $whereraw = GetInvoice::get_assignment_whereraw( $req['supplypoint_code'] , session('user_now.customer_code') );

        // 使用率 usage_t 取得
        $usage_t = [];
        $usage_t_spec = [];

        // 年度の範囲を求める式 usage_t の usage_dateは、201901 のような年月を数字で直列に並べた値
        $date_number = (int)$req['billing_date'] * 100;
        // $results = UsageT::where("customer_code", session('user_now.customer_code'))
        // ->where("supplypoint_code", $req['supplypoint_code'])
        $results = UsageT::where("supplypoint_code", $req['supplypoint_code'])
        ->where('customer_code', $contract['customer_code'])
        ->whereBetween('usage_date', [ $date_number + 1, $date_number + 12 ])
        ->whereRaw($whereraw) 
        ->orderBy('usage_date', 'asc')
        ->get();

        foreach ($results as $temp_usage_t) {
            $usage_t[] = [
                "supplypoint_code"    => $temp_usage_t->supplypoint_code,
                "customer_code"       => $temp_usage_t->customer_code,
                "usage"               => $temp_usage_t->usage,
                "usage_date"          => $temp_usage_t->usage_date,
            ];
        }
        // Log::debug( $results );
        if (session()->get('db_accesspoint_now', '0') == 2) {
            // $usage_t_spec["step_size"] = 1000;
            $usage_t_spec["step_size"] = 200;
        } else {
            $usage_t_spec["step_size"] = 10;
        }
        // 請求データ billing / billing_itemize から 一覧表示 読み込み
        $billings = [];
        $billing_code_list = [];
        $billings_itemize = [];
        $billings_itemize["name"] = [];

        // $results = Billing::where("customer_code", session('user_now.customer_code'))
        // ->where("supplypoint_code", $req['supplypoint_code'])
        $results = Billing::where("supplypoint_code", $req['supplypoint_code'])
        ->where('customer_code', $contract['customer_code'])
        ->whereBetween('usage_date', [ $date_number + 1, $date_number + 12 ])
        ->whereRaw($whereraw) 
        ->orderBy('usage_date', 'desc')
        ->get();

                //    ->where("supplypoint_code", $req['supplypoint_code'])->get();
        Log::debug( $results );

        foreach ($results as $temp_contract) {
            $billings[] = [
                "supplypoint_code"    => $temp_contract->supplypoint_code, // 供給地点特定番号
                "customer_code"       => $temp_contract->customer_code,    // マイページID
                "billing_code"        => $temp_contract->billing_code,     // 請求番号
                "itemize_code"        => $temp_contract->itemize_code,     // 内訳コード
                "start_date"          => $temp_contract->start_date,       // 利用開始年月日
                "end_date"            => $temp_contract->end_date,         // 利用終了年月日
                "billing_date"        => $temp_contract->billing_date,     // 請求年月
                "billing_amount"      => $temp_contract->billing_amount,   // 請求額
                "tax"                 => $temp_contract->tax,              // 消費税相当額
                "payment_type"        => $temp_contract->payment_type,     // 支払い種別
                "power_percentage"    => $temp_contract->power_percentage, // 力率
                "metering_date"       => $temp_contract->metering_date,    // 検針月日
                "next_metering_date"  => $temp_contract->next_metering_date,// 次回検針予定日
                "main_indicator"      => $temp_contract->main_indicator,   // 当月指示数
                "main_indicator_last_month" => $temp_contract->main_indicator_last_month,  // 前月指示数
                "meter_multiply"      => $temp_contract->meter_multiply,   // 計器乗率
                "difference"          => $temp_contract->difference,       // 差引
                "payment_date"        => $temp_contract->payment_date,     // 当月お支払い予定日
                "usage_date"          => $temp_contract->usage_date,       // 利用年月
            ];
            $billing_code_list[] = $temp_contract->itemize_code;

            // 内訳一覧の項目を先に埋めておく
            $billings_itemize[$temp_contract->itemize_code]["usage_date"]     = $temp_contract->usage_date;
            $billings_itemize[$temp_contract->itemize_code]["billing_amount"] = $temp_contract->billing_amount;
            $billings_itemize[$temp_contract->itemize_code]["tax"] = $temp_contract->tax;
        }
        
        // 請求データ billings_itemize から 一覧表示 読み込み
        
        Log::debug( "billing_code_list : " );
        Log::debug( $billing_code_list);
        // billing_itemizeの一覧を読み込んだ後整えて配列代入する
        $results = BillingItemize::whereIn('itemize_code', $billing_code_list)
        ->orderBy('itemize_code', 'asc')
        ->orderBy('itemize_order','asc')
        ->get();

        // $billings_itemize[] = [
        //     "billing_code"  => $temp_itemize->billing_code, // 請求番号
        //     "itemize_code"  => $temp_itemize->itemize_code, // 内訳コード
        //     "itemize_order" => $temp_itemize->itemize_order, // 明細表示順
        //     "itemize_name"  => $temp_itemize->itemize_name, // 内訳名
        //     "itemize_bill"  => $temp_itemize->itemize_bill, // 内訳金額
        //     "note"          => $temp_itemize->note, // ノート
        // ];

        // SQLではなくアルゴリズムでまとめて内訳一覧を構成
        $now_itemize_code = "";
        $now_itemize_count = -1;

        foreach ($results as $temp_itemize) {
            Log::debug($temp_itemize);
            // 年月の記載 初回入力の場合
            if ($temp_itemize["itemize_code"] != $now_itemize_code) {
                $now_itemize_count ++;
                $now_itemize_code = $temp_itemize["itemize_code"];
                $billings_itemize[$now_itemize_code]["itemize_code"] = $temp_itemize->itemize_code; // 内訳コード
                // // 請求データ billings からは請求額、消費税相当額を代入する
                // $now_billing = $billings[array_search( $temp_itemize["itemize_code"] , $billing_code_list)];
                // $billings_itemize[$now_itemize_code]["billing_amount"] = $now_billing["billing_amount"];
                // $billings_itemize[$now_itemize_code]["tax"] = $now_billing["tax"];
            } else {
                // 
            }
            $billings_itemize[$now_itemize_code][$temp_itemize->itemize_name] = $temp_itemize->itemize_bill; // 内訳金額
            // 内訳名 を重複なくすべて取り込む
            if (!in_array($temp_itemize->itemize_name, $billings_itemize["name"], true)) {
                $billings_itemize["name"][] = $temp_itemize->itemize_name; // 内訳名
            }
        }
        Log::debug('$billings_itemizeの中身');
        Log::debug($billings_itemize);

        // 戻り値に割り当てられた 請求データ を渡す。
        $value = [];
        $value["status"]    = "200";
        // $value["contract"]  = $contract;
        $value["usage_t"]   = $usage_t;
        $value["usage_t_spec"]  = $usage_t_spec;
        $value["billing_date"]  = $req['billing_date'];
        $value["billings"]  = $billings;
        $value["billings_itemize"] = $billings_itemize;
        $value["message"]   = "OK";
        return $value;    

    }
    /**
     * 30分電力量API（とりあえず取得テストだけ）
     */
    public function get_usage()
    {
        $curl_handle = curl_init();

        curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl_handle, CURLOPT_URL, 'http://api.dev.all-electric.grandata.jp/v1/power/usage/0900011794952100000000/30min?from=20220418&to=20220420');
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);

        $json_response = curl_exec($curl_handle);

        Log::debug($json_response);
    }
    /**
     * 4 CSV/XLS 一括出力
     * 使用場所、対象年月に従ったcsv/xlsを出力。
     */
    public function export_chart(Request $request)
    {
        // 
        $req = $request->all();
        
        Log::debug( $req['supplypoint_code'] );
        Log::debug( $req['billing_date'] );

        $value_child = $this->billings_getlist( $request );

        // csvを元データをもとに生成する
        $encoded_csv = "";
        // $itemize_tmp = $value_child["billings_itemize"];
        // Log::debug($itemize_tmp);

        // 譲渡データをもとに
        // $this->get_assignment_list( session('user_now.customer_code')  );
        // ToDo:譲渡データの一覧を元に除外、追加を実施する

        // 1行目
        $billname_count = 0;
        $name_list = ["利用年月","請求年月"];
        $name_list2 = ["基本料金","電力量　１段料金","電力量　２段料金","電力量　３段料金"];

        foreach($value_child["billings_itemize"]["name"] as $temp_name){
            // Log::debug($temp_name);
            if (!in_array($temp_name, $name_list2)) {
                $name_list2[] = $temp_name;
            }
        }
        $name_list = array_merge( $name_list, $name_list2 );
        $name_list = array_merge( $name_list, ["合計金額"] );

        $encoded_csv.= ((count($name_list) > 0) ? implode(',', $name_list) : "-err-" ) . "\r\n";
        
        // 2行目以降
        Log::debug("2行目以降 ");
        foreach( $value_child["billings"] as $temp_data) {
            Log::debug($temp_data);
            // 初期化
            $data_list = [];
            // 利用年月
            $data_list[] = $temp_data["usage_date"];

            // 請求年月
            $data_list[] = $temp_data["billing_date"];

            // 内訳項目
            $temp_itemize_code = $temp_data["itemize_code"];
            Log::debug( $temp_itemize_code);
            Log::debug( $value_child["billings_itemize"][ $temp_itemize_code ] );

            foreach ($name_list2 as $temp_name) {
                // Log::debug($loop1);
                // 配列に値が存在しない場合を確認して空白を合間に入れる。
                if (  array_key_exists( $temp_name, $value_child["billings_itemize"][ $temp_itemize_code ] ) )  {
                    $data_list[] = $value_child["billings_itemize"][ $temp_itemize_code ][$temp_name]; 
                } else {
                    $data_list[] = "";
                }
            }

            // 合計金額
            $data_list = array_merge($data_list, [$temp_data["billing_amount"]] );

            $encoded_csv.= ((count($data_list) > 0) ? implode(',', $data_list) : "-err-" ) . "\r\n";
        }
        // Log::debug( $encoded_csv );

        // 戻り値に割り当てられた 請求データ を渡す。
        $value = [];
        $value["status"]       = "200";
        $value["file_name"]    = "請求一覧_" . $req['billing_date'] . ".csv";
        // $value["encoded_csv"]  = base64_encode( mb_convert_encoding($encoded_csv , "SJIS") );
        $value["encoded_csv"]  = base64_encode( $encoded_csv );
        $value["message"]      = "OK";
        return $value;            
    }
    
    /**
     * 5 CSV/XLS 一括出力オリジナル
     * 使用場所、対象年月に従ったcsv/xlsを出力。
     */
    public function export_chart_original(Request $request)
    {
        $req = $request->all();

        // 関係する全拠点の取得を行う。
        Log::debug( $req['supplypoint_code'] );
        Log::debug( $req['billing_date'] );

        $value_child = [];

        // supplypoint_code の一覧  
        $contracts = GetInvoice::get_supplypoint_list(session('user_now.customer_code'));

        // 追加
        $value_child[] = [];

        // csvを元データをもとに生成する
        $encoded_csv = "";

        // 1行目
        $name_list = ["マイページID",
        "ご契約者名",
        "電気名義",
        "使用場所住所",
        // "店舗名",
        "供給地点特定番号",
        "請求月",
        "ご利用期間",
        "検針月日",
        "次回検針予定日",
        "当月お支払い予定日",
        "請求金額合計",
        "使用量",
        "プラン名"];
        
        // 以降は  で追加されます。
        $variable_name_list = [];

        // "基本料金",
        // "電力量　１段料金",
        // "電力量　２段料金",
        // "燃料費調整額",
        // "再エネ発電促進賦課金"

        $billname_count = 0;

        
        // 2行目以降
        Log::debug("2行目以降 ");

        // contractsにある全拠点を元に巡回
        foreach($contracts as $temp_data){
            Log::debug($temp_data);
            $customer_code = $temp_data["customer_code"];
            $supplypoint_code = $temp_data["supplypoint_code"];
            // Log::debug("supplypoint_code : " . $supplypoint_code);

            $temp_req = [];
            $temp_req["customer_code"] = $customer_code;
            $temp_req["supplypoint_code"] = $supplypoint_code;
            $temp_req["date"]     = $req["billing_date"].$req["billing_month"];
            $temp_req["month"]    = $req["billing_month"];
            Log::debug($temp_req);

            // Log::debug("ccc: ");
            // supplypoint_code の一覧 をもとに、
            // すべての電力請求詳細の取得をする
            $result = GetInvoice::get_billing_detail($temp_req);
            if ($result["status"] == false) {
                continue(1);
            }
            
            // Log::debug("ddd: ");
            $billing         = $result["billing"];
            $billing_itemize = $result["billing_itemize"];
            // Log::debug( $billing_itemize );
            // billing_itemize 最初の読み込み時に１行目の行末に項目名を追加させる。  
            foreach( $billing_itemize as $temp_itemize) {
                // ヘッダーリストに存在しない場合は項目名を追加
                if (!in_array($temp_itemize["itemize_name"], $name_list)) {
                    $name_list[] = $temp_itemize["itemize_name"];
                    $variable_name_list[] = $temp_itemize["itemize_name"];
                }
            }            

            $billname_count++;

            // 固定ヘッダー列の各データを設定
            $data_list = [
                $billing["customer_code"], // マイページID
                session('user_now.name'),  // ご契約者名
                $billing["contract_name"], // 電気名義
                $billing["address"], // 使用場所住所
                // $billing["shop_name"], // 店舗名
                "'".$billing["supplypoint_code"], // 供給地点特定番号 前の'は数字が指数表示になるのを防ぐため。  
                $billing["billing_date"], // 請求月
                $billing["start_date"]."～".$billing["end_date"], // ご利用期間
                $billing["metering_date"], // 検針月日
                $billing["next_metering_date"], // 次回検針予定日
                $billing["payment_date"], // 当月お支払い予定日
                '"'.number_format($billing["billing_amount"]).'"', // 請求金額合計
                // ($billing["usage"] - $billing["main_indicator_last_month"])."kwh", // 使用量
                $billing["usage"]."kwh", // 使用量
                $billing["plan"], // プラン名
            ];

            // 金額
            // 変動ヘッダー列の各データを設定
            foreach ($variable_name_list as $header_name) {
                $hit = false;
                foreach( $billing_itemize as $temp_itemize) {
                    if ($temp_itemize["itemize_name"] == $header_name) {
                        $data_list[] = $temp_itemize["itemize_bill"];
                        $hit = true;
                        // 内側のforeachを抜ける
                        continue;
                    }
                }
                if (!$hit) {
                    $data_list[] = "";
                }
            }

            $encoded_csv.= implode(',', $data_list) . "\r\n";
        }

        // 最後に一行目の追加を行頭に行う
        $encoded_csv = implode(',', $name_list) . "\r\n" . $encoded_csv;

        // Log::debug( $encoded_csv );

        // 戻り値に割り当てられた 請求データ を渡す。
        $value = [];
        $value["status"]       = "200";
        $value["file_name"]    = "電気利用料内訳_" . $req['billing_date'] . $req['billing_month'] . ".csv";
        // $value["encoded_csv"]  = base64_encode( mb_convert_encoding($encoded_csv , "SJIS") );
        $value["encoded_csv"]  = base64_encode( $encoded_csv );
        $value["message"]      = "OK";
        return $value;            
    }


    /**
     * 詳細画面
     */
    public function detail(Request $request)
    {
        Log::debug("ConfirmUsagedataController detail:");
        $req = $request->all();

        // ユーザの閲覧可能に該当しない場合は拒否
        $contract = [];
        $contracts = GetInvoice::get_supplypoint_list(session('user_now.customer_code'));
        // 通信契約
        // $fileMaker = new FileMakerController();
        // $local_file = $fileMaker->downloadForFileMaker('customer_info_read_file/','customer_info_read_file_' . session('user_now.customer_code') . '.csv');
        // if (!empty($local_file)) {
        //     $mobile_contracts = $fileMaker->read_file($local_file);
        //     if (!empty($mobile_contracts)) {
        //         $contracts = array_merge($contracts, $fileMaker->contract_format($mobile_contracts));
        //         $mobile_contracts = $fileMaker->contract_format($mobile_contracts);
        //     }
        // }
        foreach($contracts as $temp_contract){
            if ($temp_contract['supplypoint_code'] == $req['supplypoint_code']) {
                $contract = $temp_contract;
            }
        }
        // 0件 -> 表示エラー
        if (count($contract) == 0) {
            abort(400);
        }
        
        // supplypoint_code 
        // billing_date
        $temp_req = [];
        $temp_req["customer_code"] = $contract["customer_code"];
        $temp_req["supplypoint_code"] = $req["supplypoint_code"];
        $temp_req["date"]     = $req["date"];
        Log::debug($temp_req);

        $result = GetInvoice::get_billing_detail($temp_req);
        if ($result["status"] == false) {
            abort(400);
        }

        $service = null;
        if ($contract["pps_type"] == SupplierConsts::GRANDATA_ELECTRIC_INDEX || $contract["pps_type"] == SupplierConsts::HTB_ENERGY_ELECTRIC_INDEX) {
            $service = 'electric';
        } else if ($contract["pps_type"] == SupplierConsts::GRANDATA_GAS_INDEX || $contract["pps_type"] == SupplierConsts::SAISAN_GAS_INDEX || $contract["pps_type"] == SupplierConsts::FAMILY_NET_JAPAN_GAS_INDEX) {
            $service = 'gas';
        } else if (substr($req["supplypoint_code"], 0, 2) == 'GP') {
            $service = 'wifi';

            //モバイル契約データと紐づくレコードはcontractテーブルに存在しない為、ここで格納
            $result["billing"]["contract_name"] = $mobile_contracts[0]["contract_name"];
            $result["billing"]["address"] = $mobile_contracts[0]["address"];
            $result["billing"]["plan"] = $mobile_contracts[0]["plan"];

            // 非表示の内訳を削除
            $tmp_itemize = [];
            foreach ($result["billing_itemize"] as $key => $value) {
                if ($value['itemize_bill'] != 0 && $value['itemize_name'] != '消費税相当額') {
                    array_push($tmp_itemize, $value);
                }
            }
            $result["billing_itemize"] = $tmp_itemize;
        }

        $billing         = $result["billing"];
        $billing_itemize = $result["billing_itemize"];

        // ユーザー情報を取得
        return view('confirm_usagedata_detail')->with("billing",  $billing)
        ->with("billing_itemize",  $billing_itemize)->with("req",$req)
        ->with("service", $service);
    }

    /**
     * 任意の契約の請求データの最古、最新の年月を取得する
     */
    public function billing_range_by_contract($supplypoint_code)
    {
        $customer_code = session('user_now.customer_code');

        $first_billing_date = '';
        $latest_billing_date = '';

        $whereraw = GetInvoice::get_assignment_whereraw($supplypoint_code, $customer_code);
        $results = Billing::where("supplypoint_code", $supplypoint_code)
        ->where('customer_code', $customer_code)
        ->whereRaw($whereraw);

        if ($results->count() > 0) {
            $billing_range = $results->selectRaw('max(usage_date) as latest_billing_date ,min(usage_date) as first_billing_date')->first();
            if (empty($first_billing_date) || $billing_range->first_billing_date < $first_billing_date) {
                $first_billing_date = $billing_range->first_billing_date;
            }
            if (empty($latest_billing_date) || $latest_billing_date < $billing_range->latest_billing_date) {
                $latest_billing_date = $billing_range->latest_billing_date;
            }
        }

        $param = [
            'first_billing_date' => $first_billing_date,
            'latest_billing_date' => $latest_billing_date,
        ];
        return $param;
    }

    /**
     * 明細PDF出力
     */
    public function detail_pdf(Request $request, $supplypoint_code, $date)
    {
        $users = $request->session()->get('user_now', array());
        $user = User::where('customer_code', $users['customer_code'])->first();

        // ユーザの閲覧可能に該当しない場合は拒否
        $contract = [];
        $contracts = GetInvoice::get_supplypoint_list($users['customer_code']);
        foreach($contracts as $temp_contract){
            if ($temp_contract['supplypoint_code'] == $supplypoint_code) {
                $contract = $temp_contract;
            }
        }
        // 0件 -> 表示エラー
        if (count($contract) == 0) {
            abort(400);
        }
        $temp_req = [];
        $temp_req["customer_code"] = $contract["customer_code"];
        $temp_req["supplypoint_code"] = $supplypoint_code;
        $temp_req["date"] = $date;

        $result = GetInvoice::get_billing_detail($temp_req);
        if ($result["status"] == false) {
            abort(400);
        }

        $billing = $result["billing"];
        $billing_itemize = $result["billing_itemize"];
        $billing_itemize = array();
        for ($i = 1; $i <= 20; $i++) {
            if (count($result["billing_itemize"]) > 0) {
                $billing_itemize += array($i => array_shift($result["billing_itemize"]));
            } else {
                $billing_itemize += array($i => "");
            }
        }
        $pdf = \PDF::loadView('usagedata_detail_pdf', ['billing' => $billing, 'billing_itemize' => $billing_itemize, 'contract' => $contract, 'user' => $user])
        ->setOption('encoding', 'utf-8')
        ->setOption('header-font-size', 7)
        ->setOption('header-left', '[G01-01]')
        ->setOption('footer-font-size', 7)
        ->setOption('footer-right', '審査番号:20291231GD9999');
        return $pdf->inline('usagedata_detail.pdf');
    }

}
