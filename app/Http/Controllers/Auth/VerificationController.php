<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;

use App\Mail\FirstCheckMail;

// eloquent
use App\User;

/**
 * 初回入力フォーム
 * 
 * 初回ログイン直後、
 * 1.新しいパスワードを入力願う。
 * 2.email未入力の場合必ず入力してもらう。
 * 
 * 参考元：
 * - Laravel Email Verification 5.7 using REST API  
 * https://stackoverflow.com/questions/52362927/laravel-email-verification-5-7-using-rest-api/52745390
 * 
 * - Laravel5.7のEmail Verificationを読む   
 * https://qiita.com/yamaji_daisuke/items/731868a4de6037794976
 */
class VerificationController extends Controller
{
    // 標準と異なり treitで VerifiesEmails 継承しない  
    // use VerifiesEmails;

    /**
     * 確認後にユーザーをリダイレクトする場所。
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * 新しいコントローラインスタンスを作成します。
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }


    /**
     * ● メール確認通知を表示します。
     * / hasVerifiedEmailが 設定されていれば ホーム画面に移動させます
     * Show the email verification notice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        Log::debug("VerificationController : show");
        return $request->user()->hasVerifiedEmail()
                        ? redirect($this->redirectPath())
                        : view('verify');
    }
    
    /**
     * 初回認証1 初回email登録 
     * 1.変更予定の email_new を一時領域に記録   
     * 2.初回メール確認メール送信
     */
    public function update(Request $request)
    {
        Log::debug("VerificationController : update");
        //ToDo: email、パスワードの内容確認を行う。
        $req = $request->all();
        Log::debug($req);
        // return back()->withInput()->with('status', 'メッセージ表示テスト');
        
        // デフォルト方式でのバリデーション
        $validatedData = $request->validate([
            'email' => 'required|email|confirmed',
            // email_confirmation の起動  
        ]);
        
        // バリデーションを通過したのであれば確認メールを送る。
        // 確認メールが正しく確認できた場合に、はじめてメール変更を受け付けるようになる
        Log::debug("validation passed.");
        
        try {
            // メールアドレスの重複チェック処理
            // 他のログインIDで同じメールアドレスが使われているか確認（本人の場合は更新許可）
            $user_id = Session::get('user_now.id',0);
            $is_used_by_others = User::where('id', '!=', $user_id)
                            ->where(function($query) use($req){
                                $query->where('email', $req['email'])
                                ->orWhere('email_new', $req['email']);
                            })->count();

            if ($is_used_by_others) {
                $error_msg = 'このメールアドレスは既に使用されています。';
                return  back()->withErrors(
                    ['msg'=> $error_msg]
                )->withInput($request->all);
            }
        } catch (\PDOException $e) {
            Log::error($e);
            // セキュリティ上DBにアクセスできなかったことだけ返す
            return back()->withInput()->with('status', '更新に失敗いたしました');
        }

        $result = ["status" => 0];
        $uuid = get_csrf_token(); // from common/helper.php  
        $remember_token = password_hash($uuid, PASSWORD_DEFAULT);
        Log::debug("remember_token hash : " . $remember_token);
        // メールアドレスを一時領域のテーブルに記載する。比較できるように
        try
        {
            // $user_id = Session::get('user_now.id',0);
            $user = User::find( $user_id );

            $user->email_new = $req['email']; // email_new 一時保存のテーブル
            $user->reminder_expired_at = DB::raw('DATE_ADD(NOW(), INTERVAL 24 HOUR)'); // 24時間を期限
            $user->password_reminder  = $remember_token; // リメンバートークン発行
            $user->updated_user_id = Session::get('user_now.customer_code', 'undefined');
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

        // メールアドレスに送信する
        $email_new = $req['email'];
        // DBの種類に応じて仕訳
        $url= ((session()->get('db_accesspoint_now', '0') == 2) ? route('verification.email2_reminder') : route('verification.email_reminder')) . "?remember_token={$uuid}&email=" . urlencode($email_new);
        
        // 初回メール確認メール送信
        Mail::to(mail_alias_replace($email_new))->send(new FirstCheckMail($url));

        // ログイン画面へのリダイレクトの際にログアウト処理は行っておく。
        // ログアウトしたユーザを記録する (code 999として記録)
        $operation_history_log = new \App\Http\Middleware\OperationHistoryMiddleware;
        $operation_history_log->OperationHistory($request, 999);

        Auth::logout();
        session()->flush();

        return redirect('/')->with('status', 'メール送信を行いました。');

    }

    /**
     * 認証済みユーザーの電子メールアドレスを確認済みとしてマークします。
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        Log::debug("VerificationController : verify");
        if ($request->route('id') != $request->user()->getKey()) {
            throw new AuthorizationException;
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect($this->redirectPath())->with('verified', true);
    }



    /**
     * ToDo:メール確認通知を再送信します。
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        Log::debug("VerificationController : resend");
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }


}
