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
class ReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name)
    {

        $title = '【' . (( session()->get('db_accesspoint_now', '0') == 2) ? config('const.TitleName2') : config('const.TitleName')) . '】パスワード再設定';
        # グランデータではタイトルはこちら
        if (config('const.ViewThame')[0] == 'views_himawari') {
            $title = "【グランデータマイページ】パスワード再設定";
        }
        $this->title = $title;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.remind')
        ->text('email.remind_plain')
        ->subject($this->title)
        ->with([
            "service_name" => config('const.TitleName'),
            'reset_url' => $this->name,
        ]);
    }
}
