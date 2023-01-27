<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

// use App\Service\MailService;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

use App\Mail\ChangePasswordNotification;
// use App\Exceptions\EmailNotSetException;

// use Validator;

// eloquent
use App\User; 


/**
 * パスワード変更画面
 */
class PasswordChangeController extends Controller
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
    public function create(Request $request)
    {
        return view('password_change');
    }

  /**
   * パスワード変更
   */
  public function store(Request $request)
  {
    // デフォルト方式でのバリデーション
    $validatedData = $request->validate([
        'password_new' => 'required|min:10|max:20|confirmed',
    ]);
    // return back()->withInput()->with('status', 'メッセージ表示テスト');

    $password_new          = (isset($_REQUEST['password_new'])) ? $_REQUEST['password_new']: '';
    //$password_new_confirm  = (isset($_REQUEST['password_new_confirm'])) ? $_REQUEST['password_new_confirm']: '';
    
    // 手動のバリデーション
    // 肯定先読み ex. 正規表現「肯定先読み」とは？ http://www-creators.com/archives/2746
    // ex. 言語別：パスワード向けの正規表現  https://qiita.com/mpyw/items/886218e7b418dfed254b
    if (! preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!-\/:-@\[-`{-~]{10,20}$/', $password_new)) {
        return  back()->withErrors(
           ['password_new'=> '新しいパスワードは英字の大文字/小文字、数字を全て使用してください。']
        )->withInput($request->all);
    }
    
    $result = ["status" => 0];

    // パスワード変更を行う $password
    try
    {
        $user_id = Session::get('user_now.id',0);
        $user = User::find( $user_id );
        // $user->password = Hash::make( $password_new );
        // if (empty($user->email)) {
        //     throw new EmailNotSetException;
        // }
        $user->password = bcrypt( $password_new );
        
        $user->save();
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

    // パスワード設定完了通知
    // # password_hash は password_verifyで確認 http://php.net/manual/ja/function.password-hash.php
    // $service = new MailService();

    // $site_title = config('const.TitleName');
    // $from_name = Session::get('user_now.name',"");

    // 言語別
    // $subject = '【' . $site_title . '】マイページパスワード設定完了のお知らせ';

    try {
        $name = Session::get('user_now.name',"");
        $text = 'これからもよろしくお願いいたします。';
        $to   = Session::get('user_now.email',"");
    
        // Mailableに詳細記載
        Mail::to(mail_alias_replace($to))->send(new ChangePasswordNotification($name, $text));
    } catch (\Exception $e) {
        Log::error($e);
        return redirect('home')->with('status', 'パスワード変更しました。');
    }

    // ページ遷移
    return redirect('home')->with('status', 'パスワード変更しました。');
  }

}
