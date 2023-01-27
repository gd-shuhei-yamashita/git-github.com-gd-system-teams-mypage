<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

// eloquent
use App\User;
use App\Contract;
use App\Billing;
use App\UsageT;
use App\ReplacementHistory;

/**
 * マイページID紐付変更画面
 * Contract（契約）、Billing（請求）、UsageT（使用量）テーブルの、マイページIDを一斉に書き換えます
 * 更新履歴を画面で確認可能です。
 */
class AdminChangeCustomerIDLinkageController extends Controller
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
        // ユーザー情報を取得
        $users = $request->session()->get('users', array());
        Log::debug($users);
        return view('admin/change_customer_id_linkage');
    }


    /**
     * 一覧取得
     */
    public function getlist(Request $request)
    {
        Log::debug("AdminChangeCustomerIDLinkageController : getlist");
        $req = $request->all();

        // 検索条件デバッグ出力
        Log::debug( "検索条件：" );
        Log::debug($req);

        /** 置換履歴 ReplacementHistory 取得 */
        $replacement_history = [];

        $results = ReplacementHistory::where('type',1);

        // 1件取得モード
        if (array_key_exists( "now_cid", $req)) {
            $results = $results->where( "id", $req['now_cid'] );
        }

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
        $replacement_history = $results->orderBy('created_at', 'desc')->get();
        Log::debug( $replacement_history );

        // 戻り値に割り当てられた 請求データ を渡す。
        $value = [];
        $value["status"]  = "200";
        $value["replacement_history"]   = $replacement_history;
        $value["replacement_history_counts"]   = $results_count;
        $value["now_state"]      = $now_state;
        $value["message"] = "OK";
        return $value;    

    }


    /**
     * 記事追加/変更
     */
    public function store(Request $request)
    {
        Log::debug("AdminChangeCustomerIDLinkageController : store");
        $req = $request->all();
        Log::debug($req);
        // return back()->withInput()->with('status', 'メッセージ表示テスト');
        
        // デフォルト方式でのバリデーション
        $validatedData = $request->validate([
            'now_customer_code' => 'required|string|alpha_num|between:9,10',
            'new_customer_code' => 'required|string|alpha_num|between:9,10',
        ]);

        // マイページID(旧)とマイページID(新)は同一では不可
        if ($request->now_customer_code == $request->new_customer_code) {
            return back()->withInput()->withErrors(['new_customer_code' => "マイページID(旧)とマイページID(新)が同一です。"]);
        }

        // 存在しないマイページIDは使えない
        $user_search2 = User::where("customer_code",$request->now_customer_code);
        if ($user_search2->count() == 0) {
            return back()->withInput()->withErrors(['now_customer_code' => "存在しないマイページIDです"]);
        }
        $user_search2 = User::where("customer_code",$request->new_customer_code);
        if ($user_search2->count() == 0) {
            return back()->withInput()->withErrors(['new_customer_code' => "存在しないマイページIDです"]);
        }

        $result = ["status" => 0];

        
        // 記事追加を行う
        $user_code = Session::get('user_login.customer_code',"");

        if (Session::get('db_accesspoint_now' ,0) == 2) {
            DB::connection('mysql2')->beginTransaction();
        } else {
            DB::beginTransaction();
        }

        Log::debug("Transaction Start");
        try
        {
            /**
             * Contract（契約）
             * PRIMARY KEY (`customer_code`,`supplypoint_code`)
             * 
             * Billing（請求）
             * PRIMARY KEY (`supplypoint_code`,`customer_code`,`billing_code`,`itemize_code`)
             * 
             * UsageT（使用量）
             * PRIMARY KEY (`customer_code`,`supplypoint_code`,`usage_date`)
             */

            // 契約差分 Contract 更新対象を一覧化
            $contract_list = [];
            $contract = Contract::where("customer_code", $request->now_customer_code)->where('supplypoint_code', '<>', '9999999999999999999999');
            // Log::debug($contract->get());
            foreach($contract->get() as $test) {
                $contract_list[] = $test->supplypoint_code;
            }
            
            // 内訳差分 Billing 更新対象を一覧化
            $billing_list = [];
            $billing = Billing::where("customer_code", $request->now_customer_code);
            // Log::debug($billing->get());
            foreach($billing->get() as $test) {
                $billing_list[] = "(".$test->supplypoint_code.",".$test->billing_code.",".$test->itemize_code.")";
            }

            // 使用率差分 UsageT 更新対象を一覧化
            $usage_t_list = [];
            $usage_t = UsageT::where("customer_code", $request->now_customer_code);
            // Log::debug($usage_t->get());
            foreach($usage_t->get() as $test) {
                $usage_t_list[] = "(".$test->supplypoint_code.",".$test->usage_date.")";
            }

            // 置き換え
            $contract_new = Contract::where("customer_code", $request->now_customer_code)->where('supplypoint_code', '<>', '9999999999999999999999');
            $contract_new->update(['customer_code' => $request->new_customer_code]);

            $billing_new = Billing::where("customer_code", $request->now_customer_code);
            $billing_new->update(['customer_code' => $request->new_customer_code]);

            $usage_t_new = UsageT::where("customer_code", $request->now_customer_code);
            $usage_t_new->update(['customer_code' => $request->new_customer_code]);


            // $cid = $req["cid"];
            // if ($cid == 0) {
            //     // 新規
            //     $notice = new Assignment;
            //     $notice->notice_date    = $req["notice_date"];
            //     $notice->url            = $req["url"];
            //     $notice->notice_comment = preg_replace("/\r\n|\r|\n/", '\n', htmlentities($req["notice_comment"]));
            //     $notice->save();

            // } else {
            //     // 変更
            //     $notice = Notice::find( $cid );

            //     $notice->notice_date    = $req["notice_date"];
            //     $notice->url            = $req["url"];
            //     $notice->notice_comment = preg_replace("/\r\n|\r|\n/", '\n', htmlentities($req["notice_comment"]));
            //     $notice->save();
            // }
            
            // 置換履歴
            $replacement_history = new ReplacementHistory;
            $replacement_history->type = 1; // 変更種別:(1:顧客ID紐付変更、2:供特紐付変更)
            $replacement_history->old_code = $req['now_customer_code'];
            $replacement_history->new_code = $req['new_customer_code'];
            $replacement_history->df_contract = implode(",\n", $contract_list);
            $replacement_history->df_billing  = implode(",\n", $billing_list);
            $replacement_history->df_usage_t  = implode(",\n", $usage_t_list);

            $replacement_history->created_user_id = $user_code;
            $replacement_history->updated_user_id = $user_code;
            $replacement_history->save();

            // Commit
            if (Session::get('db_accesspoint_now' ,0) == 2) {
                DB::connection('mysql2')->commit();
            } else {
                DB::commit();
            }
            Log::debug("Transaction OK");
        } catch (\PDOException $e)
        {
            Log::debug("Transaction Failed. Rollback Start.");
            DB::rollBack();
            Log::debug($e);

            // セキュリティ上DBにアクセスできなかったことだけ返す
            $result = ["status" => 1];
        }

        // 失敗時は前画面に戻す
        if ($result["status"] == 1) {
            return back()->withInput()->with('status', '更新に失敗いたしました');
        }

        Log::debug( config('const.TitleName') );

        // ページ遷移
        // return redirect()->route("regist_notice")->with('status', '記事変更しました。');
        return back()->with('status', '顧客ID紐付変更 実施しました。');
    }    
}
