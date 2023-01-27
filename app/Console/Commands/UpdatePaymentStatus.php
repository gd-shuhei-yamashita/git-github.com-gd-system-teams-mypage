<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\PaymentStatus; 

class UpdatePaymentStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_payment_status {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update payment status';

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
        try {

            DB::beginTransaction();

            $result = DB::connection('redshift')->table('transaction_schema.credit_master_by_customer AS t1')
            ->select('t2.power_customer_location_number', 't1.billing_month', 't1.payment_amount_summaly', 't1.payment_type', 't1.csa_dwh_created_at')
            ->distinct()
            ->join('transaction_schema.credit_master_by_item AS t2', function ($join) {
                $join->on('t1.apply_number', 't2.apply_number');
                $join->on('t1.billing_month', 't2.billing_month');
                $join->on('t1.accounting_month', 't2.accounting_month');
            })
            ->where('t1.csa_dwh_created_at', '>=', date('Y-m-d H:i:s', strtotime($this->argument('date'))))
            ->get();
            Log::channel('importlog')->debug("Update payment status from: " . date('Y-m-d H:i:s', strtotime($this->argument('date'))));
            Log::channel('importlog')->debug("Update payment status count: " . $result->count());
            foreach ($result as $value) {
                $payment_status = PaymentStatus::firstOrNew(
                    [
                        'supplypoint_code' => $value->power_customer_location_number,
                        'billing_date' => $value->billing_month
                    ]
                );
                if ($payment_status->exists) {
                    $payment_status->payment_amount = $value->payment_amount_summaly;
                    $payment_status->payment_type = $value->payment_type;
                    $payment_status->created_at = $value->csa_dwh_created_at;
                    $payment_status->updated_user_id = 'update_payment_status_batch';
                    $payment_status->save();
                } else {
                    $payment_status->payment_amount = $value->payment_amount_summaly;
                    $payment_status->payment_type = $value->payment_type;
                    $payment_status->created_at = $value->csa_dwh_created_at;
                    $payment_status->created_user_id = 'update_payment_status_batch';
                    $payment_status->updated_user_id = 'update_payment_status_batch';
                    $payment_status->save();
                }
            }

            DB::commit();
            Log::channel('importlog')->debug("Update payment status : Success");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('importlog')->debug("Update payment status : Error");
            Log::channel('importlog')->debug($e);
        }
    }
}
