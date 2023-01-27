<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use App\Facades\GetInvoice;

// eloquent
use App\UserSub; 
use App\UserNow; 
use App\UserUnion; 
use App\ParentChild;
use App\Contract;

use App\Models\Services\Mobile;

/**
 *  申込情報検索
 */
class AdminSearchApplicationInformationController extends Controller
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
        Log::debug("AdminSearchApplicationInformationController : index");
        // ユーザー情報を取得
        return view('admin/search_application_information');
    }

    /**
     * 内部向け関数 
     * ユーザ一覧の取得  
     */
    function users_getlistsql($req,$sub = false)
    {
        // 本来はViewで対処可能だった  
        // $results = UserUnion::with("contract");

        if ($sub) {
            // 副表示系
            // Log::debug("sub:" . session()->get('db_accesspoint_now', '0'));
            // 対偶のDBを選ぶ
            $UserSub = new UserSub;
            if (session()->get('db_accesspoint_now', '0') == 2) {
                // Log::debug("sub:2->1");
                $UserSub->setConnection('mysql');
                $results = $UserSub;
            } else {
                // Log::debug("sub:1->2");
                $UserSub->setConnection('mysql2');
                $results = $UserSub;
            }

        } else {
            // 主表示系 
            Log::debug("main:"); 
            if (session()->get('db_accesspoint_now', '0') == 2) {
                $UserNow = new UserNow;
                $results = $UserNow::with("contract")->selectRaw('*, 2 as `db_from`');
            } else {
                $UserNow = new UserNow;
                $results = $UserNow::with("contract")->selectRaw('*, 1 as `db_from`');
            }
        }


        // ToDo:縮退運用系(副表示系)  
        // $User2 = DB::connection('mysql2')->table('users')->selectRaw('*, 2 as `db_from`');

        // 下記直接記述ではある程度うまくいく。 別DBとの unionはできない。
        // $results = $UserSub;
        // $User = DB::connection('mysql')->table('users')->selectRaw('*, 1 as `db_from`');
        // $User2 = DB::connection('mysql2')->table('users')->selectRaw('*, 2 as `db_from`');
        // $results = $User2;

        // なお、EloQuent ではselectの記述は機能しない。->with("contract")は機能します。
        // $User = new UserSub;
        // $User->select(DB::raw('*, 2 as `db_from`'))->with("contract");
        // $User2 = new UserSub;
        // $User2->setConnection('mysql')->select(DB::raw('*, 2 as `db_from`'))->with("contract");
        // $results = $User->union($User2);
        // $results = $User;
        // $results = UserSub::with("contract");
        
        // 表示区分 仕分け
        if ($req['search_testuser']) {
            $results = $results->where("demouser", 1); // demouser 1:検証用ユーザ
        } else {
            $results = $results->where("demouser", 9); // demouser 9:一般ユーザ
        }
        
        // 削除済みユーザの検索
        if ($req['search_deleteuser']) {
            // 削除済み ex. ソフトデリート済みモデルのみの取得 https://readouble.com/laravel/5.5/ja/eloquent.html  
            $results = $results->onlyTrashed(); 
        }

        // 検索条件をresultsに付け加えていく
        if ($req['customer_code']) {
            $results = $results->where("customer_code", $req['customer_code']);
        }
        // ex. リレーション先のテーブルに条件をつけて取得するケース（whereHas）https://laraweb.net/practice/2446/  
        // クロージャー ex.  http://www.84kure.com/blog/2016/04/02/php-%E3%82%AF%E3%83%AD%E3%83%BC%E3%82%B8%E3%83%A3%E3%83%BC%E3%81%8C%E8%A6%AA%E3%81%AE%E3%82%B9%E3%82%B3%E3%83%BC%E3%83%97%E3%81%8B%E3%82%89%E5%A4%89%E6%95%B0%E3%82%92%E5%BC%95%E3%81%8D%E7%B6%99/
        if ($req['supplypoint_code']) {
            $results = $results->whereHas('contract', function($q) use (&$req){
                $q->where('supplypoint_code', $req['supplypoint_code']);
            });
        }
        if ($req['email']) {
            $results = $results->Where("email", 'like', '%' . $req['email'] . '%');
        }
        if ($req['zip_code']) {
            $results = $results->where("zip_code", $req['zip_code']);
        }
        if ($req['customer_name']) {
            $results = $results->Where("name", 'like', '%' . $req['customer_name'] . '%');
        }
        if ($req['phone']) {
            $results = $results->where("phone", $req['phone']);
        }

        return $results;
    }

    /**
     * 1 ユーザ一覧の取得
     * viewを利用して、multi_master 構成時には、複数DBを束ねて検索するように動作するようにしていた。  
     * ToDo: 別サーバのDBにビューは利用できないため、。  
     */
    public function users_getlist(Request $request)
    {
        Log::debug("AdminSearchApplicationInformationController : users_getlist");
        $req = $request->all();

        // 検索条件デバッグ出力
        Log::debug( "検索条件：" );
        Log::debug($req);
        
        // 検索条件に従ったユーザ一覧表示の取得
        /** ユーザ users 取得 */
        $results = $this->users_getlistsql($req);
        
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
        $users = $results->orderBy('customer_code', 'asc')->get();
        // Log::debug( $users );

        // 縮退運用系(副表示系)  
        // 同条件で別DBの合計数のみ取得する。
        // db_fromを元に接続先を決める  
        if (config("const.DBPlacement") == "multi_master") {
            $results_sub = $this->users_getlistsql($req, true);
            // userssubの条件での合計数を取得する
            $results_sub_count = $results_sub->count();
        } else {
            $results_sub_count = null;
        }


        // 戻り値に割り当てられた 請求データ を渡す。
        $value = [];
        $value["status"]  = "200";
        $value["db_accesspoint_now"] = session()->get('db_accesspoint_now', '0');
        $value["users"]   = $users;
        $value["users_counts"]   = $results_count;
        $value["users_sub_counts"]   = $results_sub_count;
        $value["now_state"]      = $now_state;
        $value["message"] = "OK";
        return $value;    

    }

    /**
     * 2:ユーザ覗き見モード設定
     */
    public function users_peek(Request $request) {
        // 権限が管理者の場合進めて良い
        Log::debug("AdminSearchApplicationInformationController : users_peek");
        $req = $request->all();

        Log::debug( "customer_code : " . $req["customer_code"] );
        Log::debug( "db_from : " . $req["db_from"] );
        
        // db_fromを元に接続先を決める  
        $UserSub = new UserSub;
        // この条件ではdb接続先は1にする
        if ($req["db_from"] == 1) {
            $UserSub->setConnection('mysql');
        }
        // この条件のみdb接続先は2にする
        if ($req["db_from"] == 2) {
            $UserSub->setConnection('mysql2');
        }

        // $user = UserNow::where('customer_code', $req["customer_code"])->first();
        $user = $UserSub->where('customer_code', $req["customer_code"])->first();
        // Log::debug($user);

        // 存在しないユーザであったらエラーメッセージを返す
        if (!$user) {
            return back()->with('status', '存在しないユーザ '.$req["customer_code"].' でログインしようとしました。');
        }

        // 現在権限ユーザの変更
        $request->session()->put('user_now', $user->toArray());
        // 別DBの場合
        if ($req["db_from"] == 2) {
            $request->session()->put('db_accesspoint_now', '2');   
        } else {
            $request->session()->put('db_accesspoint_now', '1');   
        }

        // ログイン時にParentChild関係性をセッションに持つ。  
        // 親子顧客関係 parent_child から 子_顧客コード の読み込み
        $parent_child = [];
        $results = ParentChild::with("user")->where("parent_customer_code", $user->customer_code )->get();
        // Log::debug( $results );
        foreach ($results as $temp_contract) {
            $parent_child[] = [
                "child_customer_code" => $temp_contract->child_customer_code,
                "user_name"           => $temp_contract->user['name'],
            ];
        }
        // $request->session()->put('user_login_parent_child', $parent_child);
        $request->session()->put('user_now_parent_child', $parent_child);

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
                    Log::debug("supplypoint_code Sync error: " . $contract['customer_code']);
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

        // ホームに転送される
        return redirect()->route('home')->with('status', 'ユーザ覗き見モードで'.$req["customer_code"].'になりました。');
    }

    /**
     * 3:覗き見モードログアウト
     */
    public function peek_logout(Request $request) {
        Log::debug("AdminSearchApplicationInformationController : users_peek");
        $req = $request->all();

        Log::debug( $req["customer_code"] );

        // 申込情報検索に転送される
        $user_login = $request->session()->get('user_login', []);
        $request->session()->put('user_now', $user_login);

        // 主接続先を親権限のDBに戻す
        $request->session()->put('db_accesspoint_now', $request->session()->get('db_accesspoint', '0'));

        $user_login_parent_child = $request->session()->get('user_login_parent_child', []);
        $request->session()->put('user_now_parent_child', $user_login_parent_child);

        // ホームに転送される
        return redirect()->route('search_application_information')->with('status', 'ユーザ覗き見モードを解除しました。  '.$req["customer_code"].' ');

    }

    /**
     * ユーザ管理メニュー 更新反映
     */
    public function store(Request $request) {
        Log::debug("AdminSearchApplicationInformationController : store");
        $req = $request->all();
        Log::debug( $req );

        /** 作業者記載 ユーザコード */
        $user_code = Session::get('user_login.customer_code',"");

        // 記事追加を行う
        $result = [];
        try
        {
            // ユーザを検索してマッチするなら。  
            $user_id = $req['user_id'];
            $user = UserNow::withTrashed()->find( $user_id ); // 削除済みのものも取得する      

            // フラグのオンオフを行う
            // * ユーティリティ 初回認証リテイク on/off
            if ($req['ninshou']=='false') {
                $user->email_verified_at = null;
            } else {
                // ToDo: すでに日付があるなら実施しない
                $user->email_verified_at = DB::raw('now()');
            }

            // * ユーティリティ 削除フラグ on/off
            if ($req['deleted']=='false') {
                $user->deleted_at = null;
            } else {
                // ToDo: すでに日付があるなら実施しない
                $user->deleted_at = date("Y-m-d H:i:s");
            }

            $user->updated_user_id = $user_code;                
            $user->save();
            $result["status"]  = "200";
            $result["message"] = "OK";
        } catch (\PDOException $e)
        {
            Log::error($e);
            // セキュリティ上DBにアクセスできなかったことだけ返す
            $result["status"]  = "500";
            $result["message"] = "NG change ";
        }

        // 戻り値に動作ステータスを渡す。
        return $result;
    }

}
