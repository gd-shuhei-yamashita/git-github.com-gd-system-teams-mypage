<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Contract;

class SyncContractSwitchingScheduledDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_contract_switching_scheduled_date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync contract switching_scheduled_date';

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
            // contractテーブルのレコードを取得（brand_idがNULL、10万件ずつ）
            $contracts = Contract::whereNotNull('contract_code')
            ->whereNull('switching_scheduled_date')
            ->limit(100000)
            ->get();

            foreach ($contracts as $contract) {
                if (!empty($contract->customer_code) && !empty($contract->contract_code)) {
                    // マリーの契約レコードクエリ
                    $mallie_contract_query = DB::connection('mysql_mallie')->table('HalueneContract AS HC')
                    ->join('CustomerOrdered AS CO', 'CO.id', 'HC.customer_id')
                    ->where('CO.code', $contract->customer_code)
                    ->where('HC.code', $contract->contract_code)
                    ->select('HC.switching_scheduled_date');

                    $mallie_contract = '';
                    if ($mallie_contract_query->count() == 1) { // 一意になるケース
                        $mallie_contract = $mallie_contract_query->first();
                    } else if($mallie_contract_query->count() > 1) { // 一意にならないケース。HalueneContract.status != 2のレコードを取得
                        $mallie_contract_query->where('HC.status', '!=', 2);
                        $mallie_contract = $mallie_contract_query->first();
                    }

                    // マイページ契約レコードの更新
                    if (!empty($mallie_contract)) { 
                        $contract->switching_scheduled_date = $mallie_contract->switching_scheduled_date;
                        $contract->save();
                    }
                }
            }
            DB::commit();
            $this->info('Success');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('Error');
            Log::debug($e);
        }
    }
}
