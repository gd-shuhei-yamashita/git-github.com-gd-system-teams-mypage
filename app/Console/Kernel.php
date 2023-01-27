<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SendThankYouLetterMail::Class,
        Commands\SendNoticeMail::Class,
        Commands\UpdatePaymentStatus::Class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // サンキューレター作成お知らせメール送信バッチ
        $schedule->command('send_notice_thankyou_letter_mail')->dailyAt('11:00');
        // お知らせ公開メール送信バッチ(毎日11:00)
        $schedule->command('send_notice_mail')->dailyAt('11:00');

        // 支払い状況データ取込バッチ(毎月20日11:00、先月の20日以降の更新分取込)
        $schedule->command('update_payment_status ' . date('Ym20', strtotime('-1 month')))->monthlyOn(20, '10:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
