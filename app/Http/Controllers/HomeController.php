<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use DateTime;

use App\Http\Controllers\FileMakerController;

// eloquent
use App\Notice;
use App\Billing;

use App\Facades\GetInvoice; // ファサード : 請求書関連共通クラス 供給地点特定番号 supplypoint_code

use App\Http\Controllers\ContractRenewalController;
use App\Http\Controllers\ContractNoticeController;

/**
 * ホーム画面
 */
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        Log::debug("/home");
        // セッションの値を全て取得
        // $data = Session::all();
        // Log::debug($data);

        return view('home')->with("phase", [0,0]);
    }


    /**
     * 一覧取得
     */
    public function getlist(Request $request)
    {
        Log::debug("AdminRegistNoticeController : getlist");
        $req = $request->all();

        // 検索条件デバッグ出力
        Log::debug( "検索条件：" );
        Log::debug($req);

        /** 通知 notice 取得 */
        $notice = [];
        $customer_code = session('user_now.customer_code');
        $results = Notice::leftJoin('notice_relation', function ($join) {
            $join->on('notice_relation.notice_id', 'notice.id')
                 ->whereNull('notice_relation.deleted_at');
        })
        ->where('notice_date', '<=' , DB::raw('now()'))
        ->where(function($query) use($customer_code){
            $query->whereNull('notice_relation.customer_code')
                  ->orWhere('notice_relation.customer_code',$customer_code);
        });
        // usersの条件での合計数を取得する
        $results_count = $results->count();

        // ページングに関する処理 skip take
        $now_state = 0;
        if ($req["now_state"]) {
            Log::debug("now_state");
            $now_state = $req['now_state'];
            $results = $results->skip($req['now_state']*$req['display_number']);
        }
        if ($req["display_number"]) {
            $results = $results->take($req['display_number']);
        }

        // 一覧取得
        $notice = $results->orderBy('notice_date', 'desc')->get()->toArray();
        Log::debug( $notice );

        $contract = new ContractNoticeController();
        $new_contract = $contract->check_contract_notice($request->session()->get('user_now.customer_code'));
        if (!empty($new_contract)) {
            $contract_notice = [
                'notice_comment' => '契約のお知らせ（契約締結後書面）',
                'url' => route('contract_notice')
            ];
            $notice_date = null;
            foreach ($new_contract as $value) {
                if (empty($notice_date)) {
                    $notice_date = $value['delivery_date'];
                } elseif ($notice_date < $value['delivery_date']){
                    $notice_date = $value['delivery_date'];
                }
            }
            $contract_notice['notice_date'] = $notice_date->format('Y-m-d');
            array_unshift($notice, $contract_notice);
            array_pop($notice);
        }

        $contract_notice = null;
        $renewal = new ContractRenewalController();
        $renewal_contract = $renewal->check_contract_renewal($request->session()->get('user_now.customer_code'));
        if (!empty($renewal_contract)) {
            $contract_notice = [
                'notice_comment' => '契約更新のお知らせ',
                'url' => route('contract_renewal')
            ];
            $notice_date = null;
            foreach ($renewal_contract as $value) {
                if (empty($notice_date)) {
                    $notice_date = $value['delivery_date'];
                } elseif ($notice_date < $value['delivery_date']){
                    $notice_date = $value['delivery_date'];
                }
            }
            $contract_notice['notice_date'] = $notice_date->format('Y-m-d');
            array_unshift($notice, $contract_notice);
            array_pop($notice);
        }

        // 戻り値に割り当てられた 通知 一覧 を渡す。
        $value = [];
        $value["status"]  = "200";
        $value["notice"]   = $notice;
        $value["notice_counts"]   = $results_count;
        $value["now_state"]      = $now_state;
        $value["message"] = "OK";
        return $value;    

    }

    /**
     * ご請求金額
     * ご請求金額のAPI値を返す。他画面でも、たとえば、"請求金額・使用量確認" 画面では有用。  
     */
    public function billing_amount(Request $request)
    {
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

        // 一覧から有効な請求を調べて値をまとめる。
        // 使用率 usage_t 取得
        $usage_t = [];

        // 年度の範囲を求める式 usage_t の usage_dateは、201901 のような年月を数字で直列に並べた値

        // $billing_date = date("Ym");
        // $billing_date = "201901";
        $billing_date = $request["billing_date"];

        // $date_number        = 20190300;
        // $date_number        = floor(date("Ym")) * 100;
        // $date_number        = floor($billing_date) * 100;
        
        $start_date = "";
        $end_date   = "";

        /** ご請求金額 */
        $billing_amount_total = 0;
        // billingレコード数
        $billing_count = 0;

        // Log::debug( $date_number );

        // billingテーブルより、有効な項目を確定して集計する。
        foreach ($contracts as $key => $temp_data) {
            // Log::debug($temp_data);
            $supplypoint_code = $temp_data["supplypoint_code"];
            $customer_code = $temp_data["customer_code"];
            Log::debug("supplypoint_code : " . $supplypoint_code);
            // 譲渡データの一覧を元に除外、追加を実施する
            $whereraw = GetInvoice::get_assignment_whereraw( $supplypoint_code , session('user_now.customer_code') );

            $results = Billing::where("supplypoint_code", $supplypoint_code)
            ->where('customer_code', $customer_code)
            ->whereRaw($whereraw);
            $billing_count = $billing_count + $results->count();
            $contract_billing_count = $results->count();
            $results->where('billing_date', $billing_date);
            
            // 金額を集計
            $contract_billing_amount = 0;
            Log::debug($results->get()->toArray());
            foreach ($results->get() as $temp_value) {
                Log::debug($temp_value["billing_amount"]);
                $billing_amount_total = $billing_amount_total + $temp_value["billing_amount"];
                $contract_billing_amount = $contract_billing_amount + $temp_value["billing_amount"];
            }
            $contracts[$key] += ['contract_billing_count' => $contract_billing_count];
            $contracts[$key] += ['contract_billing_amount' => $contract_billing_amount];
        }

        // 値をまとめる
        //$period = " 2019年06月(4/03～5/07ご利用分)";
        $w_start_date = str_replace("-", "/", substr( $start_date , 5, 5) );
        $w_end_date   = str_replace("-", "/", substr( $end_date , 5, 5) );
        $period = " " .  substr( $billing_date , 0, 4) . "年" 
                . substr( $billing_date , 4, 2) . "月";
                // . "(". $w_start_date . "～" . $w_end_date . "ご利用分)";
        $claim = substr( $billing_date , 0, 4) . "年" . intval(substr( $billing_date , 4, 2)) . "月請求分";

        //$total  = "4,370円";
        $total  = number_format($billing_amount_total) . "円";

        // 戻り値に ご請求金額 を渡す。
        $value = [];
        $value["status"]  = "200";
        $value["period"] = $period;
        $value["total"]  = $total;
        $value["billing_date"] = $billing_date;
        $value["start_date"] = $w_start_date;
        $value["end_date"]   = $w_end_date;
        $value["billing_amount_total"] = $billing_amount_total;
        $value["message"] = "OK";
        $value["billing_count"] = $billing_count;
        $value["contracts"] = $contracts;
        $value["claim"] = $claim;
        Log::debug($value);
        return $value;

    }

    /**
     * 請求データの最古、最新の年月を取得する
     */
    public function billing_range(Request $request)
    {
        $customer_code = session('user_now.customer_code');
        // 契約一覧を取得
        $contracts = GetInvoice::get_supplypoint_list($customer_code);

        $first_billing_date = '';
        $latest_billing_date = '';
        foreach ($contracts as $contract) {
            // 請求データ取得クエリ
            $whereraw = GetInvoice::get_assignment_whereraw($contract['supplypoint_code'], $contract['customer_code']);
            $results = Billing::where("supplypoint_code", $contract['supplypoint_code'])
            ->where('customer_code', $contract['customer_code'])
            ->whereRaw($whereraw);
            // 請求データが１件以上あるとき、請求年月の最古と最新を取得
            if ($results->count() > 0) {
                $billing_range = $results->selectRaw('max(billing_date) as latest_billing_date ,min(billing_date) as first_billing_date')->first();
                if (empty($first_billing_date) || $billing_range->first_billing_date < $first_billing_date) {
                    $first_billing_date = $billing_range->first_billing_date;
                }
                if (empty($latest_billing_date) || $latest_billing_date < $billing_range->latest_billing_date) {
                    $latest_billing_date = $billing_range->latest_billing_date;
                }
            }
        }

        $param = [
            'first_billing_date' => $first_billing_date,
            'latest_billing_date' => $latest_billing_date,
        ];
        return $param;
    }
}
