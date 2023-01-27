<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App;
use App\Consts\PaymentOrderedConsts;

use App\Facades\GetInvoice;
use App\Billing;
use App\UsageT;
use DateTime;

/**
 * 支払い状況画面
 */
class PaymentStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $data = array();
        return view('payment_status')->with("data", $data);
    }

    /**
     * 支払い状況詳細
     */
    public function detail(Request $request)
    {
        $data = array();
        $billing_data = array();
        $detail_list = array();

        $req = $request->all();

        $usage_date = $req['date'];
        // 請求月（使用月+1）
        $usage_y = mb_substr($usage_date, 0, 4);
        $usage_m = mb_substr($usage_date, 4, 2);
        if ($usage_m == '12') {
            $usage_y = $usage_y + 1;
            $usage_m = '01';
            $billing_date = $usage_y . $usage_m;
        } else {
            $billing_date = $usage_date + 1;
        }

        // 契約一覧
        $contracts = GetInvoice::get_supplypoint_list(session('user_now.customer_code'));
        $billig_data['billing_amount_total'] = 0;

        foreach ($contracts as $contract) {
            $result = $this->get_detail($contract["customer_code"], $contract["supplypoint_code"], $billing_date);
            if ($result["status"]) {
                if (!empty($result['billing']['start_date'])) {
                    $result['billing']['start_date'] = (new DateTime($result['billing']['start_date']))->format('Y年m月d日');
                }
                if (!empty($result['billing']['end_date'])) {
                    $result['billing']['end_date'] = (new DateTime($result['billing']['end_date']))->format('Y年m月d日');
                }
                if (!empty($result['billing']['metering_date'])) {
                    $result['billing']['metering_date'] = (new DateTime($result['billing']['metering_date']))->format('Y年m月d日');
                }
                if (!empty($result['billing']['next_metering_date'])) {
                    $result['billing']['next_metering_date'] = (new DateTime($result['billing']['next_metering_date']))->format('Y年m月d日');
                }
                array_push($detail_list, $result['billing']);
                if (empty($billing_data['payment_date'])) {
                    if ((preg_match('/^(\d{4}\/\d{2}\/\d{2})$/',$result['billing']['payment_date']))) {
                        $billig_data['payment_date'] = (new DateTime($result['billing']['payment_date']))->format('Y年m月d日');
                    }else {
                        $billig_data['payment_date'] = $result['billing']['payment_date'];
                    }
                }
                if (empty($billing_data['payment_type'])) {
                    $billig_data['payment_type'] = $result['billing']['payment_type'];
                }
                $billig_data['billing_amount_total'] = $billig_data['billing_amount_total'] + $result['billing']['billing_amount'];
            }
        }

        // 支払い状況チェック
        $payment_check = $this->get_billing(session('user_now.customer_code'), $billing_date, $billing_date);
        $payment_check[$billing_date]['today'] = (new DateTime())->format('Ym');

        $data['detail_list'] = $detail_list;
        $data['billig_data'] = $billig_data;
        $data['payment_check'] = $payment_check[$billing_date];
        return view('payment_status_detail')->with("data", $data);
    }

    public function billing_list(Request $request)
    {
        $req = $request->all();
        $billing_data = $this->get_billing(session('user_now.customer_code'), $req['billing_date_start'], $req['billing_date_end']);
        // json自動ソート防止
        $billing_list = [];
        foreach ($billing_data as $value) {
            array_push($billing_list, $value);
        }
        $result['billing_list'] = $billing_list;
        return $result;
    }

    /**
     * 明細一覧取得
     */
    public function get_billing($customer_code, $billing_date_start = null, $billing_date_end = null)
    {
        $contracts = GetInvoice::get_supplypoint_list($customer_code);

        $billing_list = [];
        foreach ($contracts as $contract) {
            $supplypoint_code = $contract["supplypoint_code"];
            $customer_code = $contract["customer_code"];
            $whereraw = GetInvoice::get_assignment_whereraw($contract["supplypoint_code"], $customer_code);

            $query = Billing::where("billing.supplypoint_code", $contract["supplypoint_code"])
            ->leftJoin('payment_status', function ($join) {
                $join->on('payment_status.supplypoint_code', 'billing.supplypoint_code');
                $join->on('payment_status.billing_date', 'billing.billing_date');
            })
            ->where('billing.customer_code', $contract["customer_code"])
            ->whereRaw($whereraw)
            ->select('billing.billing_date')
            ->addSelect('billing.billing_amount')
            ->addSelect('payment_status.payment_amount')
            ->addSelect('payment_status.payment_type')
            ->orderBy('billing.billing_date', 'desc');
            if (!empty($billing_date_start) && !empty($billing_date_end)) {
                $query->whereBetween('billing.billing_date', [ $billing_date_start, $billing_date_end]);
            }
            $billings = $query->get();

            foreach ($billings as $key => $billing) {
                if (empty($billing_list[$billing->billing_date])) {
                    $billing_list[$billing->billing_date] = $billing->toArray();
                } else {
                    $billing_list[$billing->billing_date]['billing_amount'] += $billing->billing_amount;
                    $billing_list[$billing->billing_date]['payment_amount'] += $billing->payment_amount;
                }
            }
        }
        return $billing_list;
    }

    public function get_detail($customer_code, $supplypoint_code, $billing_date) {

        $billing_query = Billing::join('contract', 'contract.supplypoint_code', 'billing.supplypoint_code')
        ->where("billing.supplypoint_code", $supplypoint_code)
        ->where("billing.customer_code", $customer_code)
        ->where("billing.billing_date", $billing_date);

        if ($billing_query->count() > 0) {
            $billing = $billing_query->first()->toArray();
            $usage_query = UsageT::where('supplypoint_code', $supplypoint_code)
            ->where("customer_code", $customer_code)
            ->where("usage_date", $billing['usage_date']);
            if ($usage_query->count() > 0) {
                $usage = $usage_query->first();
                $billing['usage'] = $usage->usage;
            } else {
                $billing['usage'] = '';
            }
            return ["status"=> true, "billing" => $billing];
        } else {
            return ["status"=> false];
        }
    }
}
