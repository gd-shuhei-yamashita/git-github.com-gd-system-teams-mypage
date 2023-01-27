<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use DateTime;

use App\Facades\GetInvoice;
use App\Contract;

class ContractRenewalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // ユーザー情報を取得
        $users = $request->session()->get('user_now', array());

        $renewal_contract = $this->check_contract_renewal($users['customer_code']);
        if (empty($renewal_contract)) {
            return view('contract_renewal');
        }
        $data = array();
        $data['before_flag'] = false;
        $data['after_flag'] = false;
        foreach ($renewal_contract as $value) {
            if ($value['date_status'] == 'before') {
                $data['before_flag'] = true;
                $data['before_delivery_date'] = $value['delivery_date']->format('Y年m月d日');
            } elseif ($value['date_status'] == 'after') {
                $data['after_flag'] = true;
                $data['after_delivery_date'] = $value['delivery_date']->format('Y年m月d日');
            }
        }
        return view('contract_renewal', ["data" => $data]);
    }

    /**
     * お知らせ期間内の契約の取得
     */
    public function check_contract_renewal($customer_code)
    {
        // ユーザがマイページで見る権限のある契約を取得
        $contracts = GetInvoice::get_supplypoint_list($customer_code);
        $result = [];
        foreach ($contracts as $key => $contract) {
            if (!empty($contract['switching_scheduled_date'])) {
                // 実行日の昨年、今年、来年のスイッチング日の範囲でチェック（年跨ぎ考慮）
                for ($i = -1; $i <= 1; $i++) {
                    $month = date('m', strtotime($contract['switching_scheduled_date']));
                    $day = date('d', strtotime($contract['switching_scheduled_date']));
                    //今年のスイッチング予定日
                    $new_switching_scheduled_date = (new DateTime())->setDate(date('Y') + $i, $month, $day)->setTime(0, 0);

                    $one_month_before = (new DateTime())->setDate(date('Y') + $i, $month, $day)->setTime(0, 0)->modify('-31 day');
                    $one_month_after = (new DateTime())->setDate(date('Y') + $i, $month, $day)->setTime(0, 0)->modify('+31 day');
                    $now = (new DateTime())->setTime(0, 0);
                    if ($one_month_before <= $now && $now < $new_switching_scheduled_date) {
                        $contract['date_status'] = 'before';
                        $contract['new_switching_scheduled_date'] = $new_switching_scheduled_date;
                        $contract['delivery_date'] = $one_month_before;
                        $result[] = $contract;
                        break;
                    } elseif ($new_switching_scheduled_date <= $now && $now < $one_month_after) {
                        $contract['date_status'] = 'after';
                        $contract['new_switching_scheduled_date'] = $new_switching_scheduled_date;
                        $contract['delivery_date'] = $new_switching_scheduled_date;
                        $result[] = $contract;
                        break;
                    }
                }
            }
        }
        return $result;
    }

    public function detail(Request $request, $supplypoint_code)
    {
        // ユーザー情報を取得
        $users = $request->session()->get('user_now', array());

        if ($supplypoint_code == 'none') {
            $supplypoint_code = '';
        }

        // MallieDBから契約情報取得
        $mallie_result = DB::connection('mysql_mallie')->table('HalueneContract AS hc')
        ->join('CustomerOrdered AS co', 'hc.customer_id', '=', 'co.id')
        ->where('hc.power_customer_location_number', $supplypoint_code)
        ->where('co.code', $users['customer_code'])
        ->where('hc.status', '!=', 2)
        ->select('hc.pps_business_number')
        ->first();

        // 契約情報取得
        $contracts = GetInvoice::get_supplypoint_list($users['customer_code']);
        foreach ($contracts as $contract) {
            if ($contract['supplypoint_code'] == $supplypoint_code) {
                $result = $contract;
                break;
            }
        }
        // 契約の詳細が取得できない場合は情報なしで表示
        if (empty($mallie_result) || empty($result['switching_scheduled_date'])) {
            return view('contract_renewal_detail');
        }

        $data = [];
        $data['supplypoint_code'] = $supplypoint_code;
        $data['pps_business_number'] = $mallie_result->pps_business_number;
        $data['plan'] = $result['plan'];
        $data['switching_scheduled_date'] = date('Y年m月d日', strtotime($result['switching_scheduled_date']));
        if (date('Y', strtotime($result['switching_scheduled_date'])) == date('Y')) {
            $data['next_contract_start'] = (new DateTime())->setDate(date('Y') + 1, date('m', strtotime($result['switching_scheduled_date'])), date('d', strtotime($result['switching_scheduled_date'])))->setTime(0, 0)->format('Y年m月d日');
            $data['next_contract_end'] = (new DateTime())->setDate(date('Y') + 2, date('m', strtotime($result['switching_scheduled_date'])), date('d', strtotime($result['switching_scheduled_date'])))->setTime(0, 0)->modify('-1 day')->format('Y年m月d日');
        } else {
            $data['next_contract_start'] = (new DateTime())->setDate(date('Y'), date('m', strtotime($result['switching_scheduled_date'])), date('d', strtotime($result['switching_scheduled_date'])))->setTime(0, 0)->format('Y年m月d日');
            $data['next_contract_end'] = (new DateTime())->setDate(date('Y') + 1, date('m', strtotime($result['switching_scheduled_date'])), date('d', strtotime($result['switching_scheduled_date'])))->setTime(0, 0)->modify('-1 day')->format('Y年m月d日');
        }
        return view('contract_renewal_detail', [ "data" => $data]);
    }
}
