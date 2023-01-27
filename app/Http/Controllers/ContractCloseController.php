<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

// eloquent
use App\User;

use App\Facades\GetInvoice;
use App\Mail\CloseContractCustomerMail;
use App\Mail\CloseContractOfficeMail;

/**
 * 契約解約画面
 */
class ContractCloseController extends Controller
{
    private static $rules = [
        'service' => "required",
        'reason' => "required",
        'moving' => "required",
        'start_year' => "required_if:moving,1",
        'start_month' => "required_if:moving,1",
        'start_day' => "required_if:moving,1",
        'new_postal' => "required_if:moving,1|nullable|numeric|digits:7",
        'new_add' => "required_if:moving,1",
        'new_build' => "nullable",
        'plan' => "required",
        'add' => "nullable",
        'electric_last_year' => "required_if:service,electric,electric_gas",
        'electric_last_month' => "required_if:service,electric,electric_gas",
        'electric_last_day' => "required_if:service,electric,electric_gas",
        'gas_last_year' => "required_if:service,gas,electric_gas",
        'gas_last_month' => "required_if:service,gas,electric_gas",
        'gas_last_day' => "required_if:service,gas,electric_gas",
        'meter' => "required",
        'supplypoint_code' => "nullable",
        'customer_num' => "nullable",
        'plan_name' => "nullable",
        'name' => "required",
        'phone' => "nullable",
        'postal_send' => "required|numeric|digits:7",
        'add_send' => "required",
        'build_send' => "nullable",
        'tel' => "nullable|numeric|digits:11",
        'mail' => "required|email",
        'purapori' => "required",
    ];
    private static $messages = [
        'service.required' => "選択して下さい",
        'reason.required' => "選択して下さい",
        'moving.required' => "選択して下さい",
        'start_year.required_if' => "選択して下さい",
        'start_month.required_if' => "選択して下さい",
        'start_day.required_if' => "選択して下さい",
        'new_postal.required_if' => "郵便番号を入力して下さい",
        'new_postal.numeric' => "半角数字7桁で入力してください。",
        'new_postal.max' => "半角数字7桁で入力してください。",
        'new_add.required_if' => "住所を入力して下さい",
        'plan.required' => "選択して下さい",
        'electric_last_year.required_if' => "選択して下さい",
        'electric_last_month.required_if' => "選択して下さい",
        'electric_last_day.required_if' => "選択して下さい",
        'gas_last_year.required_if' => "選択して下さい",
        'gas_last_month.required_if' => "選択して下さい",
        'gas_last_day.required_if' => "選択して下さい",
        'meter.required' => "選択して下さい",
        'name.required' => "お名前を入力して下さい",
        'postal_send.required' => "郵便番号を入力して下さい",
        'postal_send.numeric' => "半角数字7桁で入力してください。",
        'postal_send.max' => "半角数字7桁で入力してください。",
        'add_send.required' => "住所を入力して下さい",
        'tel.numeric' => "半角数字で入力してください",
        'tel.digits' => "半角数字は11桁で入力してください。",
        'mail.required' => "メールアドレスを入力して下さい",
        'mail.email' => "メールアドレスの形式が正しくありません。",
        'purapori.required' => "プライバシーポリシーを確認の上チェックしてください",
    ];
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $contracts = GetInvoice::get_supplypoint_list(session('user_now.customer_code'));

        return view('contract_close')->with("contracts", $contracts)->with("user", session('user_now'));
    }

    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), self::$rules, self::$messages);
        if ($validator->fails()) {
            // Log::debug($validator);
            return back()->withInput()->withErrors($validator);
        }

        $data = $validator->validated();
        $request->session()->put('close_params', $data);   
        
        return view('contract_close_confirm')->with("data", $data);
    }

    public function thanks(Request $request)
    {
        $data = session('close_params');

        $user = user::on('mysql')->where('customer_code', session('user_now.customer_code'))->first();
        if (!empty($user)) {
            $user->notification_email = $data['mail'];
            $user->save();
        }

        Mail::to(mail_alias_replace('service-info@grandata-grp.co.jp'))->send(new CloseContractOfficeMail($data));
        Mail::to(mail_alias_replace($data['mail']))->send(new CloseContractCustomerMail($data));
        return view('contract_close_thanks');
    }
}
