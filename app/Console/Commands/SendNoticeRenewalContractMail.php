<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use DateTime;

use App\User;
use App\Mail\NoticeRenewalContractMail;
use App\Exceptions\EmailNotSetException;
use App\Http\Controllers\ContractRenewalController;

class SendNoticeRenewalContractMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:send_notice_renewal_mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send mail';

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
        // 全ユーザ取得
        $users = User::select('*')->whereNull('deleted_at')->get();

        $renewal = new ContractRenewalController();
        $today = (new DateTime())->setTime(0, 0);
        foreach ($users as $user) {
            // ユーザがマイページで見る権限のある、契約更新お知らせ期間内の契約を取得
            $renewal_contracts = $renewal->check_contract_renewal($user->customer_code);
            foreach ($renewal_contracts as $contract) {
                // 実行日がお知らせ公開日の場合、メール送信
                if ($today == $contract['delivery_date'] ) {
                    Mail::to(mail_alias_replace($user->email))->send(new NoticeRenewalContractMail());
                    break;
                }
            }
        }
    }
}
