<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

// eloquent
use App\Notice;
use App\Billing;

use App\Facades\GetInvoice; // ファサード : 請求書関連共通クラス 供給地点特定番号 supplypoint_code
use App\Consts\HalueneContractOptionPlanConsts;

/**
 * オプション解約画面
 */
class OptionPlanCloseController extends Controller
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

    public function index(Request $request, $option_contract_id)
    {
        //オプションの取得
        $option = $this->check_option_update($option_contract_id, session('user_now.customer_code'));
        if (empty($option)) {
            return view('option_plan_close');
        }

        $data = array();
        $data['name'] = $option->name;
        $data['contract_date'] = !empty($option->contract_date) ? date('Y年m月d日', strtotime($option->contract_date)) : '';
        $data['close_date'] = !empty($option->close_date) ? date('Y年m月d日', strtotime($option->close_date)) : '';
        $data['close_reason_list'] = HalueneContractOptionPlanConsts::CLOSE_REASON_LIST;
        $data['option_contract_id'] = $option_contract_id;

        return view('option_plan_close')->with("data", $data);
    }

    /**
     * 供給地点特定番号からオプション情報を取得
     */
    public function get_option($supplypoint_code, $customer_code)
    {
        //MallieDBからオプション情報を取得するクエリ文
        $option_query = DB::connection('mysql_mallie')->table('HalueneOptionPlan AS hop')
        ->join('HalueneContractOptionPlan AS hcop', 'hcop.option_plan_id', '=', 'hop.id')
        ->join('HalueneContract AS hc', 'hc.id', '=', 'hcop.contract_id')
        ->join('CustomerOrdered AS co', 'co.id', '=', 'hc.customer_id')
        ->where('hc.power_customer_location_number', $supplypoint_code)
        ->where('co.code', $customer_code)
        ->where('hc.status', '!=', 2)
        ->where('hop.include_flag', '!=', 1)
        ->where('hcop.status', '!=', 2)
        ->select('hcop.id', 'hop.name', 'hcop.status');

        if ($option_query->count() < 1) {
            return null;
        } else {
            return $option_query->get();
        }
    }

    /**
     * オプション解約ページの閲覧可否チェック
     */
    public function check_option_update($option_contract_id, $customer_code)
    {
        // MallieDBからオプション情報取得
        $option = DB::connection('mysql_mallie')->table('HalueneOptionPlan AS hop')
        ->join('HalueneContractOptionPlan AS hcop', 'hcop.option_plan_id', '=', 'hop.id')
        ->join('HalueneContract AS hc', 'hc.id', '=', 'hcop.contract_id')
        ->join('CustomerOrdered AS co', 'co.id', '=', 'hc.customer_id')
        ->where('hcop.id', $option_contract_id)
        ->where('hc.status', '!=', 2)
        ->where('hop.include_flag', '!=', 1)
        ->whereNotIn('hcop.status', [2,3])
        ->where('co.code', $customer_code)
        ->select('hcop.id', 'hop.name', 'hcop.contract_date', 'hcop.close_date', 'hc.power_customer_location_number')
        ->first();

        if (empty($option)) {
            return null;
        }

        // ユーザの閲覧できる契約
        $contracts = GetInvoice::get_supplypoint_list($customer_code);
        $supplypoint = array();
        foreach ($contracts as $contract) {
            $supplypoint[] = $contract["supplypoint_code"];
        }

        // 閲覧できる契約に紐づくオプションの場合、オプション情報を返す
        if (in_array($option->power_customer_location_number, $supplypoint, true)) {
            return $option;
        } else {
            return null;
        }
    }

    public function confirm(Request $request)
    {
        $rules = [
            'close_reason'  => 'required',
            'option_contract_id' => 'required|numeric|digits_between:1,11',
            'close_reason_other' => 'nullable',
        ];
        $messages = [
            'close_reason.required'  => "ご解約理由を選択してください。",
            'option_contract_id.*' => 'オプション契約IDが不正です',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }
        $params = $validator->validated();
        $option_contract_id = $params['option_contract_id'];
        $close_reason = $params['close_reason'];
        $close_reason_other = empty($params['close_reason_other']) ? '' : $params['close_reason_other'];

        // その他ならば理由必須
        if ($close_reason == 99) {
            $validator = Validator::make($request->only(['close_reason_other']), ['close_reason_other'  => 'required'], ['close_reason_other.required'  => " その他を選択した場合、理由をご記入ください。"]);
            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            }
        }

        // 解約できる権限があるかチェック
        $option = $this->check_option_update($option_contract_id, session('user_now.customer_code'));
        if (empty($option)) {
            return back()->withInput()->withErrors(['option_contract_id' => 'オプション契約IDが不正です']);
        }

        //MallieDBを解約でアップデート
        $option = DB::connection('mysql_mallie')->table('HalueneContractOptionPlan')
        ->where('id', '=', $option_contract_id)
        ->update([
            'status' => 3,
            'close_date' => now(),
            'close_reason' => $close_reason,
            'close_reason_others' => 'マイページより解約：' . $close_reason_other,
            'updater' => 'MYPAGE',
            'updatedate' => now()
        ]);

        return view('option_plan_close')->with("data", ['complete' => true]);
    }
}
