<?php

namespace App\Models\Services;

use App;

use Illuminate\Support\Facades\Log;
use App\Models\Services\Service;
use App\Models\DB\Billing;
use App\Models\DB\Contract;
use App\Models\API\MobileFileMaker;

/**
 * 電気に関する情報を扱うモデル
 */
class Mobile extends Service
{

    private $fileMakerDir = 'customer_info_read_file/';
    private $fileNamePrefix = 'customer_info_read_file_';
    private $fileExtension = '.csv';
    private $cache = [];

    /**
     * 契約データがあるかどうか
     * @return boolean
     */
    public function hasContract() {
        $wifi_flag = false;
        try {
            $fileMaker = new MobileFileMaker();
            $local_file = $fileMaker->getFileFromFmServer($this->fileMakerDir, $this->fileNamePrefix. $this->customerCode. $this->fileExtension);
            if (!empty($local_file)) {
                $mobile_contracts = $fileMaker->getCsvFileData($local_file);
                if (!empty($mobile_contracts) && !empty($fileMaker->formatForContract($mobile_contracts))) {
                    $wifi_flag = true;
                    $this->cache[$this->customerCode] = $fileMaker->formatForContract($mobile_contracts);
                }
            }
        } catch (\Throwable $th) {
            Log::error('FileMakerのデータにアクセスできませんでした。');
        }
        return $wifi_flag;
    }

    /**
     * 契約データを取得
     * @param string $yearMonth 年月
     * @return array
     */
    public function getContracts($yearMonth = null) {
        try {
            if (isset($this->cache[$this->customerCode])) {
                $contracts = $this->cache[$this->customerCode];
            } else {
                $fileMaker = new MobileFileMaker();
                $local_file = $fileMaker->getFileFromFmServer($this->fileMakerDir, $this->fileNamePrefix. $this->customerCode. $this->fileExtension);
                if (!empty($local_file)) {
                    $mobile_contracts = $fileMaker->getCsvFileData($local_file);
                    if (!empty($mobile_contracts)) {
                        $contracts = $fileMaker->formatForContract($mobile_contracts);
                    } else {
                        return [];
                    }
                }
            }
            foreach($contracts as $k => $contract) {
                $contract['type'] = self::SERVICE_TYPE_MOB;
                $contract['status_name'] = Service::getStatusName($contract);
                $contracts[$k] = $contract;
            }
            return $contracts;
        } catch (\Throwable $th) {
            Log::error('FileMakerのデータにアクセスできませんでした。');
            return [];
        }
    }

    /**
     * 契約データを取得
     * @param string
     * @return array
     */
    public function getContract() {
        try {
            $contents = $this->getContracts();
            if (count($contents) > 0) {
                return current($contents);
            }
            return [];
        } catch (\Throwable $th) {
            Log::error('FileMakerのデータにアクセスできませんでした。');
            return [];
        }
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


    /**
     * 配送日時変更用のURLを作成して返す
     * @return string
     */
    public function getDeliveryChangeUrl() {
        $param_data = base64_encode($this->customerCode);
        $param_str = 'param='.$param_data.'&check=1';
        if(App::environment('product')) {
            $wifi_delivery_date_change_url = config('const.ProdDeliveryDateMobileURL') . '/?' . $param_str;
        } else if (App::environment('staging')) {
            $wifi_delivery_date_change_url = config('const.DevDeliveryDateMobileURL') . '/?' . $param_str;
        } else {
            $wifi_delivery_date_change_url = '/?' . $param_str;
        }
        return $wifi_delivery_date_change_url;
    }
}
