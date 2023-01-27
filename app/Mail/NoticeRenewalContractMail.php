<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * お知らせ公開メール
 */
class NoticeRenewalContractMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.notice_renewal_contract')
        ->text('email.notice_renewal_contract')
        ->subject('【重要】契約更新のお知らせが公開されました。')
        ->with([
            'mypage_url' => 'https://mypage.grandata-service.jp/',
        ]);
    }
}
