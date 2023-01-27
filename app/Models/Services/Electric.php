<?php

namespace App\Models\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Services\Service;
use App\Models\DB\Billing;
use App\Models\DB\Contract;



/**
 * 電気に関する情報を扱うモデル
 */
class Electric extends Service
{

    /**
     * 契約データを取得
     * @param string $yearMonth 年月
     * @return array
     */
    public function getContracts($yearMonth = null) {
        $contracts = Contract::getContracts($this->customerCode, $yearMonth);
        $electricContracts = [];
        foreach($contracts as $contract) {
            $ppsType = $contract['pps_type'];
            $supplypoint = $contract['supplypoint_code'];
            $contract['type'] = Service::getContractServiceName($ppsType, $supplypoint);
            $contract['status_name'] = Service::getStatusName($contract);
            if ($contract['type'] === self::SERVICE_TYPE_ELE){
                $electricContracts[] = $contract;
            }
        }
        return $electricContracts;
    }

    /**
     * 請求データを取得
     * @param string $yearMonth 年月
     * @return array
     */
    public function getBillings($yearMonth) {
        $contractList = $this->getContracts($yearMonth);
        foreach ($contractList as $key => $contract) {
            $supplypoint = $contract['supplypoint_code'];
            $customerCode = $contract['customer_code'];
            $billdingList = Billing::getList($supplypoint, $customerCode, $yearMonth);

            $contractList[$key]['billing_count'] = count($billdingList);
            $contractList[$key]['billing_amount'] = array_sum(array_column($billdingList, 'billing_amount'));
            $contractList[$key]['usage_date'] = isset($billdingList[0]['usage_date']) ? $billdingList[0]['usage_date'] : '';
        }
        return $contractList;
    }

}
