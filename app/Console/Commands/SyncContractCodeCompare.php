<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Contract;
use App\Consts\SupplierConsts;

class SyncContractCodeCompare extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_contract_code_compare';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copare mypage mallie';

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
            // contractテーブルのレコードを取得（contract_code,pps_type,supplypoint_codeが未設定、10万件ずつ）
            $contracts = Contract::whereNull('contract_code')
            ->whereNull('pps_type')
            ->limit(100000)
            ->get();

            foreach ($contracts as $contract) {
                if (!empty($contract->customer_code)) {
                    // マリーの契約レコードクエリ
                    $mallie_contract_query = DB::connection('mysql_mallie')->table('HalueneContract AS HC')
                    ->join('CustomerOrdered AS CO', 'CO.id', 'HC.customer_id')
                    ->join('HaluenePowerPlan AS HPP', 'HPP.id', 'HC.power_plan_id')
                    ->where('CO.code', $contract->customer_code)
                    ->where('HC.status', '!=', 2)
                    ->select('HC.code AS contract_code', 'HC.power_customer_location_number', 'CO.code AS customer_code', 'HC.pps_business_number', 'HPP.name')
                    ->selectRaw(
                        'CASE HC.power_location_address_type
                        WHEN 1 THEN CONCAT(IFNULL(CO.prefecture, ""), IFNULL(CO.city, ""), IFNULL(CO.town, ""), IFNULL(CO.street_number_choume, ""), IF(CHAR_LENGTH(CO.street_number_choume) != 0,"　",""), IFNULL(CO.street_number_banchi, ""), IF(CHAR_LENGTH(CO.building_name) != 0,"　",""), IFNULL(CO.building_name, ""))
                        ELSE CONCAT(IFNULL(HC.power_prefecture, ""),IFNULL(HC.power_city, ""),IFNULL(HC.power_town, ""),IFNULL(HC.power_street_number_choume, ""), IF(CHAR_LENGTH(HC.power_street_number_choume) != 0,"　",""),IFNULL(HC.power_street_number_banchi, ""), IF(CHAR_LENGTH(HC.power_building_name) != 0,"　",""), IFNULL(HC.power_building_name, ""), IFNULL(HC.power_room_number, ""))
                        END AS address'
                    );
                    if ($mallie_contract_query->count() == 1) { // 顧客コードで一意になるケース(それ以外のケースは手作業反映予定)
                        $mallie_contract = $mallie_contract_query->first();

                        $mallie_address = preg_replace("/( |　)/", "", mb_convert_kana($mallie_contract->address, "a"));
                        $mypage_address = preg_replace("/( |　)/", "", mb_convert_kana($contract->address, "a"));
                        $this->info('0,mallie,' . $contract->customer_code . ','. $mallie_contract->contract_code . ',\'' . $mallie_contract->power_customer_location_number . ',' . $mallie_contract->name . ',' . $mallie_address);
                        $this->info('0,mypage,' . $contract->customer_code . ','. $contract->contract_code . ',\'' . $contract->supplypoint_code . ',' . $contract->plan . ',' . $mypage_address);

                        if ($mallie_address == $mypage_address) {
                            // マイページ契約レコードの更新
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
                            $contract->updated_user_id = 'sync_contract_code_by_customer_code_and_plan';
                            // $contract->save();
                        }

                    } else if ($mallie_contract_query->count() > 1) {
                        $i = 1;
                        foreach ($mallie_contract_query->get() as $mallie_contract) {
                            $mallie_address = preg_replace("/( |　)/", "", mb_convert_kana($mallie_contract->address, "a"));
                            $mypage_address = preg_replace("/( |　)/", "", mb_convert_kana($contract->address, "a"));
                            $this->info($i . ',mallie,' . $contract->customer_code . ','. $mallie_contract->contract_code . ',\'' . $mallie_contract->power_customer_location_number . ',' . $mallie_contract->name . ',' . $mallie_address);
                            $this->info($i . ',mypage,' . $contract->customer_code . ','. $contract->contract_code . ',\'' . $contract->supplypoint_code . ',' . $contract->plan . ',' . $mypage_address);
                            $i++;
                        }
                    }
                }
            }
            DB::commit();
            $this->info('Success_by_customer_code_and_plan');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->info('Error_by_customer_code_and_plan');
            Log::debug($e);
        }
    }
}
