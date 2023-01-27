<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use DateTime;

use App\Facades\GetInvoice;
use App\Contract;
use App\Consts\BrandConsts;
use App\Consts\SupplierConsts;
use App\Consts\HalueneOptionPlanConsts;

class ContractNoticeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // ユーザー情報を取得
        $users = $request->session()->get('user_now', array());

        $contract = $this->check_contract_notice($users['customer_code']);
        if (empty($contract)) {
            return view('contract_notice');
        }
        $data = array();
        $notice_date = null;
        foreach ($contract as $value) {
            if (empty($notice_date)) {
                $notice_date = $value['delivery_date'];
            } elseif ($notice_date < $value['delivery_date']){
                $notice_date = $value['delivery_date'];
            }
        }
        $data['delivery_date'] = $notice_date->format('Y年m月d日');
        return view('contract_notice', ["data" => $data]);
    }

    /**
     * お知らせ期間内の契約の取得
     */
    public function check_contract_notice($customer_code)
    {
        $thankyou_letter_contracts = $this->check_thankyou_letter_exists($customer_code);

        $result = [];
        foreach ($thankyou_letter_contracts as $key => $contract) {
            $thank_you_letter_noticed_date = new DateTime(date('Y-m-d', strtotime($contract->thank_you_letter_noticed_date)));
            $three_month_after = (new DateTime(date('Y-m-d', strtotime($contract->thank_you_letter_noticed_date))))->modify('+93 day');
            $now = (new DateTime())->setTime(0, 0);
            if ($thank_you_letter_noticed_date <= $now && $now < $three_month_after) {
                $result[] = [
                    'delivery_date' => $thank_you_letter_noticed_date,
                    'supplypoint_code' => $contract->power_customer_location_number
                ];
                break;
            }
        }
        return $result;
    }

    public function check_thankyou_letter_exists($customer_code)
    {
        // ユーザがマイページで見る権限のある契約を取得
        $contracts = GetInvoice::get_supplypoint_list($customer_code);

        $supplypoints = [];
        foreach ($contracts as $contract) {
            $supplypoints[] = $contract["supplypoint_code"];
        }
        // MallieDBから契約情報取得
        $mallie_result = DB::connection('mysql_mallie')->table('HalueneContract AS hc')
        ->join('CustomerOrdered AS c', 'hc.customer_id', 'c.id')
        ->whereIn('hc.power_customer_location_number', $supplypoints)
        ->whereNotNull('hc.thank_you_letter_noticed_date')
        ->where('c.code', $customer_code)
        ->where('hc.status', 1)
        ->select('hc.thank_you_letter_noticed_date', 'hc.power_customer_location_number')
        ->get();

        return $mallie_result;
    }

    /**
     * 通知対象のサンキューレターがある顧客を取得
     */
    public function get_users_thankyou_letter_notice()
    {
        // MallieDBから契約情報取得
        $mallie_query = DB::connection('mysql_mallie')->table('HalueneContract AS hc')
        ->whereRaw('DATE_FORMAT(hc.thank_you_letter_noticed_date, "%Y-%m-%d") = DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY), "%Y-%m-%d")')
        ->where('hc.status', 1)
        ->select('hc.power_customer_location_number');

        if ($mallie_query->count() < 1) {
            return null;
        }

        $mallie_contract = $mallie_query->get();
        $supplypoints = [];
        foreach ($mallie_contract as $value) {
            $supplypoints[] = $value->power_customer_location_number;
        }
        // マイページDBから対象の供給地点番号のユーザを取得
        $mypage_query = DB::table('users AS u')
        ->join('contract AS c', 'c.customer_code', 'u.customer_code')
        ->where(function ($query) use ( $supplypoints ) {
            $query->whereIn("c.supplypoint_code", $supplypoints);
        });

        $result = $mypage_query->get();

        return $result;
    }

    public function output_thankyou_letter(Request $request, $supplypoint_code)
    {
        $users = $request->session()->get('user_now', array());

        if ( empty($users['customer_code']) ) {
            return back();
        }

        if ($supplypoint_code == 'none') {
            $supplypoint_code = '';
        }

        $result = $this->get_thanyou_letter_params($supplypoint_code, $users['customer_code']);
        $contract = null;
        $options = array();
        foreach ($result as $value) {
            if (empty($contract)) {
                $contract = $value;
            }
            if (!empty($value->option_name)) {
                if(!empty($value->about_billing_text) && mb_strlen($value->about_billing_text) > 50) {
                    $text_array = array();
                    for ($i = 0; $i < mb_strlen($value->about_billing_text); $i = $i +  50 ) {
                    array_push($text_array, mb_substr($value->about_billing_text, $i, 50));
                    }
                    $value->about_billing_text = $text_array;
                }
                array_push($options, $value);
            }
        }

        // ブランド取得
        $brand = '';
        foreach (BrandConsts::BRAND_LIST as $value) {
            if (strpos($contract->power_plan_name, $value[BrandConsts::BRAND_NAME_INDEX]) !== false) {
                $brand = $value;
                break;
            }
        }

        // 小売事業者取得
        $supplier = null;
        $add_supplier = null;
        $supplier = SupplierConsts::SUPPLIER_LIST[$contract->pps_business_number];
        $add_supplier_list = [ SupplierConsts::FAMILY_NET_JAPAN_GAS, SupplierConsts::SAISAN_GAS, SupplierConsts::HTB_ENERGY_ELECTRIC ];
        if (in_array($contract->pps_business_number, $add_supplier_list)) {
            $add_supplier = SupplierConsts::SUPPLIER_LIST[SupplierConsts::GRANDATA_ELECTRIC];
        }

        $pdf = \PDF::loadView('thankyou_letter', ['contract' => $contract, 'options' => $options, 'brand' => $brand, 'supplier' => $supplier, 'add_supplier' => $add_supplier])
        ->setOption('encoding', 'utf-8')
        ->setOption('header-font-size', 7)
        ->setOption('header-left', '[A07-09]')
        ->setOption('margin-bottom', 1)  
        ->setOption('header-right', '[page] / [topage]');
        return $pdf->inline('thankyou_letter.pdf');
    }

    /**
     * サンキューレターに表示するプラン名取得
     */
    public function get_mallie_plan($supplypoint_code = null, $customer_code = null)
    {
        $query = DB::connection('mysql_mallie')->table('HalueneContract AS HC');
        $query->join('CustomerOrdered AS C', 'HC.customer_id', 'C.id');
        $query->join('HaluenePowerPlan AS HPP', 'HC.power_plan_id', 'HPP.id');
        $query->select('HPP.name_printed as power_plan_name');
        $query->where('HC.status', 1);
        if (!empty($supplypoint_code)) {
            $query->where('HC.power_customer_location_number', $supplypoint_code);
        }
        if (!empty($customer_code)) {
            $query->where('C.code', $customer_code);
        }
        $result = $query->get();
        return $result;
    }

    public function get_thanyou_letter_params($supplypoint_code = null, $customer_code = null)
    {
        $query = DB::connection('mysql_mallie')->table('HalueneContract AS HC');
        // テーブル結合
        $query->join('CustomerOrdered AS C', 'HC.customer_id', 'C.id');
        $query->join('PaymentOrdered AS P', 'HC.payment_id', 'P.id');
        $query->leftJoin('HaluenePowerPlan AS HPP', 'HC.power_plan_id', 'HPP.id');
        $query->leftJoin('HaluenePowerPlanPrice AS HPPP', 'HPP.power_plan_price_id', 'HPPP.id');
        $query->leftJoin('HaluenePowerSupplyCompany AS HPSC', 'HPPP.power_supply_company_id', 'HPSC.id');
        $query->leftJoin('HalueneAbemaCoupon AS HAC', 'HAC.code', 'HC.code');
        $query->leftJoin('HalueneShop AS HS', 'HS.id', 'HC.shop_id');
        $query->leftJoin('HalueneSalesStaff AS HSSK', 'HSSK.id', 'HC.sales_staff_id_kakutoku');
        $query->leftJoin('HalueneSalesStaff AS HSS', 'HSS.id', 'HC.agent_staff_id');
        $query->leftJoin('HalueneCallCenter AS HCC', 'HCC.id', 'HS.call_center_id');
        $query->leftJoin('HalueneContractOptionPlan AS HCOP', function ($join) {
            $join->on('HC.id', 'HCOP.contract_id');
            $join->whereNotIn('HCOP.status', [2, 3]);
        });
        $query->leftJoin('HalueneOptionPlan AS HOP', 'HCOP.option_plan_id', 'HOP.id');
        // オプションサブクエリ
        // 電気量のお知らせ
        $subQueryHCOP1 = DB::connection('mysql_mallie')->table('HalueneOptionPlan AS HOP1');
        $subQueryHCOP1->join('HalueneContractOptionPlan AS H1', function ($join) {
            $join->on('HOP1.id', 'H1.option_plan_id');
        });
        $subQueryHCOP1->where('H1.option_plan_id', HalueneOptionPlanConsts::ID_DENKIRYO_OSHIRASE);
        $subQueryHCOP1->whereNotIn('H1.status', [2, 3]);
        $subQueryHCOP1->select('HOP1.name_printed AS option_name_1');
        $subQueryHCOP1->addSelect('HOP1.payment_type AS option_payment_type_1');
        $subQueryHCOP1->selectRaw('HOP1.price * 1.1 AS option_price_1');
        $subQueryHCOP1->addSelect('H1.contract_id AS hcop1_contract_id');
        $query->leftJoinSub($subQueryHCOP1->toSql(), 'HCOP1', 'HCOP1.hcop1_contract_id', 'HC.id');
        $query->mergeBindings($subQueryHCOP1);
        // つながる修理サポート（Z）
        $subQueryHCOP5 = DB::connection('mysql_mallie')->table('HalueneOptionPlan AS HOP5');
        $subQueryHCOP5->join('HalueneContractOptionPlan AS H5', function ($join) {
            $join->on('HOP5.id', 'H5.option_plan_id');
        });
        $subQueryHCOP5->whereIn('H5.option_plan_id', [HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_Z, HalueneOptionPlanConsts::ID_KADEN_SYURI_SUPPORT]);
        $subQueryHCOP5->whereNotIn('H5.status', [2, 3]);
        $subQueryHCOP5->select('HOP5.name_printed AS option_name_5');
        $subQueryHCOP5->addSelect('HOP5.payment_type AS option_payment_type_5');
        $subQueryHCOP5->selectRaw('HOP5.price * 1.1 AS option_price_5');
        $subQueryHCOP5->addSelect('H5.contract_id AS hcop5_contract_id');
        $query->leftJoinSub($subQueryHCOP5->toSql(), 'HCOP5', 'HCOP5.hcop5_contract_id', 'HC.id');
        $query->mergeBindings($subQueryHCOP5);
        // つながる修理サポート（M）
        $subQueryHCOP7 = DB::connection('mysql_mallie')->table('HalueneOptionPlan AS HOP7');
        $subQueryHCOP7->join('HalueneContractOptionPlan AS H7', function ($join) {
            $join->on('HOP7.id', 'H7.option_plan_id');
        });
        $subQueryHCOP7->whereIn('H7.option_plan_id', [HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_M, HalueneOptionPlanConsts::ID_MOBILE_SYURI_SUPPORT, HalueneOptionPlanConsts::ID_MOBILE_SYURI_SUPPORT_PULS]);
        $subQueryHCOP7->whereNotIn('H7.status', [2, 3]);
        $subQueryHCOP7->select('HOP7.name_printed AS option_name_8');
        $subQueryHCOP7->addSelect('HOP7.payment_type AS option_payment_type_8');
        $subQueryHCOP7->selectRaw('HOP7.price * 1.1 AS option_price_8');
        $subQueryHCOP7->addSelect('H7.contract_id AS hcop11_contract_id');
        $query->leftJoinSub($subQueryHCOP7->toSql(), 'HCOP7', 'HCOP7.hcop11_contract_id', 'HC.id');
        $query->mergeBindings($subQueryHCOP7);
        // music.jp動画コース
        $subQueryHCOP11 = DB::connection('mysql_mallie')->table('HalueneOptionPlan AS HOP11');
        $subQueryHCOP11->join('HalueneContractOptionPlan AS H11', function ($join) {
            $join->on('HOP11.id', 'H11.option_plan_id');
        });
        $subQueryHCOP11->whereIn('H11.option_plan_id', [HalueneOptionPlanConsts::ID_MUSICJP_DOUBGA_COURSE]);
        $subQueryHCOP11->whereNotIn('H11.status', [2, 3]);
        $subQueryHCOP11->select('HOP11.name_printed AS option_name_12');
        $subQueryHCOP11->addSelect('HOP11.payment_type AS option_payment_type_12');
        $subQueryHCOP11->selectRaw('HOP11.price * 1.1 AS option_price_12');
        $subQueryHCOP11->addSelect('H11.contract_id AS hcop15_contract_id');
        $subQueryHCOP11->addSelect('H11.music_option_id AS music_movie_option_id');
        $query->leftJoinSub($subQueryHCOP11->toSql(), 'HCOP11', 'HCOP11.hcop15_contract_id', 'HC.id');
        $query->mergeBindings($subQueryHCOP11);
        // music.jp漫画コース
        $subQueryHCOP12 = DB::connection('mysql_mallie')->table('HalueneOptionPlan AS HOP12');
        $subQueryHCOP12->join('HalueneContractOptionPlan AS H12', function ($join) {
            $join->on('HOP12.id', 'H12.option_plan_id');
        });
        $subQueryHCOP12->whereIn('H12.option_plan_id', [HalueneOptionPlanConsts::ID_MUSICJP_MANGA_COURSE]);
        $subQueryHCOP12->whereNotIn('H12.status', [2, 3]);
        $subQueryHCOP12->select('HOP12.name_printed AS option_name_13');
        $subQueryHCOP12->addSelect('HOP12.payment_type AS option_payment_type_13');
        $subQueryHCOP12->selectRaw('HOP12.price * 1.1 AS option_price_13');
        $subQueryHCOP12->addSelect('H12.contract_id AS hcop16_contract_id');
        $subQueryHCOP12->addSelect('H12.music_option_id AS music_comic_option_id');
        $query->leftJoinSub($subQueryHCOP12->toSql(), 'HCOP12', 'HCOP12.hcop16_contract_id', 'HC.id');
        $query->mergeBindings($subQueryHCOP12);
        // 付帯系つながる修理サポート
        $subQueryFUTAI2 = DB::connection('mysql_mallie')->table('HalueneContractOptionPlan AS H6');
        $subQueryFUTAI2->whereIn('H6.option_plan_id', [3, 7, 8]);
        $subQueryFUTAI2->whereNotIn('H6.status', [2, 3]);
        $subQueryFUTAI2->select('H6.option_plan_id');
        $subQueryFUTAI2->addSelect('H6.contract_id AS hcop6_contract_id');
        $subQueryFUTAI2->selectRaw(
            'CASE
            WHEN H6.option_plan_id = ' . HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_S . '
            THEN ' . HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_S . '
            WHEN H6.option_plan_id = ' . HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_N . '
            THEN ' . HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_N . '
            WHEN H6.option_plan_id = ' . HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_S2 . '
            THEN ' . HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_S2 . '
            ELSE 0
            END AS "name_printed2"'
        );
        $subQueryFUTAI2->selectRaw(
            'CASE
            WHEN H6.option_plan_id IN (' . HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_S . ',' . HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_N . ',' . HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_S2 . ')
            THEN H6.cp_id_key
            ELSE ""
            END AS "cp_id_key"'
        );
        $query->leftJoinSub($subQueryFUTAI2->toSql(), 'FUTAI2', 'FUTAI2.hcop6_contract_id', 'HC.id');
        $query->mergeBindings($subQueryFUTAI2);
        // 付帯系つながる修理サポートZ
        $subQueryFUTAI6 = DB::connection('mysql_mallie')->table('HalueneContractOptionPlan AS H8');
        $subQueryFUTAI6->whereIn('H8.option_plan_id', [HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_Z, HalueneOptionPlanConsts::ID_KADEN_SYURI_SUPPORT]);
        $subQueryFUTAI6->whereNotIn('H8.status', [2, 3]);
        $subQueryFUTAI6->select('H8.option_plan_id');
        $subQueryFUTAI6->addSelect('H8.contract_id AS hcop8_contract_id');
        $subQueryFUTAI6->selectRaw(
            'CASE
            WHEN H8.option_plan_id = ' . HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_Z . '
            THEN ' . HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_Z . '
            WHEN H8.option_plan_id = ' . HalueneOptionPlanConsts::ID_KADEN_SYURI_SUPPORT . '
            THEN ' . HalueneOptionPlanConsts::ID_KADEN_SYURI_SUPPORT . '
            ELSE 0
            END AS "name_printed6"'
        );
        $subQueryFUTAI6->selectRaw(
            'CASE
            WHEN H8.option_plan_id IN ('
            . HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_Z . ', '
            . HalueneOptionPlanConsts::ID_KADEN_SYURI_SUPPORT .
            ')
            THEN H8.cp_id_key
            ELSE ""
            END AS "cp_id_key_z"'
        );
        $query->leftJoinSub($subQueryFUTAI6->toSql(), 'FUTAI6', 'FUTAI6.hcop8_contract_id', 'HC.id');
        $query->mergeBindings($subQueryFUTAI6);
        // 付帯系つながる修理サポートM
        $subQueryFUTAI9 = DB::connection('mysql_mallie')->table('HalueneContractOptionPlan AS H11');
        $subQueryFUTAI9->whereIn('H11.option_plan_id', [HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_M, HalueneOptionPlanConsts::ID_MOBILE_SYURI_SUPPORT, HalueneOptionPlanConsts::ID_MOBILE_SYURI_SUPPORT_PULS]);
        $subQueryFUTAI9->whereNotIn('H11.status', [2, 3]);
        $subQueryFUTAI9->select('H11.option_plan_id');
        $subQueryFUTAI9->addSelect('H11.contract_id AS hcop11_contract_id');
        $subQueryFUTAI9->selectRaw(
            'CASE
            WHEN H11.option_plan_id = ' . HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_M . '
            THEN ' . HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_M . '
            WHEN H11.option_plan_id = ' . HalueneOptionPlanConsts::ID_MOBILE_SYURI_SUPPORT . '
            THEN ' . HalueneOptionPlanConsts::ID_MOBILE_SYURI_SUPPORT . '
            WHEN H11.option_plan_id = ' . HalueneOptionPlanConsts::ID_MOBILE_SYURI_SUPPORT_PULS . '
            THEN ' . HalueneOptionPlanConsts::ID_MOBILE_SYURI_SUPPORT_PULS . '
            ELSE 0
            END AS "name_printed9"'
        );
        $subQueryFUTAI9->selectRaw(
            'CASE
            WHEN H11.option_plan_id IN ('
            . HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_M . ', '
            . HalueneOptionPlanConsts::ID_MOBILE_SYURI_SUPPORT . ', '
            . HalueneOptionPlanConsts::ID_MOBILE_SYURI_SUPPORT_PULS .
            ')
            THEN H11.cp_id_key
            ELSE ""
            END AS "cp_id_key_m"'
        );
        $query->leftJoinSub($subQueryFUTAI9->toSql(), 'FUTAI9', 'FUTAI9.hcop11_contract_id', 'HC.id');
        $query->mergeBindings($subQueryFUTAI9);
        // 付帯系スマートシネマ
        $subQueryFUTAI5 = DB::connection('mysql_mallie')->table('HalueneContractOptionPlan AS H7');
        $subQueryFUTAI5->join('HalueneOptionPlan AS HOP7', function ($join) {
            $join->on('HOP7.id', 'H7.option_plan_id');
        });
        $subQueryFUTAI5->whereIn('H7.option_plan_id', [
            HalueneOptionPlanConsts::ID_SMART_CINEMA_UNEXT_LITE_PLAN,
            HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_MONTHLY_PLAN,
            HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_OLD,
            HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_NEW,
        ]);
        $subQueryFUTAI5->whereNotIn('H7.status', [2, 3]);
        $subQueryFUTAI5->select('H7.contract_id AS hcop7_contract_id');
        $subQueryFUTAI5->addSelect('H7.option_plan_id');
        $subQueryFUTAI5->selectRaw(
            'CASE
            WHEN H7.option_plan_id = ' . HalueneOptionPlanConsts::ID_SMART_CINEMA_UNEXT_LITE_PLAN . '
            THEN ' . HalueneOptionPlanConsts::ID_SMART_CINEMA_UNEXT_LITE_PLAN . '
            WHEN H7.option_plan_id = ' . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_MONTHLY_PLAN . '
            THEN ' . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_MONTHLY_PLAN . '
            WHEN H7.option_plan_id = ' . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_OLD . '
            THEN ' . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_OLD . '
            WHEN H7.option_plan_id = ' . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_NEW . '
            THEN ' . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_NEW . '
            ELSE 0
            END AS "name_printed5"'
        );
        $subQueryFUTAI5->selectRaw(
            'CASE
            WHEN H7.option_plan_id IN ('
            . HalueneOptionPlanConsts::ID_SMART_CINEMA_UNEXT_LITE_PLAN . ', '
            . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_MONTHLY_PLAN . ', '
            . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_OLD . ', '
            . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_NEW .
            ')
            THEN H7.cp_smart_cinema_id_key
            ELSE ""
            END AS "cp_smart_cinema_id_key"'
        );
        $subQueryFUTAI5->selectRaw(
            'CASE
            WHEN H7.option_plan_id IN ('
            . HalueneOptionPlanConsts::ID_SMART_CINEMA_UNEXT_LITE_PLAN . ', '
            . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_MONTHLY_PLAN . ', '
            . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_OLD . ', '
            . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_NEW .
            ')
            THEN H7.cp_smart_cinema_password_key
            ELSE ""
            END AS "cp_smart_cinema_password_key"'
        );
        $subQueryFUTAI5->selectRaw(
            'CASE
            WHEN H7.option_plan_id IN ('
            . HalueneOptionPlanConsts::ID_SMART_CINEMA_UNEXT_LITE_PLAN . ', '
            . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_MONTHLY_PLAN . ', '
            . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_OLD . ', '
            . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_NEW .
            ')
            THEN H7.cp_smart_cinema_gift_code
            ELSE ""
            END AS "cp_smart_cinema_gift_code"'
        );
        $subQueryFUTAI5->selectRaw(
            'CASE
            WHEN H7.option_plan_id IN ('
            . HalueneOptionPlanConsts::ID_SMART_CINEMA_UNEXT_LITE_PLAN . ', '
            . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_MONTHLY_PLAN . ', '
            . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_OLD . ', '
            . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_NEW .
            ')
            THEN DATE_FORMAT( H7.contract_date, "%Y年%m月%d日")
            ELSE ""
            END AS "start_date_text5"'
        );
        $subQueryFUTAI5->selectRaw(
            'CASE
            WHEN H7.option_plan_id IN ('
            . HalueneOptionPlanConsts::ID_SMART_CINEMA_UNEXT_LITE_PLAN . ', '
            . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_MONTHLY_PLAN . ', '
            . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_OLD . ', '
            . HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_NEW .
            ')
            THEN HOP7.smart_cinema_management_source
            ELSE ""
            END AS "smart_cinema_management_source"'
        );
        $query->leftJoinSub($subQueryFUTAI5->toSql(), 'FUTAI5', 'FUTAI5.hcop7_contract_id', 'HC.id');
        $query->mergeBindings($subQueryFUTAI5);
        // お財布サポートbyえらべる倶楽部
        $subQueryHCOP4 = DB::connection('mysql_mallie')->table('HalueneOptionPlan AS HOP4');
        $subQueryHCOP4->join('HalueneContractOptionPlan AS H4', function ($join) {
            $join->on('HOP4.id', 'H4.option_plan_id');
        });
        $subQueryHCOP4->where('H4.option_plan_id', HalueneOptionPlanConsts::ID_OSAIFU_SUPPORT_ERABERUCLUB);
        $subQueryHCOP4->whereNotIn('H4.status', [2, 3]);
        $subQueryHCOP4->select('HOP4.name_printed AS option_name_4');
        $subQueryHCOP4->addSelect('HOP4.payment_type AS option_payment_type_4');
        $subQueryHCOP4->selectRaw('HOP4.price * 1.1 AS option_price_4');
        $subQueryHCOP4->addSelect('H4.contract_id AS hcop4_contract_id');
        $subQueryHCOP4->addSelect('H4.cp_license_key AS hcop4_cp_license_key');
        $query->leftJoinSub($subQueryHCOP4->toSql(), 'HCOP4', 'HCOP4.hcop4_contract_id', 'HC.id');
        $query->mergeBindings($subQueryHCOP4);
        // ペットハート
        $subQueryFUTAI12 = DB::connection('mysql_mallie')->table('HalueneContractOptionPlan AS H14');
        $subQueryFUTAI12->whereIn('H14.option_plan_id', [HalueneOptionPlanConsts::ID_PET_HEART, HalueneOptionPlanConsts::ID_PET_HEART_HEART_PREMIUM]);
        $subQueryFUTAI12->whereNotIn('H14.status', [2, 3]);
        $subQueryFUTAI12->select('H14.option_plan_id');
        $subQueryFUTAI12->addSelect('H14.contract_id AS hcop14_contract_id');
        $subQueryFUTAI12->selectRaw(
            'CASE
            WHEN H14.option_plan_id = ' . HalueneOptionPlanConsts::ID_PET_HEART . '
            THEN ' . HalueneOptionPlanConsts::ID_PET_HEART . '
            WHEN H14.option_plan_id = ' . HalueneOptionPlanConsts::ID_PET_HEART_HEART_PREMIUM . '
            THEN ' . HalueneOptionPlanConsts::ID_PET_HEART_HEART_PREMIUM . '
            ELSE 0
            END AS "name_printed12"'
        );
        $subQueryFUTAI12->selectRaw(
            'CASE
            WHEN H14.option_plan_id IN (' . HalueneOptionPlanConsts::ID_PET_HEART . ', ' . HalueneOptionPlanConsts::ID_PET_HEART_HEART_PREMIUM . ')
            THEN H14.cp_id_key
            ELSE ""
            END AS "servise_id12"'
        );
        $subQueryFUTAI12->selectRaw(
            'CASE
            WHEN H14.option_plan_id IN (' . HalueneOptionPlanConsts::ID_PET_HEART . ', ' . HalueneOptionPlanConsts::ID_PET_HEART_HEART_PREMIUM . ')
            THEN H14.cp_password_key
            ELSE ""
            END AS "password12"'
        );
        $query->leftJoinSub($subQueryFUTAI12->toSql(), 'FUTAI12', 'FUTAI12.hcop14_contract_id', 'HC.id');
        $query->mergeBindings($subQueryFUTAI12);
        // music.jp動画コース
        $subQueryFUTAI14 = DB::connection('mysql_mallie')->table('HalueneContractOptionPlan AS H16');
        $subQueryFUTAI14->whereIn('H16.option_plan_id', [HalueneOptionPlanConsts::ID_MUSICJP_DOUBGA_COURSE]);
        $subQueryFUTAI14->whereNotIn('H16.status', [2, 3]);
        $subQueryFUTAI14->select('H16.option_plan_id');
        $subQueryFUTAI14->addSelect('H16.contract_id AS hcop16_contract_id');
        $subQueryFUTAI14->addSelect('H16.cp_id_key AS music_movie_account_id');
        $subQueryFUTAI14->addSelect('H16.cp_password_key AS music_movie_account_password');
        $subQueryFUTAI14->addSelect('H16.music_option_id AS music_movie_option_id');
        $subQueryFUTAI14->selectRaw(
            'CASE
            WHEN H16.option_plan_id = ' . HalueneOptionPlanConsts::ID_MUSICJP_DOUBGA_COURSE . '
            THEN ' . HalueneOptionPlanConsts::ID_MUSICJP_DOUBGA_COURSE . '
            ELSE 0
            END AS "name_printed14"'
        );
        $query->leftJoinSub($subQueryFUTAI14->toSql(), 'FUTAI14', 'FUTAI14.hcop16_contract_id', 'HC.id');
        $query->mergeBindings($subQueryFUTAI14);
        // music.jp漫画コース
        $subQueryFUTAI15 = DB::connection('mysql_mallie')->table('HalueneContractOptionPlan AS H17');
        $subQueryFUTAI15->whereIn('H17.option_plan_id', [HalueneOptionPlanConsts::ID_MUSICJP_MANGA_COURSE]);
        $subQueryFUTAI15->whereNotIn('H17.status', [2, 3]);
        $subQueryFUTAI15->select('H17.option_plan_id');
        $subQueryFUTAI15->addSelect('H17.contract_id AS hcop17_contract_id');
        $subQueryFUTAI15->addSelect('H17.cp_id_key AS music_comic_account_id');
        $subQueryFUTAI15->addSelect('H17.cp_password_key AS music_comic_account_password');
        $subQueryFUTAI15->addSelect('H17.music_option_id AS music_comic_option_id');
        $subQueryFUTAI15->selectRaw(
            'CASE
            WHEN H17.option_plan_id = ' . HalueneOptionPlanConsts::ID_MUSICJP_MANGA_COURSE . '
            THEN ' . HalueneOptionPlanConsts::ID_MUSICJP_MANGA_COURSE . '
            ELSE 0
            END AS "name_printed15"'
        );
        $query->leftJoinSub($subQueryFUTAI15->toSql(), 'FUTAI15', 'FUTAI15.hcop17_contract_id', 'HC.id');
        $query->mergeBindings($subQueryFUTAI15);
        // 宛先
        $query->selectRaw(
            'CASE
            WHEN HC.pps_business_number != "' . SupplierConsts::HTB_ENERGY_ELECTRIC . '" THEN C.code
            ELSE HC.subscriber_code
            END AS customer_id'
        );
        $query->selectRaw(
            'CASE
            WHEN P.invoice_address_type = 1
            THEN CONCAT(IFNULL(C.prefecture, ""), IFNULL(C.city, ""), IFNULL(C.town, ""), IFNULL(C.street_number_choume, ""))
            ELSE CONCAT(IFNULL(P.invoice_prefecture, ""), IFNULL(P.invoice_city, ""), IFNULL(P.invoice_town, ""), IFNULL(P.invoice_street_number_choume, ""))
            END AS invoice_address1'
        );
        $query->selectRaw(
            'CASE
            WHEN P.invoice_address_type = 1
            THEN CONCAT(IFNULL(C.street_number_banchi, ""), IF(CHAR_LENGTH(C.building_name) != 0,"　",""), IFNULL(C.building_name, ""))
            ELSE CONCAT(IFNULL(P.invoice_street_number_banchi, ""), IF(CHAR_LENGTH(P.invoice_building_name) != 0,"　",""), IFNULL(P.invoice_building_name, ""))
            END AS invoice_address2'
        );
        // 契約者様
        $query->selectRaw('CONCAT(IFNULL(C.last_name, ""),IFNULL(C.first_name, "")) AS contract_name');
        $query->selectRaw('CONCAT(IFNULL(C.prefecture, ""), IFNULL(C.city, ""), IFNULL(C.town, ""), IFNULL(C.street_number_choume, ""), IFNULL(C.street_number_banchi, ""), IF(CHAR_LENGTH(C.building_name) != 0,"　",""), IFNULL(C.building_name, "")) AS contract_address');
        // ログイン
        $query->addSelect('C.code AS customer_code');
        $query->selectRaw(
            'CASE
            WHEN HPP.mypage_pw_view_flag = 1
            THEN ""
            ELSE C.login_password
            END AS login_password'
        );
        $query->addselect('C.mail_address AS mail_address');
        // 契約締結販売店
        $query->addselect('HS.name_printed AS shop_name');
        $query->selectRaw(
            'CASE
            WHEN HC.agent_staff_id != 0 THEN HSS.name
            ELSE HSSK.name
            END AS staff_name'
        );
        // 契約内容
        $query->selectRaw('DATE_FORMAT( HC.apply_date, "%Y年%m月%d日") AS apply_date');
        $query->addselect('HC.power_customer_location_number AS power_customer_location_number');
        $query->selectRaw('DATE_FORMAT( HC.switching_scheduled_date, "%Y年%m月%d日") AS switching_scheduled_date');
        $query->selectRaw('DATE_FORMAT( date_add(HC.switching_scheduled_date, interval 2 month),"%Y年%m月") AS after_2month');
        $query->selectRaw(
            'CASE
            WHEN P.invoice_address_type = 1
            THEN CONCAT(IFNULL(C.last_name, ""),IFNULL(C.first_name, ""))
            ELSE CONCAT(IFNULL(P.invoice_last_name, ""),IFNULL(P.invoice_first_name, ""))
            END AS invoice_name'
        );
        $query->selectRaw(
            'CASE
            WHEN P.invoice_address_type = 1
            THEN C.zip_code
            ELSE P.invoice_zip_code
            END AS invoice_zip'
        );
        $query->selectRaw(
            'CASE
            WHEN P.invoice_address_type = 1
            THEN CONCAT(IFNULL(C.prefecture, ""), IFNULL(C.city, ""), IFNULL(C.town, ""), IFNULL(C.street_number_choume, ""), IFNULL(C.street_number_banchi, ""), IF(CHAR_LENGTH(C.building_name) != 0,"　",""), IFNULL(C.building_name, ""))
            ELSE CONCAT(IFNULL(P.invoice_prefecture, ""), IFNULL(P.invoice_city, ""), IFNULL(P.invoice_town, ""), IFNULL(P.invoice_street_number_choume, ""), IFNULL(P.invoice_street_number_banchi, ""), IF(CHAR_LENGTH(P.invoice_building_name) != 0,"　",""), IFNULL(P.invoice_building_name, ""))
            END AS invoice_address'
        );
        $query->addselect('HC.power_customer_name as power_customer_name');
        $query->selectRaw(
            'CASE
            WHEN HC.power_location_address_type = 1
            THEN C.zip_code
            ELSE HC.power_zip_code
            END AS power_zip'
        );
        $query->selectRaw(
            'CASE
            WHEN HC.power_location_address_type = 1
            THEN CONCAT(IFNULL(C.prefecture, ""), IFNULL(C.city, ""), IFNULL(C.town, ""), IFNULL(C.street_number_choume, ""), IFNULL(C.street_number_banchi, ""), IF(CHAR_LENGTH(C.building_name) != 0,"　",""), IFNULL(C.building_name, ""))
            ELSE CONCAT(IFNULL(HC.power_prefecture, ""), IFNULL(HC.power_city, ""), IFNULL(HC.power_town, ""), IFNULL(HC.power_street_number_choume, ""), IFNULL(HC.power_street_number_banchi, ""), IF(CHAR_LENGTH(HC.power_building_name) != 0,"　",""), IFNULL(HC.power_building_name, ""), IF(CHAR_LENGTH(HC.power_room_number) != 0,"　",""), IFNULL(HC.power_room_number, ""))
            END AS power_address'
        );
        // プラン
        $query->addselect('HPP.name_printed as power_plan_name');
        $query->addselect('HPP.contract_months AS contract_months');
        $query->addselect('HPP.cancel_fee AS cancel_fee');
        // 支払
        $query->addselect('P.payment_type AS payment_type');
        $query->selectRaw('"毎月末日締め" AS billing_closing_date');
        $query->selectRaw('"" AS payment_date');
        // 料金
        $query->addselect('HPPP.detail_language_1_basic_a AS detail_language_1_basic_a');
        $query->addselect('HPPP.detail_language2_basic AS detail_language2_basic');
        $query->addselect('HPPP.unit_basic AS unit_basic');
        $query->addselect('HPPP.basic_price AS basic_price');
        $query->addselect('HPPP.detail_language_1 AS detail_language_1');
        $query->addselect('HPPP.detail_language2_1 AS detail_language2_1');
        $query->addselect('HPPP.unit_1 AS unit_1');
        $query->addselect('HPPP.current_type1_price AS current_type1_price');
        $query->addselect('HPPP.detail_language_1_basic_b AS detail_language_1_basic_b');
        $query->addselect('HPPP.detail_language2_basic_b AS detail_language2_basic_b');
        $query->addselect('HPPP.unit_basic_b AS unit_basic_b');
        $query->addselect('HPPP.basic_price_b AS basic_price_b');
        $query->addselect('HPPP.detail_language_2 AS detail_language_2');
        $query->addselect('HPPP.detail_language2_2 AS detail_language2_2');
        $query->addselect('HPPP.unit_2 AS unit_2');
        $query->addselect('HPPP.current_type2_price AS current_type2_price');
        $query->addselect('HPPP.detail_language_1_basic_c AS detail_language_1_basic_c');
        $query->addselect('HPPP.detail_language2_basic_c AS detail_language2_basic_c');
        $query->addselect('HPPP.unit_basic_c AS unit_basic_c');
        $query->addselect('HPPP.basic_price_c AS basic_price_c');
        $query->addselect('HPPP.detail_language_3 AS detail_language_3');
        $query->addselect('HPPP.detail_language2_3 AS detail_language2_3');
        $query->addselect('HPPP.unit_3 AS unit_3');
        $query->addselect('HPPP.current_type3_price AS current_type3_price');
        $query->addselect('HPPP.detail_language_1_basic_d AS detail_language_1_basic_d');
        $query->addselect('HPPP.detail_language2_basic_d AS detail_language2_basic_d');
        $query->addselect('HPPP.unit_basic_d AS unit_basic_d');
        $query->addselect('HPPP.basic_price_d AS basic_price_d');
        $query->addselect('HPPP.detail_language_4 AS detail_language_4');
        $query->addselect('HPPP.detail_language2_4 AS detail_language2_4');
        $query->addselect('HPPP.unit_4 AS unit_4');
        $query->addselect('HPPP.current_type4_price AS current_type4_price');
        $query->addselect('HPPP.detail_language_1_basic_e AS detail_language_1_basic_e');
        $query->addselect('HPPP.detail_language2_basic_e AS detail_language2_basic_e');
        $query->addselect('HPPP.unit_basic_e AS unit_basic_e');
        $query->addselect('HPPP.basic_price_e AS basic_price_e');
        $query->addselect('HPPP.detail_language_5 AS detail_language_5');
        $query->addselect('HPPP.detail_language2_5 AS detail_language2_5');
        $query->addselect('HPPP.unit_5 AS unit_5');
        $query->addselect('HPPP.current_type5_price AS current_type5_price');
        $query->addselect('HPPP.detail_language_1_basic_f AS detail_language_1_basic_f');
        $query->addselect('HPPP.detail_language2_basic_f AS detail_language2_basic_f');
        $query->addselect('HPPP.unit_basic_f AS unit_basic_f');
        $query->addselect('HPPP.basic_price_f AS basic_price_f');
        $query->addselect('HPPP.detail_language_6 AS detail_language_6');
        $query->addselect('HPPP.detail_language2_6 AS detail_language2_6');
        $query->addselect('HPPP.unit_6 AS unit_6');
        $query->addselect('HPPP.current_type6_price AS current_type6_price');
        $query->addselect('HPPP.detail_language_1_basic_g AS detail_language_1_basic_g');
        $query->addselect('HPPP.detail_language2_basic_g AS detail_language2_basic_g');
        $query->addselect('HPPP.unit_basic_g AS unit_basic_g');
        $query->addselect('HPPP.basic_price_g AS basic_price_g');
        $query->addselect('HPPP.detail_language_7 AS detail_language_7');
        $query->addselect('HPPP.detail_language2_7 AS detail_language2_7');
        $query->addselect('HPPP.unit_7 AS unit_7');
        $query->addselect('HPPP.current_type7_price AS current_type7_price');
        $query->addselect('HPPP.detail_language_1_basic_h AS detail_language_1_basic_h');
        $query->addselect('HPPP.detail_language2_basic_h AS detail_language2_basic_h');
        $query->addselect('HPPP.unit_basic_h AS unit_basic_h');
        $query->addselect('HPPP.basic_price_h AS basic_price_h');
        $query->addselect('HPPP.detail_language_8 AS detail_language_8');
        $query->addselect('HPPP.detail_language2_8 AS detail_language2_8');
        $query->addselect('HPPP.unit_8 AS unit_8');
        $query->addselect('HPPP.current_type8_price AS current_type8_price');
        // 料金(オプションの明細)
        $query->addselect('HOP.name_printed AS option_name');
        $query->addselect('HOP.payment_type AS option_payment_type');
        $query->selectRaw('HOP.price * 1.1 AS option_price');
        // 付帯サービス・オプションサービス
        $query->addselect('HOP.name_printed AS name_printed');
        $query->addselect('HOP.start_date_text AS start_date_text');
        $query->addselect('HOP.immunity_text AS immunity_text');
        $query->addselect('HOP.url AS url');
        $query->addselect('HOP.supplier AS supplier');
        $query->addselect('HOP.about_billing_text AS about_billing_text');
        $query->addselect('HOP.reception_counter AS reception_counter');
        $query->addselect('HOP.phone AS phone');
        $query->addselect('HOP.contact_url AS contact_url');
        $query->addselect('HOP.reception_time AS option_reception_time');
        $query->addselect('HOP.holiday AS holiday');
        $query->addselect('HOP.agreement_file_title AS agreement_file_title');
        $query->addselect('HOP.include_flag AS include_flag');
        // オプション個別
        $query->addselect('HCOP.option_plan_id');
        // 電気量のお知らせ
        $query->addselect('HCOP1.option_name_1');
        $query->selectRaw('NULL AS option_title_1');
        $query->addselect('HCOP1.option_payment_type_1');
        $query->addselect('HCOP1.option_price_1');
        // つながる修理サポート（Z）
        $query->addselect('HCOP5.option_name_5');
        $query->selectRaw('NULL AS option_title_5');
        $query->addselect('HCOP5.option_payment_type_5');
        $query->addselect('HCOP5.option_price_5');
        // つながる修理サポート（M）
        $query->addselect('HCOP7.option_name_8');
        $query->selectRaw('NULL AS option_title_8');
        $query->addselect('HCOP7.option_payment_type_8');
        $query->addselect('HCOP7.option_price_8');
        // つながる修理サポートS
        $query->addselect('FUTAI2.name_printed2');
        $query->addselect('FUTAI2.cp_id_key');
        // つながる修理サポートZ
        $query->addselect('FUTAI6.name_printed6');
        $query->addselect('FUTAI6.cp_id_key_z');
        // つながる修理サポートM
        $query->addselect('FUTAI9.name_printed9');
        $query->addselect('FUTAI9.cp_id_key_m');
        // スマートシネマ
        $query->addselect('FUTAI5.name_printed5');
        $query->addselect('FUTAI5.cp_smart_cinema_id_key');
        $query->addselect('FUTAI5.cp_smart_cinema_password_key');
        $query->addselect('FUTAI5.cp_smart_cinema_gift_code');
        $query->addselect('FUTAI5.start_date_text5');
        $query->addselect('FUTAI5.smart_cinema_management_source');
        // お財布サポートbyえらべる倶楽部
        $query->addselect('HCOP4.option_name_4');
        $query->selectRaw('NULL AS option_title_4');
        $query->addselect('HCOP4.option_payment_type_4');
        $query->addselect('HCOP4.option_price_4');
        $query->addselect('HCOP4.hcop4_cp_license_key AS serial');
        // ABEMAプレミアム
        $query->addselect('HPP.abema_flag');
        $query->selectRaw(
            'CASE
            WHEN HPP.abema_flag = 1
            THEN HAC.coupon_id
            ELSE ""
            END AS "coupon_id"'
        );
        $query->selectRaw(
            'CASE
            WHEN HPP.abema_flag = 1
            THEN HAC.coupon_code
            ELSE ""
            END AS "coupon_code"'
        );
        // music.jp
        $query->addselect('HC.music_jp_id AS music_jp_id');
        $query->addselect('HC.music_jp_password AS music_jp_password');
        // ペットハート
        $query->addselect('FUTAI12.name_printed12 AS name_printed12');
        $query->addselect('FUTAI12.servise_id12 AS servise_id12');
        $query->addselect('FUTAI12.password12 AS password12');
        // music.jp動画コース
        $query->addselect('FUTAI14.name_printed14');
        $query->addselect('FUTAI14.music_movie_account_id');
        $query->addselect('FUTAI14.music_movie_account_password');
        $query->addselect('FUTAI14.music_movie_option_id');
        // music.jp漫画コース
        $query->addselect('FUTAI15.name_printed15');
        $query->addselect('FUTAI15.music_comic_account_id');
        $query->addselect('FUTAI15.music_comic_account_password');
        $query->addselect('FUTAI15.music_comic_option_id');
        // 小売電気事業者区分
        $query->addselect('HC.pps_business_number AS pps_business_number');
        // 条件
        $query->where('HC.status', 1);
        $query->where('HC.power_customer_location_number', $supplypoint_code);
        $query->where('C.code', $customer_code);
        $result = $query->get();
        return $result;
    }
}
