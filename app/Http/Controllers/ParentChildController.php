<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

// eloquent
use App\ParentChild;
use App\User;

/**
 * 関連アカウント画面
 */
class ParentChildController extends Controller
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

        //  親子顧客関係 parent_child から 子_顧客コード の読み込み
        $users = [];

        // 必ずセッションのキーを元に出す
        $results = ParentChild::with("user")->where("parent_customer_code", session('user_now.customer_code'))->get();
        // Log::debug( $results );

        foreach ($results as $temp_contract) {
            $users[] = [
                "child_customer_code" => $temp_contract->child_customer_code,
                "user_name"           => $temp_contract->user['name'],
            ];
        }
        // Log::debug($users);

        // 関連する親子関係の 顧客アカウント一覧を出す
        return view('parent_child', [ "users" => $users ] );
    }

    
    /**
     * 2:ユーザ簡易覗き見モード設定
     */
    public function users_peek(Request $request) {
        Log::debug("ParentChildController : users_peek");
        $req = $request->all();

        Log::debug( $req["customer_code"] );

        // 親子関係が登録されていない場合にはエラーを返します。
        $user_code = Session::get('user_login.customer_code',"");
        $results = ParentChild::with("user")->where("parent_customer_code", $user_code )
        ->where("child_customer_code", $req["customer_code"])->get();
        if (!$results) {
            return back()->with('status', '不正なユーザ '.$req["customer_code"].' でログインしようとしました。');
        }

        // 存在しないユーザであったらエラーメッセージを返す
        $user = User::where('customer_code', $req["customer_code"])->first();
        if (!$user) {
            return back()->with('status', '不正なユーザ '.$req["customer_code"].' でログインしようとしました。');
        }

        // 親ユーザの記録
        $request->session()->put('parent_user', $request->session()->get('user_now') );


        // 現在権限ユーザの変更
        $request->session()->put('user_now', $user->toArray());

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
        $request->session()->put('user_now_parent_child', $parent_child);

        // ホームに転送される
        return redirect()->route('home')->with('status', '関連アカウント'.$req["customer_code"].'を確認しております。');
    }

    /**
     * 3:覗き見モードログアウト
     */
    public function peek_logout(Request $request) {
        Log::debug("ParentChildController : users_peek");
        $req = $request->all();

        Log::debug( $req["customer_code"] );

        // 親ユーザに戻す(支えられるのは２階層まで)
        // 存在しないユーザであったらエラーメッセージを返す
        // $user = User::where('customer_code', $request->session()->get('parent_user') )->first();
        // if (!$user) {
        //     return back()->with('status', '内部エラー :' . $request->session()->get('parent_user'));
        // }
        // $request->session()->put('user_now', $user->toArray());
        
        $request->session()->put('user_now', $request->session()->get('parent_user') );
        $request->session()->forget('parent_user');

        //現時点のユーザの情報に差し替える
        $parent_child = [];
        $results = ParentChild::with("user")->where("parent_customer_code", $request->session()->get('user_now.customer_code') )->get();
        // Log::debug( $results );
        foreach ($results as $temp_contract) {
            $parent_child[] = [
                "child_customer_code" => $temp_contract->child_customer_code,
                "user_name"           => $temp_contract->user['name'],
            ];
        }
        $request->session()->put('user_now_parent_child', $parent_child);        

        // ホームに転送される
        return redirect()->route('parent_child')->with('status', '関連アカウント確認を終了しました。  '.$req["customer_code"].' ');

    }

}
