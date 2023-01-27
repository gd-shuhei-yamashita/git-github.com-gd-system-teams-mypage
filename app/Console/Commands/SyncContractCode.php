<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Contract;
use App\Consts\SupplierConsts;

class SyncContractCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_contract_code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync contract code';

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
            // contractテーブルのレコードを取得（contract_code,pps_typeが未設定、supplypoint_codeが設定済、10万件ずつ）
            $contracts = Contract::whereNotIn('supplypoint_code', ['0', '1', '2', '3', '4', '5', 'key'])
            ->whereNull('contract_code')
            ->whereNull('pps_type')
            ->limit(100000)
            ->get();

            foreach ($contracts as $contract) {
                if (!empty($contract->customer_code) && !empty($contract->supplypoint_code)) {
                    // マリーの契約レコードクエリ
                    $mallie_contract_query = DB::connection('mysql_mallie')->table('HalueneContract AS HC')
                    ->join('CustomerOrdered AS CO', 'CO.id', 'HC.customer_id')
                    ->where('CO.code', $contract->customer_code)
                    ->where('HC.power_customer_location_number', $contract->supplypoint_code)
                    ->select('HC.code AS contract_code', 'HC.power_customer_location_number', 'CO.code AS customer_code', 'HC.pps_business_number');

                    $mallie_contract = '';
                    if ($mallie_contract_query->count() == 1) { // 顧客コード、供給地点特定番号で一意になるケース
                        $mallie_contract = $mallie_contract_query->first();
                    } else if($mallie_contract_query->count() > 1) { // 顧客コード、供給地点特定番号で複数になるケース。HalueneContract.status != 2のレコードを取得
                        $mallie_contract_query->where('HC.status', '!=', 2);
                        $mallie_contract = $mallie_contract_query->first();
                    }

                    // マイページ契約レコードの更新
                    if (!empty($mallie_contract)) { 
                        $contract->contract_code = $mallie_contract->contract_code;
                        if ($mallie_contract->pps_business_number == SupplierConsts::GRANDATA_ELECTRIC) {
                            $contract->pps_type = SupplierConsts::GRANDATA_ELECTRIC_INDEX;
                        } else if ($mallie_contract->pps_business_number == SupplierConsts::GRANDATA_GAS) {
                            $contract->pps_type = SupplierConsts::GRANDATA_GAS_INDEX;
                        } else if ($mallie_contract->pps_business_number == SupplierConsts::SAISAN_GAS) {
                            $contract->pps_type = SupplierConsts::SAISAN_GAS_INDEX;
                        } else if ($mallie_contract->pps_business_number == SupplierConsts::FAMILY_NET_JAPAN_GAS) {
                            $contract->pps_type = SupplierConsts::FAMILY_NET_JAPAN_GAS_INDEX;
                        } else if ($mallie_contract->pps_business_number == SupplierConsts::HTB_ENERGY_ELECTRIC) {
                            $contract->pps_type = SupplierConsts::HTB_ENERGY_ELECTRIC_INDEX;
                        }
                        $contract->updated_user_id = 'sync_contract_code';
                        $contract->save();
                    }
                }
            }
            DB::commit();
            Log::debug('Success');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::debug('error');
            Log::debug($e);
        }
    }
}
