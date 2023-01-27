<?php
namespace App\Extensions;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\Log;

// eloquent
use App\UserSub;

/**
 * traitをカスタムして、複数DBにログイン対応できるようにする
 * 
 */
trait AuthenticatesUsersExtra {
    use AuthenticatesUsers;

    /**
     * アプリケーションへのログイン要求を処理します。 from AuthenticatesUsers  
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        // バリデーション
        $this->validateLogin($request);

        // ロック判定
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        // ログイン試行
        if ($this->attemptLogin($request)) {
            Log::debug("Trait - AuthenticatesUsersExtra:");
            Log::debug($request);
            return $this->sendLoginResponse($request);
        }

        // ログイン試行回数を増やす
        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        // 失敗したログイン応答を送信する
        return $this->sendFailedLoginResponse($request);
    }
    
    /**
     * 要求から必要な認証資格情報を取得します。
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * ユーザーが認証された後で応答を送信します。
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        // セッションIDの再生成
        $request->session()->regenerate();

        // ログイン試行のクリア
        $this->clearLoginAttempts($request);
        $user_selected = [];
        
        // db1とdb2を仕分けして正しく受け渡せるようにする。
        $db_accesspoint = $request->session()->get('db_accesspoint', '0');
        if ( $db_accesspoint == 1 ) {
            Log::debug("db_accesspoint database1:");
            $user_selected = $this->guard()->user();  
        } elseif ($db_accesspoint == 2) {
            Log::debug("check database2:");
            // db_accesspoint_id をキーに
            $UserSub = new UserSub;
            $UserSub->setConnection('mysql2');
            $result = $UserSub->where('id', $request->session()->get('db_accesspoint_id', '0'));
            $user_selected = $result->first();
        } else {
            Log::debug("db_accesspoint session error - db_accesspoint is not set");
        }

        Log::debug($user_selected);

        if ($user_selected->role == 9) {
            return $this->authenticated($request, $user_selected)
                    ?: redirect()->intended($this->redirectPath());
        } else {
            return $this->authenticated($request, $user_selected)
                    ?: redirect()->intended('/home');
        }
        // return $this->authenticated($request, $this->guard()->user())
        // ?: redirect()->intended($this->redirectPath());
    }

}

