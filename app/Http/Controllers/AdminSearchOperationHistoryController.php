<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

// eloquent
use App\OperationHistory;

/**
 * 操作履歴検索画面
 */
class AdminSearchOperationHistoryController extends Controller
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

        return view('admin/search_operation_history', ["forms" => $forms]);
    }

    
    /**
     * 一覧取得
     */
    public function getlist(Request $request)
    {
        Log::debug("AdminSearchOperationHistoryController : getlist");
        $req = $request->all();

        // 検索条件デバッグ出力
        Log::debug( "検索条件：" );
        Log::debug($req);

        /** 通知 notice 取得 */
        $operation_history = [];
        
        // $results = OperationHistory::where( 'notice_date', '<=' , DB::raw('now()'));
        $results = new OperationHistory;

        // 検索条件をresultsに付け加えていく
        if ($req['customer_code']) {
            $results = $results->where("user_id", $req['customer_code']);
        }
        // notice_date_from
        if ($req['notice_date_from']) {
            $results = $results->where("created_at", '>=' , $req['notice_date_from'] . " 00:00:00");
        }
        // notice_date_to
        if ($req['notice_date_to']) {
            $results = $results->where("created_at", '<=' , $req['notice_date_to'] . " 23:59:59");
        }
        // usersの条件での合計数を取得する
        $operation_history_count = $results->count();

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
        $operation_history = $results->orderBy('id', 'desc')->get();
        Log::debug( $operation_history );

        // 戻り値に割り当てられた 請求データ を渡す。
        $value = [];
        $value["status"]  = "200";
        $value["operation_history"]   = $operation_history;
        $value["operation_history_counts"]   = $operation_history_count;
        $value["now_state"]      = $now_state;
        $value["message"] = "OK";
        return $value;    

    }

}
