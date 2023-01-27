<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

// eloquent
use App\User;

class ChangeEmailAddressController extends Controller
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
        return view('change_email_address');
    }


    public function update(Request $request)
    {

        ## 入力されたメールアドレス
        $mail_address     = (isset($_REQUEST['mail_address'])) ? $_REQUEST['mail_address']: '';
        $re_mail_address  = (isset($_REQUEST['re_mail_address'])) ? $_REQUEST['re_mail_address']: '';

        # check validation
        $error_msg = $this->error_chk($mail_address, $re_mail_address);
        if ($error_msg) {
            return  back()->withErrors(
                ['msg'=> $error_msg]
            )->withInput($request->all);

        } 
        
        // メールアドレス更新処理 ここから
        $message = 'メールアドレスの更新に成功しました。';

        /** 作業者記載 ユーザコード */
        $user_code = Session::get('user_login.customer_code',"");

        try {
            // $user_id = Session::get('user_now.id',0);
            // メールアドレスの重複チェック処理
            // 他のログインIDで同じメールアドレスが使われているか確認（本人の場合は更新許可）
            $user_id = Session::get('user_now.id',0);
            $is_used_by_others = User::where('id', '!=', $user_id)
                            ->where(function($query) use($mail_address){
                                $query->where('email', $mail_address)
                                ->orWhere('email_new', $mail_address);
                            })->count();

            if ($is_used_by_others) {
                $error_msg = 'このメールアドレスは既に使用されています。';
                return  back()->withErrors(
                    ['msg'=> $error_msg]
                )->withInput($request->all);
            }

            $user = User::find( $user_id );
            $user->email = $mail_address;
            $user->updated_user_id = $user_code;                
            $user->save();
            $request->session()->put('user_now.email', $mail_address);
        } catch (\PDOException $e){
            Log::error($e);
            $message = 'メールアドレスの変更に失敗しました。';
        }

        return redirect('home')->with('status', $message);
    }


    /**
     *  メールアドレスのチェック
     *
     *  ERROR有： return string
     *  ERROR無： return false
     */
    private function error_chk($mail_1, $mail_2)
    {

        if ($mail_1 != $mail_2) {

            return 'メールアドレスが一致しておりません。';

        } else {

            if ($mail_1 == '') {
                return 'メールアドレスが入力されておりません。';
            }

            if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\+\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail_1)) {
                return 'メールアドレス形式で入力してください。';
            }
        }

        return false;
    }
}
