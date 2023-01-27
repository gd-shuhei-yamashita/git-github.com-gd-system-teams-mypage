<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use DateTime;

use App\User;
use App\Notice;
use App\NoticeRelation;
use App\Mail\NoticeMail;

class SendNoticeMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send_notice_mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notice mail';

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
        // 現在日に公開のお知らせ取得
        $notice_query = Notice::whereDate('notice_date', date("Y-m-d"))->whereNull('deleted_at');

        if ($notice_query->count() > 0) {
            $notice = $notice_query->get();
            foreach ($notice as $value) {
                if ($value->send_email_flag) { // メール送信対象
                    // 公開範囲チェック
                    $notice_relation_query = NoticeRelation::where('notice_id', $value->id)->whereNull('deleted_at');
                    if ($notice_relation_query->count() > 0) { // 一部公開
                        $users = User::join('notice_relation', 'notice_relation.customer_code', 'users.customer_code')
                        ->where('notice_relation.notice_id', $value->id)
                        ->select('users.email')->get();                            
                    } else { // 全体公開
                        $users = User::select('users.email')->get();
                    }
                    foreach ($users as $user){
                        // メール送信
                        Mail::to(mail_alias_replace($user->email))->send(new NoticeMail($value->notice_comment));
                    }
                }
            }
        }
    }
}
