<?php

namespace App\Http\Controllers;

use App\Mail\PasswordReset;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Route;

// eloquent
use App\User;

/**
 * ログイン認証処理 のおおよそ
 */
class AuthController extends Controller
{
  /**
   * ログイン画面 表示
   */
  public function login($cid = null)
  {
    $inquiry = null;

    return view('login', ['cid' => $cid, 'inquiry' => $inquiry]);
  }


  /**
   * パスワード変更画面
   */
  public function password_init(Request $request)
  {
    $id = (isset($_REQUEST['id'])) ? $_REQUEST['id']: '';
    $email = (isset($_REQUEST['email'])) ? $_REQUEST['email']: '';
    $remember_token = (isset($request['remember_token'])) ? $request['remember_token']: '';
    $route_name = Route::currentRouteName();
    Log::debug( $route_name );
    return view('password_init', ['id' => $id, 'email'=>$email , 'remember_token'=>$remember_token , 'route_name'=>$route_name ]);
  }


  /**
   * パスワード変更処理
   */
  public function password_init_change(Request $request)
  {
    Log::debug("Controller: AuthController.php : password_init_change");
    // 画面入力データの取得
    $remember_token = (isset($request['remember_token'])) ? $request['remember_token']: '';
    $email = (isset($_REQUEST['email'])) ? $_REQUEST['email']: '';
    $route_name = Route::currentRouteName();
    Log::debug( $route_name );

    $password_new = (isset($_REQUEST['password_new'])) ? $_REQUEST['password_new']: '';
    // $password_new_confirm = (isset($_REQUEST['password_new_confirm'])) ? $_REQUEST['password_new_confirm']: '';

    // デフォルト方式でのバリデーション
    // ここでの confirmed は、password_new_confirmの比較を行う
    $validatedData = $request->validate([
      'password_new' => 'required|string|min:10|max:20|confirmed',
    ]);

    // バリデーションは PasswordChangeController と同様
    // 手動のバリデーション
    // 肯定先読み ex. 正規表現「肯定先読み」とは？ http://www-creators.com/archives/2746
    // ex. 言語別：パスワード向けの正規表現  https://qiita.com/mpyw/items/886218e7b418dfed254b
    if (! preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!-\/:-@\[-`{-~]{10,20}$/', $password_new)) {
      return  back()->withErrors(
         ['password_new'=> '新しいパスワードは英字の大文字/小文字、数字を全て使用してください。']
      )->withInput($request->all);
    }

    // remindersの照合処理
    try
    {
      // expiration_at の判定： リマインダー発行時刻から１時間が経過していたら無効である。
      //$results = DB::table('reminders')->where('email', $email)->where( 'expiration_at', '>' , Carbon::now())->first();
      // $results = User::where('email', $email)->first();
      Log::debug($email . " 確認開始");
      $results = User::on(($route_name == 'password_init2_change') ? 'mysql2' : 'mysql' )->where('email', $email)->where( 'reminder_expired_at', '>' , DB::raw('now()'))->first();
      $result = ["status" => 1];
      if ( $results !== null) {
        // URLにあった認証トークンが正しいか判定
        // password_hash は password_verifyで確認 https://www.php.net/manual/ja/function.password-verify.php
        if (password_verify( $remember_token , $results->password_reminder)) {
          Log::debug($remember_token . " 認証トークンは正しく照合できました");
          $result = ["status" => 0];
        } else {
          Log::debug($remember_token . " 認証トークンは正しく照合できませんでした");
        }
      } else {
        Log::debug($email . " リマインダー発行時刻から24時間が経過しています");
      }

    } catch (\PDOException $e) {
      Log::error($e);
      // セキュリティ上DBにアクセスできなかったことだけ返す
      $result = ["status" => 1];
    }
    // 失敗時は前画面に戻す
    if ($result["status"] == 1) {
      $error_msg = 'メールの有効期限が切れています。再度、ログイン画面からお手続きをお願いします。';
      return back()->withInput()->with('status', $error_msg);
    }

    // パスワード変更
    try
    {
      // パスワード変更に伴い認証トークンなどを初期化します
      $user = User::on(($route_name == 'password_init2_change') ? 'mysql2' : 'mysql' )->where('email', $email)->first();
      $user->password = Hash::make( $password_new );
      $user->reminder_expired_at = null;
      $user->password_reminder = null;
      $user->updated_user_id = 'from reminder';
      $user->save();

      // パスワードをしたユーザを記録する (code 999として記録)
      $request_sub = [
        "remember_token" => $remember_token,
        "email" => $email,
      ];

      // $operation_history_log = new \App\Http\Middleware\OperationHistoryMiddleware;
      // $operation_history_log->OperationHistory($request_sub, 999);

      // 更新完了のメール  
      Mail::to(mail_alias_replace($results->email))->send(new PasswordReset());

    } catch (\PDOException $e)
    {
      Log::error($e);
      return back()->withInput()->with('status', 'パスワード変更に失敗しました。');
    }

    return redirect('/')->with('status', 'パスワード変更しました。');
  }

  /**
   * 初回認証２ 初回パスワード変更
   * 初回メールリマインダー 到達確認
   * 初回パスワード変更 が行えないで時間経過の場合は、再度初回パスワードから。  
   */
  public function email_reminder(Request $request)
  {
      Log::debug("AuthController : email_reminder");
      // ToDo: database 初回は登録する
      // 更新されているので、トークンが合わない2回目以降は受け付けない
      // return redirect('/')->with('status', '初回メール確認を正常に完了しました。');

      $route_name = Route::currentRouteName();  
      // どちらのDBかurlから判定する
      if ($route_name == 'verification.email2_reminder') {
        $request->session()->put('db_accesspoint_now', '2');
      } else {
        $request->session()->put('db_accesspoint_now', '1');
      }

      $id = (isset($_REQUEST['id'])) ? $_REQUEST['id']: '';
      $email = (isset($_REQUEST['email'])) ? $_REQUEST['email']: '';
      $remember_token = (isset($request['remember_token'])) ? $request['remember_token']: '';
      Log::debug("取得パラメータ : " . $email . " : " . $remember_token);

      if ($email === '' || $remember_token === '' ) {
        abort(404);
      }

      return view('verify_password_init', ['id' => $id, 'email'=>$email , 'remember_token'=>$remember_token , 'route_name'=>$route_name  ]);
  }

  /**
   * 初回認証２ 初回パスワード変更処理
   * password_init_change に 初回認証手続きを加える
   */
  public function email_reminder_update(Request $request)
  {
    Log::debug("Controller: AuthController.php : email_reminder_update");
    // 画面入力データの取得
    $remember_token = (isset($request['remember_token'])) ? $request['remember_token']: '';
    $email = (isset($_REQUEST['email'])) ? $_REQUEST['email']: '';

    $password_new = (isset($_REQUEST['password_new'])) ? $_REQUEST['password_new']: '';
    // $password_new_confirm = (isset($_REQUEST['password_new_confirm'])) ? $_REQUEST['password_new_confirm']: '';
    $route_name = Route::currentRouteName(); 

    Log::debug("入力値 : " . $remember_token . " : " . $email . " : " . strlen($password_new) . " : " . $route_name);

    // デフォルト方式でのバリデーション
    // ここでの confirmed は、password_new_confirmの比較を行う
    $validatedData = $request->validate([
      'password_new' => 'required|string|min:10|max:20|confirmed',
    ]);

    // バリデーションは PasswordChangeController と同様
    // 手動のバリデーション
    // 肯定先読み ex. 正規表現「肯定先読み」とは？ http://www-creators.com/archives/2746
    // ex. 言語別：パスワード向けの正規表現  https://qiita.com/mpyw/items/886218e7b418dfed254b
    if (! preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!-\/:-@\[-`{-~]{10,20}$/', $password_new)) {
      return  back()->withErrors(
         ['password_new'=> '新しいパスワードは英字の大文字/小文字、数字を全て使用してください。']
      )->withInput($request->all);
    }

    // remindersの照合処理
    try
    {
      Log::debug($email . " 確認開始");
      $results = User::on(($route_name == 'verification.email2_reminder.update') ? 'mysql2' : 'mysql' )->where('email_new', $email)->where( 'reminder_expired_at', '>' , DB::raw('now()'))->first();
      $result = ["status" => 1];
      if ( $results === null) {
        $error_msg = '不正なURLからのアクセスです。URLが正しいかご確認ください。';
        return back()->withInput()->with('status', $error_msg);
      }

      // メールアドレスの重複チェック処理
      // 他のログインIDで同じメールアドレスが使われているか確認（本人の場合は更新許可）
      $user_id = $results->id;
      $is_used_by_others = User::where('id', '!=', $user_id)
                            ->where(function($query) use($email){
                                $query->where('email', $email)
                                ->orWhere('email_new', $email);
                        })->count();

      if ($is_used_by_others) {
        Log::debug('ID:' . $user_id . '/mail:' . $email);
        $error_msg = 'このメールアドレスは既に使用されています。';
        return  back()->withInput()->with('status', $error_msg);
      }

      // expiration_at の判定： リマインダー発行時刻から１時間が経過していたら無効である。
      //$results = DB::table('reminders')->where('email', $email)->where( 'expiration_at', '>' , Carbon::now())->first();
      // $results = User::where('email', $email)->first();
      if ( $results !== null) {
        // URLにあった認証トークンが正しいか判定
        // password_hash は password_verifyで確認 https://www.php.net/manual/ja/function.password-verify.php
        if (password_verify( $remember_token , $results->password_reminder)) {
          Log::debug($remember_token . " 認証トークンは正しく照合できました");
          $result = ["status" => 0];
        } else {
          Log::debug($remember_token . " 認証トークンは正しく照合できませんでした");          
        }
      } else {
        Log::debug($email . " リマインダー発行時刻から24時間が経過しています");
      }

    } catch (\PDOException $e) {
      Log::error($e);
      // セキュリティ上DBにアクセスできなかったことだけ返す
      $result = ["status" => 1];
    }
    // 失敗時は前画面に戻す
    if ($result["status"] == 1) {
      $error_msg = 'メールの有効期限が切れています。再度、ログイン画面からお手続きをお願いします。';
      return back()->withInput()->with('status', $error_msg);
    }

    // email,パスワード変更
    try
    {
      // パスワード変更に伴い認証トークンなどを初期化します
      $user = User::on(($route_name == 'verification.email2_reminder.update') ? 'mysql2' : 'mysql' )->where('email_new', $email)->first();
      $user->password = Hash::make( $password_new );
      $user->email = $email; // 新しいemailの値に改める
      $user->email_new = null; // 登録後は消す
      $user->email_verified_at = DB::raw('now()'); // 登録日時を記す
      $user->reminder_expired_at = null;
      $user->password_reminder = null;
      $user->updated_user_id = 'from reminder';
      $user->save();

      // パスワードをしたユーザを記録する (code 999として記録)
      $request_sub = [
        "remember_token" => $remember_token,
        "email" => $email,
      ];

      // $operation_history_log = new \App\Http\Middleware\OperationHistoryMiddleware;
      // $operation_history_log->OperationHistory($request_sub, 999);

      // 更新完了のメール  
      Mail::to( mail_alias_replace($email) )->send(new PasswordReset());

    } catch (\PDOException $e)
    {
      Log::error($e);
      return back()->withInput()->with('status', 'パスワード変更に失敗しました。');
    }
    
    // step3へ移動、完了とします。  
    if ($route_name == 'verification.email2_reminder.update') {
      return redirect('email2/complete')->with('status', '初回登録が完了しました。');
    }
    return redirect('email/complete')->with('status', '初回登録が完了しました。');
  }

  /**
   * 3 初回認証完了画面
   */
  public function email_complete(Request $request)
  {
    $route_name = Route::currentRouteName();  
    // どちらのDBかurlから判定する
    if ($route_name == 'verification.email2_complete') {
      $request->session()->put('db_accesspoint_now', '2');
    } else {
      $request->session()->put('db_accesspoint_now', '1');
    }
    return view('verify_complete', []);
  }

  /**
   * ログアウト
   */
  public function logout(Request $request)
  {
    // ログアウトしたユーザを記録する (code 999として記録)
    $operation_history_log = new \App\Http\Middleware\OperationHistoryMiddleware;
    $operation_history_log->OperationHistory($request, 999);

    Auth::logout();
    session()->flush();
    return redirect('/');
  }
}