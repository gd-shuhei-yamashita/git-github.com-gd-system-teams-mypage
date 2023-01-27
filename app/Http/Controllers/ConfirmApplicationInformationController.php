<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// eloquent
use App\Contract; 
use App\Assignment;

use App\Facades\GetInvoice; // ファサード : 請求書関連共通クラス 供給地点特定番号 supplypoint_code
use App\Http\Controllers\OptionPlanCloseController;
use App\Http\Controllers\FileMakerController;


use App\Http\Controllers\ContractNoticeController;

/**
 *   契約情報の確認
 */
class ConfirmApplicationInformationController extends Controller
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
     * ユーザが見る権限のある 供給地点特定番号 supplypoint_code 一覧を正しく取得する
     */
    protected function get_supplypoint_list($customer_code)
    {
        // 譲渡データをもとに
        // ToDo:譲渡データの一覧を元に除外、追加を実施する
        // 一覧取得 （アルゴリズムは同様）
        $results = Assignment::where('assignment_after_customer_code', $customer_code)->
        orderBy('assignment_date', 'asc')->get();
        Log::debug( $results );   

        $supplypoint = [];
        foreach ($results as $temp_assignment) {
            $supplypoint[] = $temp_assignment["supplypoint_code"];
        }


        // 契約データ contract から 使用場所読み込み
        $contracts = [];
        
        $req1 = session('user_now.customer_code');
        // $req2 = ['9999999999999999999003'];

        // 必ずセッションのキーを元に出す 
        // 無名関数利用
        // ex. Laravel5で「.. or ...) and (..」みたいな複雑な条件を書く  
        // https://qiita.com/Hwoa/items/542456b63e51895f9a55
        $results = Contract::where("customer_code", $req1)
        ->orWhere(function ($query) use ( $supplypoint ) {
            $query->WhereIn("supplypoint_code", $supplypoint);
        })
        ->orderBy('supplypoint_code', 'asc')->get();
        Log::debug( $results );

        foreach ($results as $temp_contract) {
            $contracts[] = [
            "customer_code"       => $temp_contract->customer_code,
            "supplypoint_code"    => $temp_contract->supplypoint_code,
            "contract_name"       => $temp_contract->contract_name,
            "address"             => $temp_contract->address,
            "plan"                => $temp_contract->plan,
            "shop_name"           => $temp_contract->shop_name,
            ];
        }

        return $contracts;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        // 契約データ contract から 使用場所読み込み
        $contracts = [];

        // 関数より使用場所一覧を取得する
        // $contracts = $this->get_supplypoint_list( session('user_now.customer_code') );
        $contracts = GetInvoice::get_supplypoint_list(session('user_now.customer_code'));
            
        // // 必ずセッションのキーを元に出す
        // $results = Contract::where("customer_code", session('user_now.customer_code'))->get();
        // Log::debug( $results );

        // foreach ($results as $temp_contract) {
        //     $contracts[] = [
        //         "customer_code"       => $temp_contract->customer_code,
        //         "supplypoint_code"    => $temp_contract->supplypoint_code,
        //         "contract_name"       => $temp_contract->contract_name,
        //         "address"             => $temp_contract->address,
        //         "plan"                => $temp_contract->plan,
        //         "shop_name"           => $temp_contract->shop_name,
        //     ];
        // }

        // 通信契約
        $fileMaker = new FileMakerController();
        $local_file = $fileMaker->downloadForFileMaker('customer_info_read_file/','customer_info_read_file_' . session('user_now.customer_code') . '.csv');
        if (!empty($local_file)) {
            $mobile_contracts = $fileMaker->read_file($local_file);
            if (!empty($mobile_contracts)) {
                $contracts = array_merge($contracts, $fileMaker->contract_format($mobile_contracts));
            }
        }

        // オプション情報の取得
        $option_plan_close = new OptionPlanCloseController();
        foreach ($contracts as $key => $contract) {
            $option = $option_plan_close->get_option($contract['supplypoint_code'], session('user_now.customer_code'));
            if (empty($option)) {
                continue;
            } else {
                $contracts[$key]['option'] = $option;
            }
        }

        // サンキューレター表示チェック
        $contract_notice = new ContractNoticeController();
        $check_contract = $contract_notice->check_thankyou_letter_exists($request->session()->get('user_now.customer_code'));
        if (!empty($check_contract)) {
            $supplypoints = [];
            foreach ($check_contract as $value) {
                $supplypoints[] = $value->power_customer_location_number;
            }
        }
        foreach ($contracts as $key => $contract) {
            if(in_array($contract["supplypoint_code"], $supplypoints)) {
                $contracts[$key]["thankyou_letter"] = true;

                // 付帯チェック
                $contracts[$key]["futai_premiumu"] = false;
                $contracts[$key]["futai_basic"] = false;
                $contracts[$key]["futai_entame"] = false;
                $contracts[$key]["futai_digital"] = false;
                $contracts[$key]["futai_douga"] = false;
                $contract_plan = $contract_notice->get_mallie_plan($contract["supplypoint_code"], $request->session()->get('user_now.customer_code'));
                foreach ($contract_plan as $plan_name) {
                    if ( strpos($plan_name->power_plan_name, 'ABEMAでんきプレミアムプラン') !== false || strpos($plan_name->power_plan_name, 'Abemaでんきプレミアムプラン') !== false) {
                        $contracts[$key]["futai_premiumu"] = true;
                    }
                    if ( strpos($plan_name->power_plan_name, 'ベーシックプラン') !== false ) {
                        $contracts[$key]["futai_basic"] = true;
                    }
                    if ( strpos($plan_name->power_plan_name, 'エンタメプラン') !== false ) {
                        $contracts[$key]["futai_entame"] = true;
                    }
                    if ( strpos($plan_name->power_plan_name, 'デジタルコンテンツプラン') !== false ) {
                        $contracts[$key]["futai_digital"] = true;
                    }
                    if ( strpos($plan_name->power_plan_name, '動画プラン（U') !== false ) {
                        $contracts[$key]["futai_douga"] = true;
                    }
                }
            } else {
                $contracts[$key]["thankyou_letter"] = false;
            }
        }

        return view( 'confirm_application_information', [ "contracts" => $contracts ] );
    }

}
