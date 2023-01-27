<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * 解約通知メール（社内用）
 */
class CloseContractOfficeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.close_contract_office')
        ->text('email.close_contract_office')
        ->subject('【' . $this->data["plan_name"] . '】解約・引っ越し受付依頼／Mypageより')
        ->with([
            'data' => $this->data,
        ]);
    }
}
