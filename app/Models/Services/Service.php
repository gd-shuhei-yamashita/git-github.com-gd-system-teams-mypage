<?php

namespace App\Models\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

use App\Models\Services\Electric;
use App\Models\Services\Gas;
use App\Models\Services\Mobile;
use App\Models\Services\Option;

use App\Models\DB\Billing;
use App\Models\DB\BillingItemize;
use App\Models\Mallie\HalueneOptionPlan;

/**
 * サービスモデル
 */
class Service
{

    const SERVICE_TYPE_ELE = 'electric';
    const SERVICE_TYPE_GAS = 'gas';
    const SERVICE_TYPE_MOB = 'mobile';
    const SERVICE_TYPE_OPT = 'option';
    const PPS_TYPES_ELE = [1, 5];
    const PPS_TYPES_GAS = [2, 3, 4];

    const STATUS_LABEL = [
        0 => '',
        1 => '契約中',
        2 => '契約中',
        3 => '申込キャンセル',
        4 => '解約完了',
        5 => '解約完了',
        6 => '契約中',
        7 => '契約中',
    ];

    const STATUS_CODE = [
        0 => '',
        1 => 'active',
        2 => 'active',
        3 => 'cancel',
        4 => 'complete',
        5 => 'complete',
        6 => 'active',
        7 => 'active',
    ];

    const PAYMENT_METHODS = [
        '' => '',
        '0' => '',
        '1' => '口座振替',
        '2' => 'クレジットカード',
        '3' => 'コンビニ払い',
        '4' => '',
        '5' => '銀行窓口',
        '6' => '',
        '7' => '',
    ];

    public static $instanceCache = [];

    /**
     * インスタンス取得
     * @param string $customerCode
     * @return Object
     */
    public static function getInstance($customerCode = null) {
        if (is_null($customerCode)) {
            $customerCode = Session::get('user_now.customer_code');
        }
        $prefix = get_called_class();
        $cacheKey = $prefix.$customerCode;
        if (!isset(self::$instanceCache[$cacheKey])) {
            self::$instanceCache[$cacheKey] = new static();
            self::$instanceCache[$cacheKey]->setCustomerCode($customerCode);
        }
        return self::$instanceCache[$cacheKey];
    }

    /**
     * サービスタイプを判断します
     * @param string $ppsType
     * @param string $supplypoint
     * @return string
     */
    static public function getContractServiceName($ppsType, $supplypoint) {
        if ($ppsType) {
            if (in_array($ppsType, self::PPS_TYPES_ELE)) {
                return self::SERVICE_TYPE_ELE;
            }elseif (in_array($ppsType, self::PPS_TYPES_GAS)) {
                return self::SERVICE_TYPE_GAS;
            } elseif (substr($supplypoint, 0, 2) === 'GP') {
                return self::SERVICE_TYPE_MOB;
            } else {
                return self::SERVICE_TYPE_OPT;
            }
        } else {
            if (strlen($supplypoint) === 22) {
                return self::SERVICE_TYPE_ELE;
            } elseif ($supplypoint === 'wifi' || substr($supplypoint, 0, 2) === 'GP') {
                return self::SERVICE_TYPE_MOB;
            } elseif ($supplypoint === 0) {
                return self::SERVICE_TYPE_OPT;
            } else {
                return self::SERVICE_TYPE_GAS;
            }
        }
    }

    /**
     * ステータスを判定します
     * @param array $contract 契約データ
     * @param string $supplypoint
     * @return string
     */
    static public function getStatusName(&$contract) {
        switch ($contract['type']) {
            case self::SERVICE_TYPE_ELE:
            case self::SERVICE_TYPE_GAS:
            case self::SERVICE_TYPE_OPT:
                $query = DB::connection('mysql_mallie')->table('HalueneContract AS hc')
                    ->select(DB::raw('hc.status'))
                    ->join('CustomerOrdered AS co', 'co.id', '=', 'hc.customer_id')
                    ->where('hc.power_customer_location_number', $contract['supplypoint_code'])
                    ->where('co.code', $contract['customer_code'])
                    ->orderBy('hc.createdate', 'desc');
                $status = $query->count() > 0 ? $query->first()->status : 0;
                break;
            case self::SERVICE_TYPE_MOB:
                $status = $contract['mobile_status'] === '契約中' ? 1 : 0;
                break;
        }
        $contract['status_code'] = self::STATUS_CODE[$status];
        return self::STATUS_LABEL[$status];
    }


    /**
     * 使用量の単位を判定します
     * @param array $contract 契約データ
     * @return string
     */
    static public function getUsageUnit($contract) {
        $type = isset($contract['type']) ? $contract['type'] : self::getContractServiceName($contract['pps_type'], $contract['supplypoint_code']);
        switch ($type) {
            case self::SERVICE_TYPE_ELE:
                return 'kWh';
            case self::SERVICE_TYPE_GAS:
                return 'm3';
            case self::SERVICE_TYPE_MOB:
            case self::SERVICE_TYPE_OPT:
            default:
                return '';
        }
    }

    /**
     * 使用量の単位を判定します（Webに表示用）
     * @param array $contract 契約データ
     * @return string
     */
    static public function getUsageUnitForWeb($contract) {
        $type = isset($contract['type']) ? $contract['type'] : self::getContractServiceName($contract['pps_type'], $contract['supplypoint_code']);
        switch ($type) {
            case self::SERVICE_TYPE_ELE:
                return 'kWh';
            case self::SERVICE_TYPE_GAS:
                return 'm<sup>3</sup>';
            case self::SERVICE_TYPE_MOB:
            case self::SERVICE_TYPE_OPT:
            default:
                return '';
        }
    }

    /**
     * 各サービスモデルを呼び出せるようにする
     * @param string $customerCode
     * @return void
     */
    public function __call($method, $args) {
        switch ($method) {
            case 'Electric':
                return Electric::getInstance($this->customerCode);
            case 'Gas':
                return Gas::getInstance($this->customerCode);
            case 'Mobile':
                return Mobile::getInstance($this->customerCode);
            case 'Option':
                return Option::getInstance($this->customerCode);
            default:
                # code...
                break;
        }
    }

    /**
     * @var string $customerCode
     */
    public $customerCode = null;

    /**
     * ログイン中の顧客コードをセットする
     * @param string $customerCode
     * @return void
     */
    public function setCustomerCode($customerCode) {
        $this->customerCode = $customerCode;
    }

    /**
     * 全サービスの契約データを取得
     * @param string $yearMonth 年月
     * @return array
     */
    public function getContracts($yearMonth = null) {
        return array_merge(
            $this->Electric()->getContracts($yearMonth),
            $this->Gas()->getContracts($yearMonth),
            $this->Mobile()->getContracts($yearMonth),
            $this->Option()->getContracts($yearMonth)
        );
    }

    /**
     * 全サービスの請求データを取得
     * @param string $yearMonth 年月
     * @return array
     */
    public function getBillings($yearMonth) {
        return array_merge(
            $this->Electric()->getBillings($yearMonth),
            $this->Gas()->getBillings($yearMonth),
            $this->Mobile()->getBillings($yearMonth),
            $this->Option()->getBillings($yearMonth)
        );
    }

    /**
     * 契約のオプションデータを取得
     * @param string $supplypointCode 供給地点番号
     * @return array
     */
    public function getOptions($supplypointCode) {
        try {
            return HalueneOptionPlan::getOptions($this->customerCode, $supplypointCode);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return null;
        }
    }

    /**
     * 契約の利用月の詳細データを取得
     * @param string $supplypointCode 供給地点番号
     * @param string $yearMonth 利用月
     * @return array
     */
    public function getDetail($supplypointCode, $yearMonth) {
        $detail = Billing::getDetail($this->customerCode, $supplypointCode, $yearMonth);
        if (isset($detail)) {
            $detail['type'] = self::getContractServiceName($detail->pps_type, $supplypointCode);
            $detail['usage_unit_html'] = self::getUsageUnitForWeb($detail);
        }
        return $detail;
    }

    /**
     * 利用月の料金内訳データを取得
     * @param array $detail 詳細データ
     * @return array
     */
    public function getDetailItemize($detail) {
        $itemizeList = BillingItemize::getByItemizeCode($detail->itemize_code);
        if ($detail['type'] === self::SERVICE_TYPE_MOB) {
            // モバイル契約データと紐づくレコードはcontractテーブルに存在しない為、ここで格納
            $contract = $this->Mobile()->getContract();
            $detail['contract_name'] = $contract['contract_name'];
            $detail['address'] = $contract['address'];
            $detail['plan'] = $contract['plan'];
            // 非表示の内訳を削除
            foreach ($itemizeList as $key => $itemize) {
                if ($itemize['itemize_bill'] != 0 && $itemize['itemize_name'] != '消費税相当額') {
                    // OK
                } else {
                    unset($itemizeList[$key]);
                }
            }
        }
        return $itemizeList;
    }

}
