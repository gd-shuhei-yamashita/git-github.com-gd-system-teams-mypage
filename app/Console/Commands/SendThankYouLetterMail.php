<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use DateTime;

use App\User;
use App\Mail\NoticeThankYouLetterMail;
use App\Exceptions\EmailbuNotSetException;
use App\Http\Controllers\ContractNoticeController;

class SendThankYouLetterMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:send_notice_thankyou_letter_mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail thankyouletter';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $notice = new ContractNoticeController();
        $users = $notice->get_users_thankyou_letter_notice();

        if (!empty($users)) {
            foreach ($users as $user) {
                Mail::to(mail_alias_replace($user->email))->send(new NoticeThankYouLetterMail());
            }
        }
    }
}
