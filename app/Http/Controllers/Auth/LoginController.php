<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Extensions\AuthenticatesUsersExtra;
use App\Facades\GetInvoice;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

// eloquent
use App\ParentChild;
use App\Models\DB\User;
use App\Models\DB\Contract;
// use App\Models\Services\Electric;
// use App\Models\Services\Gas;
use App\Models\Services\Mobile;
// use App\Models\Services\Option;

/**
 * ログイン認証処理 の 基本
 */
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsersExtra;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * ログインページ
     */
    public function loginForm()
    {
        return view('renewal/login');
    }

    /**
     * ログイン直後に実行したいタスクを書く
     * ex. Laravelでログインしたときにいろいろやる
     * https://saba.omnioo.com/note/2998/laravel%E3%81%A7%E3%83%AD%E3%82%B0%E3%82%A4%E3%83%B3%E3%81%97%E3%81%9F%E3%81%A8%E3%81%8D%E3%81%AB%E3%81%84%E3%82%8D%E3%81%84%E3%82%8D%E3%82%84%E3%82%8B/
     */
    protected function authenticated(Request $request, $user)
    {
        // セッションにuser情報を書く ( toArray()を通して安全にします )
        $request->session()->put('user_login', $user->toArray());

        // 現在権限ユーザ (ユーザ視点モードとでもいうべきものを考慮した場合)
        $request->session()->put('user_now', $user->toArray());

        // ログイン時にParentChild関係性をセッションに持つ。
        // 親子顧客関係 parent_child から 子_顧客コード の読み込み
        $parent_child = [];
        $results = ParentChild::with('user')->where('parent_customer_code', $user->customer_code )->get();
        foreach ($results as $temp_contract) {
            $parent_child[] = [
                'child_customer_code' => $temp_contract->child_customer_code,
                'user_name'           => $temp_contract->user['name'],
            ];
        }
        $request->session()->put('user_login_parent_child', $parent_child);
        $request->session()->put('user_now_parent_child', $parent_child);
		if(isset($request->memory_id)){
			Cookie::queue(Cookie::make('memoried_id',$user->customer_code,10080));
		}else{
			Cookie::queue(Cookie::forget('memoried_id'));
		}

        $contracts = GetInvoice::get_supplypoint_list($user->customer_code);
        $supplypoint_code_undefined_flg = false;
        foreach ($contracts as $contract) {
            if (mb_strlen($contract['supplypoint_code']) == 1 || $contract['supplypoint_code'] == 'key') {
                try {
                    $mallie_contract = DB::connection('mysql_mallie')->table('HalueneContract AS HC')
                    ->join('CustomerOrdered AS CO', 'CO.id', 'HC.customer_id')
                    ->where('CO.code', $contract['customer_code'])
                    ->where('HC.code', $contract['contract_code'])
                    ->select('HC.power_customer_location_number')
                    ->first();
                    if (empty($mallie_contract->power_customer_location_number) || mb_strlen($mallie_contract->power_customer_location_number) == 1 || $mallie_contract->power_customer_location_number == 'key') {
                        $supplypoint_code_undefined_flg = true;
                    } else {
                        $contract = Contract::where('customer_code', $contract['customer_code'])
                        ->where('contract_code', $contract['contract_code'])
                        ->first();
                        $contract->supplypoint_code = $mallie_contract->power_customer_location_number;
                        $contract->save();
                    }
                } catch (\Exception $e) {
                    Log::debug('supplypoint_code Sync error: ' . $contract['customer_code']);
                    Log::debug($e);
                }

            }
        }
        $request->session()->put('supplypoint_code_undefined_flg', $supplypoint_code_undefined_flg);

        // for wifi
        $wifi_delivery_date = false;
        $wifi_delivery_date_change_url = '';
        $wifi_delivery_time = '';
        $Mobile = Mobile::getInstance($user->customer_code);
        if ($Mobile->hasContract()) {
            $mobile_contract = $Mobile->getContract();
            $wifi_delivery_date = $mobile_contract['delivery_date'];
            $wifi_delivery_time = $mobile_contract['delivery_time'];
            $wifi_delivery_date_change_url = $Mobile->getDeliveryChangeUrl();
        }
        $request->session()->put('wifi_delivery_date', $wifi_delivery_date);
        $request->session()->put('wifi_delivery_time', $wifi_delivery_time);
        $request->session()->put('wifi_delivery_date_change_url', $wifi_delivery_date_change_url);
    }

    /**
     * ex. [PHP]Laravelでメールアドレスでもユーザー名(ログインID)でもログインできるようにする
     * https://php-archive.net/php/laravel-auth-with-username-or-email/
     *
     */
    public function username()
    {
        return 'customer_code';
    }

    /**
     * ログイン判定本体
     * ex2. Laravelのログインを改造してみた (attemptLogin操作)
     * https://qiita.com/moyashimanjyu/items/a9e1809df081ed9b7671
     *
     */
    protected function attemptLogin(Request $request)
    {
        $username = $request->input($this->username());
        $password = $request->input('password');

        // username がEmailの型なら検索はemailをキーにして行う
        if (filter_var($username, \FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $username, 'password' => $password];
        } else {
            $credentials = [$this->username() => $username, 'password' => $password];
        }

        // 資格情報 credentials
        $attempted = $this->guard()->attempt(
            $credentials, $request->filled('remember')
        );

        // ここに独自の認証を作る
        if ($attempted) {
            $request->session()->put('db_accesspoint', '1');
            $request->session()->put('db_accesspoint_now', '1');
        }
        return $attempted;
    }
}
