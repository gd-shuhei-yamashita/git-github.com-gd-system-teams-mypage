<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

// eloquent
use App\Assignment;
use App\User;
use App\Contract;

/**
 * 譲渡変更
 */
class AdminChangeTransferController extends Controller
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
        // 日付
        $forms["date"] =date("Y/m/d");
        return view('admin/change_transfer', ["forms" => $forms]);
    }


    /**
     * 一覧取得
     */
    public function getlist(Request $request)
    {
        Log::debug("AdminChangeTransferController : getlist");
        $req = $request->all();

        // 検索条件デバッグ出力
        Log::debug( "検索条件：" );
        Log::debug($req);

        /** 譲渡データ Assignment 取得 */
        $assignment = [];

        // $results = Assignment::where("notice_date","<","now()");
        $results = new Assignment;

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
        
        // 

        // 一覧取得
        $assignment = $results->orderBy('assignment_date', 'desc')->get();
        Log::debug( $assignment );

        // 戻り値に割り当てられた 請求データ を渡す。
        $value = [];
        $value["status"]  = "200";
        $value["assignment"]   = $assignment;
        $value["assignment_counts"]   = $results_count;
        $value["now_state"]      = $now_state;
        $value["message"] = "OK";
        return $value;    

    }

    /**
     * 記事追加/変更
     */
    public function store(Request $request)
    {
        Log::debug("AdminChangeTransferController : store");
        $req = $request->all();
        Log::debug($req);
        // return back()->withInput()->with('status', 'メッセージ表示テスト');
        
        // デフォルト方式でのバリデーション
        $validatedData = $request->validate([
            'supplypoint_code'                => 'required|string|alpha_num|size:22',
            'assignment_before_customer_code' => 'required|string|alpha_num|between:9,10',
            'assignment_after_customer_code'  => 'required|string|alpha_num|between:9,10',
            'assignment_date'                 => 'required|date',
            'before_customer_billing_end'     => 'required|size:6',
            'after_customer_billing_start'    => 'required|size:6',
        ]);

        $result = ["status" => 0];
        
        // 存在しない供給地点特定番号は使えない
        $supplypoint_search = Contract::where("supplypoint_code",$request->supplypoint_code);
        if ($supplypoint_search->count() == 0) {
            return back()->withInput()->withErrors(['supplypoint_code' => "存在しない供給地点特定番号です"]);
        }

        // 存在しないお客様コードは使えない
        $user_search2 = User::where("customer_code",$request->assignment_before_customer_code);
        if ($user_search2->count() == 0) {
            return back()->withInput()->withErrors(['assignment_before_customer_code' => "存在しないお客様コードです"]);
        }
        $user_search2 = User::where("customer_code",$request->assignment_after_customer_code);
        if ($user_search2->count() == 0) {
            return back()->withInput()->withErrors(['assignment_after_customer_code' => "存在しないお客様コードです"]);
        }

        // 譲渡元と譲渡先は同一では不可
        if ($request->assignment_before_customer_code == $request->assignment_after_customer_code) {
            return back()->withInput()->withErrors(['assignment_after_customer_code' => "譲渡元と譲渡先が同一です。"]);
        }


        // ToDo: 譲渡先の内容など正しく代入する必要あり

        // 記事追加を行う
        try
        {
            $user_code = Session::get('user_login.customer_code',"");
            $cid = $req["cid"];
            if ($cid == 0) {
                // 新規
                $assignment = new Assignment;
                $assignment->supplypoint_code                 = $req["supplypoint_code"];
                $assignment->assignment_before_customer_code  = $req["assignment_before_customer_code"];
                $assignment->assignment_after_customer_code   = $req["assignment_after_customer_code"];
                $assignment->assignment_date                  = $req["assignment_date"];

                // 区分など
                $assignment->assignment_after_contract_name   = "sample";
                $assignment->assignment_after_address         = "sample";
                $assignment->assignment_after_plan            = "sample";
                $assignment->type                             = 0;

                $assignment->created_user_id = $user_code;
                $assignment->updated_user_id = $user_code;
                $assignment->before_customer_billing_end      = $req["before_customer_billing_end"];
                $assignment->after_customer_billing_start     = $req["after_customer_billing_start"];

                $assignment->save();

            } else {
                // 変更
                $assignment = Assignment::find( $cid );

                $assignment->supplypoint_code                 = $req["supplypoint_code"];
                $assignment->assignment_before_customer_code  = $req["assignment_before_customer_code"];
                $assignment->assignment_after_customer_code   = $req["assignment_after_customer_code"];
                $assignment->assignment_date                  = $req["assignment_date"];

                // 区分など
                $assignment->assignment_after_contract_name   = "sample";
                $assignment->assignment_after_address         = "sample";
                $assignment->assignment_after_plan            = "sample";
                $assignment->type                             = 0;

                $assignment->created_user_id = $user_code;
                $assignment->updated_user_id = $user_code;
                $assignment->before_customer_billing_end      = $req["before_customer_billing_end"];
                $assignment->after_customer_billing_start     = $req["after_customer_billing_start"];

                $assignment->save();
            }

        } catch (\PDOException $e)
        {
            Log::error($e);
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
        return back()->with('status', '譲渡変更 実施しました。');
    }    

    /**
     * リンク解除（削除）  
     */
    public function delete(Request $request)
    {
        Log::debug("AdminChangeTransferController : delete");
        $req = $request->all();
        Log::debug($req);
        $cid = $req["cid"];
        $result = ["status" => 0];
        $user_code = Session::get('user_login.customer_code',"");
        try
        {
            // ソフト削除
            $assignment = Assignment::where( 'id', $cid );
            $assignment_first = $assignment->first();
            $assignment_first->updated_user_id = $user_code;
            $assignment_first->save(); // マイページIDを書き込む
            $assignment->delete(["updated_user_id" ,$user_code]);

        } catch (\PDOException $e)
        {
            Log::error($e);
            // セキュリティ上DBにアクセスできなかったことだけ返す
            $result = ["status" => 1];
        }

        // 失敗時は前画面に戻す
        if ($result["status"] == 1) {
            return back()->withInput()->with('status', '更新に失敗いたしました');
        }

        // ページ遷移
        return redirect()->route("change_transfer")->with('status', '記事削除しました。');
    }
}
