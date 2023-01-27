<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App;
use App\Consts\PaymentOrderedConsts;
use App\Http\Controllers\FileMakerController;

/**
 * 支払い方法変更画面
 */
class PaymentMethodController extends Controller
{
    const OPENSSL_METHOD = 'AES-256-CBC';
    const OPENSSL_ENCRYPT_KEY = '8tx7turh9pwszj5gx4lh23SVhvyejit7';
    const OPENSSL_ENCRYPT_IV = 'ytsxs3Qw6ad7qWmu';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $wifi_flag = false;
        // 通信契約
        $fileMaker = new FileMakerController();
        $local_file = $fileMaker->downloadForFileMaker('customer_info_read_file/','customer_info_read_file_' . session('user_now.customer_code') . '.csv');
        if (!empty($local_file)) {
            $mobile_contracts = $fileMaker->read_file($local_file);
            if (!empty($mobile_contracts) && !empty($fileMaker->contract_format($mobile_contracts))) {
                $wifi_flag = true;
            }
        }

        return view('payment_method')->with("wifi_flag", $wifi_flag);
    }

    /**
     * 電気、ガス用画面
     */
    public function electric_gas(Request $request)
    {
        $data = array();

        //支払い方法取得
        $payment_method = $this->get_payment_method(session('user_now.customer_code'));

        if (empty($payment_method)) {
            return view('payment_method_electric_gas');
        }
        
        $data['payment_type'] = empty($payment_method->payment_type) ? 0 : $payment_method->payment_type;
        // 表示用支払い区分リスト
        $data['payment_type_list'] = array();
        foreach (PaymentOrderedConsts::PAYMENT_TYPE_LIST as $key => $value) {
            if ($key == PaymentOrderedConsts::PAYMENT_TYPE_UNREGISTERED) {
                $data['payment_type_list'] += [ $key => '登録なし'];
            } elseif (strpos($value, '（') !== false) {
                $data['payment_type_list'] += [ $key => explode('（', $value)[0] ];
            } elseif (strpos($value, '(') !== false) {
                $data['payment_type_list'] += [ $key => explode('(', $value)[0] ];
            } else {
                $data['payment_type_list'] += [ $key => $value ];
            }
        }
        // 合算系は支払い方法非表示
        if (strpos($data['payment_type_list'][$data['payment_type']], '合算') === false) {
            $data['payment_type_display'] = true;
        } else {
            $data['payment_type_display'] = false;
        }
        // Mallieに渡すparam
        $param_str = 'param';
        $param_arr = [
            'customer_ordered_id' => $payment_method->customer_id,
            'payment_ordered_id' => $payment_method->payment_id,
        ];
        $param_data = $this->base64url_encode( $this->encrypt( json_encode($param_arr, JSON_UNESCAPED_UNICODE) ) );
        if (App::environment('staging')) {
            $data['regist_url'] = config('const.DevPaymentMethodRegistURL') . '?' . $param_str . '=' . $param_data;
            $data['modify_url'] = config('const.DevPaymentMethodModifyURL') . '?' . $param_str . '=' . $param_data;
        }elseif(App::environment('product')) {
            $data['regist_url'] = config('const.ProdPaymentMethodRegistURL') . '?' . $param_str . '=' . $param_data;
            $data['modify_url'] = config('const.ProdPaymentMethodModifyURL') . '?' . $param_str . '=' . $param_data;
        }
        return view('payment_method_electric_gas')->with("data", $data);
    }

    /**
     * wifi用画面
     */
    public function wifi(Request $request)
    {

        $wifi_flag = false;
        // 通信契約
        $fileMaker = new FileMakerController();
        $local_file = $fileMaker->downloadForFileMaker('customer_info_read_file/','customer_info_read_file_' . session('user_now.customer_code') . '.csv');
        if (!empty($local_file)) {
            $mobile_contracts = $fileMaker->read_file($local_file);
            if (!empty($mobile_contracts) && !empty($fileMaker->contract_format($mobile_contracts))) {
                $wifi_flag = true;
            }
        }

        if (!$wifi_flag) {
            return view('payment_method_wifi');
        }

        $data = array();

        // Mallieに渡すparam
        $param_str = 'param';
        $param_data = base64_encode(session('user_now.customer_code'));
        if (App::environment('staging')) {
            $data['modify_url'] = config('const.DevPaymentMethodModifyMobileURL') . '/?' . $param_str . '=' . $param_data;
        }elseif(App::environment('product')) {
            $data['modify_url'] = config('const.ProdPaymentMethodModifyMobileURL') . '/?' . $param_str . '=' . $param_data;
        }
        return view('payment_method_wifi')->with("data", $data);
    }

    /**
     * 支払い方法取得
     */
    public function get_payment_method($customer_code)
    {
        //MallieDBから支払い方法を取得するクエリ文
        $payment_query = DB::connection('mysql_mallie')->table('HalueneContract AS hc')
        ->join('CustomerOrdered AS co', 'co.id', '=', 'hc.customer_id')
        ->join('PaymentOrdered AS po', 'po.id', '=', 'hc.payment_id')
        ->where('co.code', $customer_code)
        ->where('hc.status', '!=', 2)
        ->select('hc.customer_id', 'hc.payment_id', 'po.payment_type');

        if ($payment_query->count() < 1) {
            return null;
        } else {
            return $payment_query->first();
        }
    }

    function base64url_encode($data) {
        return rtrim(str_replace(array('+', '/'), array('-', '_'), base64_encode($data)), '=');
      }
    function encrypt($data){
        return openssl_encrypt($data, self::OPENSSL_METHOD, self::OPENSSL_ENCRYPT_KEY, true, self::OPENSSL_ENCRYPT_IV);
    }
}
