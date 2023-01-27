<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

// eloquent
use App\User;
use App\UserNow;

/**
 * 管理者/テストユーザー登録
 */
class SystemRegistAdministratorController extends Controller
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
        return view('system/regist_administrator');
    }


    /**
     * 記事追加/変更
     */
    public function store(Request $request)
    {
        Log::debug("SystemRegistAdministratorController : store");
        $req = $request->all();
        Log::debug($req);
        // return back()->withInput()->with('status', 'メッセージ表示テスト');

        // デフォルト方式でのバリデーション
        $validatedData = $request->validate([
            'role' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'customer_code' => 'required|size:10',
            'password' => 'required|min:10|max:20',
        ]);

        // 設定が multi_master の場合は返す
        if (config('const.DBPlacement') == 'multi_slave') {
            return back()->withInput()->withErrors(['email' => "サービス設定が multi_slave のため親サービスから登録を行ってください"]);
            // return back()->withInput()->with('status', 'メッセージ表示テスト');
        }

        // バリデーションは PasswordChangeController と同様
        // 手動のバリデーション
        // 肯定先読み ex. 正規表現「肯定先読み」とは？ http://www-creators.com/archives/2746
        // ex. 言語別：パスワード向けの正規表現  https://qiita.com/mpyw/items/886218e7b418dfed254b
        if (! preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!-\/:-@\[-`{-~]{10,20}$/', $request->password)) {
            return back()->withErrors(['password'=> '新しいパスワードは英字の大文字/小文字、数字を全て使用してください。']);
        }

        // ダミーアドレスを割り当てる
        // if ($req["role"] != 2 && $req["email"] == "") { 
        //     $request->email = $req["customer_code"]."@example.com"; // emailがヌルの場合  
        //     Log::debug("email-auto : " . $request->email);
        // }

        // 同一メールの判定等
        $user_search = User::where("email",$request->email);
        if ($user_search->count() > 0) {
            return back()->withInput()->withErrors(['email' => "Eメールアドレスはすでに使用されています"]);
            // return back()->withInput()->with('status', 'メッセージ表示テスト');
        }

        // 同一お客様コードの判定
        $user_search2 = User::where("customer_code",$request->customer_code);
        if ($user_search2->count() > 0) {
            return back()->withInput()->withErrors(['customer_code' => "お客様コードはすでに使用されています"]);
            // return back()->withInput()->with('status', 'メッセージ表示テスト');
        }

        $result = ["status" => 0];
        
        /** 作業者記載 ユーザコード */
        $user_code = Session::get('user_login.customer_code',"");

        // 記事追加を行う
        try
        {
            $user = new UserNow;
            $user->name     = $req["name"];
            $user->email    = $req["email"];
            $user->customer_code = $req["customer_code"];
            // $user->password = bcrypt('Aaaa123456'); // 初期パスワード
            $user->password = bcrypt($req["password"]); // 初期パスワード
            if ($req["role"] == 2) { 
                // 管理者
                $user->role     = 2; // 1:システム管理者\r\n2:主催者\r\n3:SA\r\n9:一般
                $user->email_verified_at = DB::raw('now()');
            } else {
                // テストユーザー
                $user->role     = 9; // 1:システム管理者\r\n2:主催者\r\n3:SA\r\n9:一般
                $user->email_verified_at = null;
            }
            $user->demouser = 1; // 1:検証用ユーザ\r\n9:一般
            $user->zip_code = '100-0000';
            $user->phone    = '090-0000-0000';
            $user->created_user_id = $user_code;
            $user->updated_user_id = $user_code;
            $user->save();
            // Todo:メール送信

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
        return back()->with('status', (($user->role == 2) ? "管理者" : "テストユーザー") . '追加しました。' . $request->email);
    }    

}
