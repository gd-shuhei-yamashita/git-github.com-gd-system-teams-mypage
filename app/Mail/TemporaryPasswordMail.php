<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * パスワード変更 リマインダーメール
 * ※パスワード忘れ
 * 
 * Mailableクラス
 */
class TemporaryPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail_address, $password)
    {

        $title = '【' . (( session()->get('db_accesspoint_now', '0') == 2) ? config('const.TitleName2') : config('const.TitleName')) . 'パスワード登録確認';
        # グランデータではタイトルはこちら
        if (config('const.ViewThame')[0] == 'views_himawari') {
            $title = "【グランデータマイページ】パスワード登録確認";
        }
        $this->title = $title;
        $this->mail_address = $mail_address;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.temporary_password')
        ->text('email.temporary_password_plain')
        ->subject($this->title)
        ->with([
            "service_name" => config('const.TitleName'),
            'mail_address' => $this->mail_address,
            'password' => $this->password,
        ]);
    }
}
