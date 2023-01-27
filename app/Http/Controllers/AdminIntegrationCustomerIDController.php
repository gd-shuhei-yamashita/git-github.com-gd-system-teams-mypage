<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

// eloquent
use App\ParentChild;
use App\User;

/**
 * マイページID統合  
 */
class AdminIntegrationCustomerIDController extends Controller
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
        return view('admin/integration_customer_id');
    }


    /**
     * 一覧取得
     */
    public function getlist(Request $request)
    {
        Log::debug("AdminRegistNoticeController : getlist");
        $req = $request->all();

        // 検索条件デバッグ出力
        Log::debug( "検索条件：" );
        Log::debug($req);

        /** 通知 notice 取得 */
        $notice = [];

        // $results = ParentChild::where("notice_date","<","now()");
        $results = new ParentChild;

        // withTrashed() で削除済みも表示する
        // 1件取得モード
        if (array_key_exists( "now_cid", $req)) {
            $results = $results->where( "id", $req['now_cid'] );
        }

        // ParentChild の条件での合計数を取得する
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
        $parent_child = $results->orderBy('created_at', 'desc')->get();
        Log::debug( $parent_child );

        // 戻り値に割り当てられた 請求データ を渡す。
        $value = [];
        $value["status"]  = "200";
        $value["parent_child"]   = $parent_child;
        $value["parent_child_counts"]   = $results_count;
        $value["now_state"]      = $now_state;
        $value["message"] = "OK";
        return $value;    

    }

    /**
     * 記事追加/変更
     */
    public function store(Request $request)
    {
        Log::debug("AdminRegistNoticeController : store");
        $req = $request->all();
        Log::debug($req);
        // return back()->withInput()->with('status', 'メッセージ表示テスト');
        
        // デフォルト方式でのバリデーション
        // 正しく半角文字列かどうか、など。
        $validatedData = $request->validate([
            'parent_customer_code' => 'required|string|alpha_num|between:9,10',
            'child_customer_code' => 'required|string|alpha_num|between:9,10',
            'change_result'=> 'required',
        ]);

        $result = ["status" => 0];

        // 追加判定
        // ToDo:すでにある組み合わせは登録が行えない制約

        // 存在している(していた)マイページIDのみを対象とする
        $user_search2 = User::where("customer_code",$request->parent_customer_code);
        if ($user_search2->count() == 0) {
            return back()->withInput()->withErrors(['parent_customer_code' => "存在しないマイページIDです"]);
        }
        $user_search2 = User::where("customer_code",$request->child_customer_code);
        if ($user_search2->count() == 0) {
            return back()->withInput()->withErrors(['child_customer_code' => "存在しないマイページIDです"]);
        }
        
        // 統合元と統合先は同一では不可
        if ($request->parent_customer_code == $request->child_customer_code) {
            return back()->withInput()->withErrors(['child_customer_code' => "統合元と統合先が同一です。"]);
        }

        // 記事追加を行う
        try
        {
            $cid = $req["cid"];
            $user_code = Session::get('user_login.customer_code',"");
            if ($cid == 0) {
                // 新規
                Log::debug("new:");
                $parent_child = new ParentChild;
                $parent_child->parent_customer_code = $req["parent_customer_code"];
                $parent_child->child_customer_code  = $req["child_customer_code"];
                $parent_child->created_user_id = $user_code;
                $parent_child->updated_user_id = $user_code;
                $parent_child->change_result        = preg_replace("/\r\n|\r|\n/", '\n', htmlentities($req["change_result"]));
                $parent_child->save();

            } else {
                // 変更
                Log::debug("change:" . $req["cid"]);
                $parent_child = ParentChild::where( 'id', $cid )->first();
                $parent_child->parent_customer_code = $req["parent_customer_code"];
                $parent_child->child_customer_code  = $req["child_customer_code"];
                $parent_child->updated_user_id = $user_code;
                $parent_child->change_result        = preg_replace("/\r\n|\r|\n/", '\n', htmlentities($req["change_result"]));
                $parent_child->save();
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
        return redirect()->route("integration_customer_id")->with('status', '記事変更しました。');
    }    

    /**
     * リンク解除（削除）  
     */
    public function delete(Request $request)
    {
        Log::debug("AdminRegistNoticeController : delete");
        $req = $request->all();
        Log::debug($req);
        $cid = $req["cid"];
        $result = ["status" => 0];
        $user_code = Session::get('user_login.customer_code',"");
        try
        {
            // ソフト削除
            $parent_child = ParentChild::where( 'id', $cid );
            $parent_child_first = $parent_child->first();
            $parent_child_first->updated_user_id = $user_code;
            $parent_child_first->save(); // マイページIDを書き込む
            $parent_child->delete(["updated_user_id" ,$user_code]);

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
        return redirect()->route("integration_customer_id")->with('status', '記事削除しました。');
    }
}
