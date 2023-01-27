<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * 解約通知メール（お客様用）
 */
class CloseContractCustomerMail extends Mailable
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
        return $this->view('email.close_contract_customer')
        ->text('email.close_contract_customer')
        ->subject('【' . $this->data["plan_name"] . '】手続き完了のお知らせ')
        ->with([
            'data' => $this->data,
        ]);
    }
}
