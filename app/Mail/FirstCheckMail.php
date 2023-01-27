<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * マイページ初回メールアドレス登録確認メール
 */
class FirstCheckMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name)
    {
        $title = '【' . (( session()->get('db_accesspoint_now', '0') == 2) ? config('const.TitleName2') : config('const.TitleName')) . '】マイページ初回メールアドレス登録確認';
        # グランデータではタイトルはこちら
        if (config('const.ViewThame')[0] == 'views_himawari') {
            $title = "【グランデータマイページ】メールアドレス登録確認";
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
        return $this->view('email.first_check')
        ->text('email.first_check_plain')
        ->subject($this->title)
        ->with([
            "service_name" => config('const.TitleName'),
            'reset_url' => $this->name,
          ]);
        // return $this->view('view.name');
    }
}
