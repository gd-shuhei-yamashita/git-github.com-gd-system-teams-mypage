<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * パスワード変更 リマインダーメール
 * 
 * Mailableクラス
 */
class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $title = '【' . (( session()->get('db_accesspoint_now', '0') == 2) ? config('const.TitleName2') : config('const.TitleName')) . '】パスワード設定完了のお知らせ';
        # グランデータではタイトルはこちら
        if (config('const.ViewThame')[0] == 'views_himawari') {
            $title = "【グランデータマイページ】パスワード設定完了のお知らせ";
        }
        $this->title = $title;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.password_reset')
        ->text('email.password_reset_plain')
        ->subject($this->title)
        ->with([
            "service_name" => config('const.TitleName')
        ]);
    }
}
