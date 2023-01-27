<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * お知らせ公開メール（サンキューレター）
 */
class NoticeThankYouLetterMail extends Mailable
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
        return $this->view('email.notice_thank_you_letter')
        ->text('email.notice_thank_you_letter')
        ->subject('【重要】契約のお知らせ（契約締結後書面）が公開されました。')
        ->with([
            'mypage_url' => 'https://mypage.grandata-service.jp/',
        ]);
    }
}
