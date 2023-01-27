<?php

namespace App\Models\DB;

use App\Facades\GetInvoice;
use App\Models\DB\BaseModel;
use Illuminate\Support\Facades\Log;

/**
 * 請求データモデル Billing
 */
class Billing extends BaseModel
{
    // ex. Laravel5.7における複合プライマリキーを持つテーブルへの挿入と更新
    //      https://qiita.com/hidea/items/968c1013a7663de8d9cc
    use \LaravelTreats\Model\Traits\HasCompositePrimaryKey;

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'billing';
    protected $primaryKey = ['supplypoint_code', 'customer_code', 'billing_code', 'itemize_code'];
    protected $guarded = array();
    public $incrementing = false;

    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = true;


    // belongsTo設定
    public function user()
    {
        return $this->belongsTo('App\Models\DB\User');
    }

    /**
     * 請求データを取得
     * @param string $supplypoint 供給地点特定番号
     * @param string $customerCode 顧客コード
     * @param string $yearMonth 年月
     * @return array
     */
    public static function getList($supplypoint, $customerCode, $yearMonth)
    {
        $whereraw = GetInvoice::get_assignment_whereraw($supplypoint, $customerCode);
        $results = self::selectRaw('billing_amount, usage_date')
            ->where('supplypoint_code', $supplypoint)
            ->where('customer_code', $customerCode)
            ->whereRaw($whereraw)
            ->where('billing_date', $yearMonth)
        ;
        return $results->get()->toArray();
    }


    /**
     * 請求データの最古、最新の年月を取得する
     * @param string $customerCode 顧客コード
     * @return array
     */
    public static function getBillingRangeAll($customerCode)
    {
        // 契約一覧を取得
        $contracts = GetInvoice::get_supplypoint_list($customerCode);

        $first = '';
        $latest = '';
        foreach ($contracts as $contract) {
            $range = self::getBillingRange($contract['customer_code'], $contract['supplypoint_code']);
            $f = $range['first_billing_date'];
            $l = $range['latest_billing_date'];
            if (empty($first) || (!empty($f) && $f < $first)) {
                $first = $f;
            }
            if (empty($latest) || (!empty($l) && $latest < $l)) {
                $latest = $l;
            }
        }

        return [
            'first_billing_date' => $first,
            'latest_billing_date' => $latest,
        ];
    }

    /**
     * 請求データの最古、最新の年月を取得する
     * @param string $customerCode 顧客コード
     * @return array
     */
    public static function getBillingRange($customerCode, $supplypointCode)
    {

        $first_billing_date = '';
        $latest_billing_date = '';

        // 請求データ取得クエリ
        $whereraw = GetInvoice::get_assignment_whereraw($supplypointCode, $customerCode);
        $results = self::where('supplypoint_code', $supplypointCode)
            ->where('customer_code', $customerCode)
            ->whereRaw($whereraw);
        // 請求データが１件以上あるとき、請求年月の最古と最新を取得
        if ($results->count() > 0) {
            $billing_range = $results->selectRaw('max(billing_date) as latest_billing_date ,min(billing_date) as first_billing_date')->first();
            $f = $billing_range->first_billing_date;
            if (empty($first_billing_date) || (!empty($f) && $f < $first_billing_date)) {
                $first_billing_date = $f;
            }
            $l = $billing_range->latest_billing_date;
            if (empty($latest_billing_date) || (!empty($l) && $latest_billing_date < $l)) {
                $latest_billing_date = $l;
            }
        }

        return [
            'first_billing_date' => $first_billing_date,
            'latest_billing_date' => $latest_billing_date,
        ];
    }

    /**
     * 供給地点に対してその年の請求データを取得する
     * @param string $customerCode 顧客コード
     * @param string $supplypointCode 供給地点特定番号
     * @param int $year 対象年
     * @return array
     */
    public static function getSupplypointYearList($customerCode, $supplypointCode, $year)
    {
        $whereraw = GetInvoice::get_assignment_whereraw($supplypointCode, $customerCode);
        $results = Billing::where('supplypoint_code', $supplypointCode)
            ->where('customer_code', $customerCode)
            ->whereBetween('usage_date', [ $year.'01', $year.'12'])
            ->whereRaw($whereraw)
            ->orderBy('usage_date', 'asc')
        ;
        return $results->get()->toArray();
    }

    /**
     * その年の請求データを取得する
     * @param string $customerCode 顧客コード
     * @param int $yearMonth 対象年月
     * @return array
     */
    public static function getYearMonthList($customerCode, $yearMonth)
    {
        $results = Billing::selectRaw('billing.*')
            ->selectRaw('payment_status.payment_amount')
            ->selectRaw('contract.contract_code, contract.contract_name, contract.address, contract.plan, contract.pps_type')
            ->selectRaw('users.email, users.name, users.phone')
            ->selectRaw('usage_t.usage')
            ->selectRaw('payment_status.payment_amount')
            ->join('contract', function ($join) {
                $join->on('contract.customer_code', 'billing.customer_code');
                $join->on('contract.supplypoint_code', 'billing.supplypoint_code');
            })
            ->join('users', function ($join) {
                $join->on('contract.customer_code', 'users.customer_code');
            })
            ->join('usage_t', function ($join) {
                $join->on('usage_t.supplypoint_code', 'billing.supplypoint_code');
                $join->on('usage_t.customer_code', 'billing.customer_code');
                $join->on('usage_t.usage_date', 'billing.usage_date');
            })
            ->leftJoin('payment_status', function ($join) {
                $join->on('payment_status.supplypoint_code', 'billing.supplypoint_code');
                $join->on('payment_status.billing_date', 'billing.billing_date');
            })
            ->where('billing.customer_code', $customerCode)
            ->where('billing.usage_date', $yearMonth)
            // ->where('usage_t.usage_date', $yearMonth)
            // ->whereRaw($whereraw)
            ->orderBy('billing.usage_date', 'asc')
        ;
        return $results->get()->toArray();
    }


    /**
     * 詳細データを取得する
     * @param string $customerCode 顧客コード
     * @param string $supplypointCode 供給地点特定番号
     * @param int $yearMonth 対象年月
     * @return array
     */
    public static function getDetail($customerCode, $supplypointCode, $yearMonth)
    {
        $results = Billing::selectRaw('billing.*')
            ->selectRaw('payment_status.payment_amount')
            ->selectRaw('contract.contract_code, contract.contract_name, contract.address, contract.plan, contract.pps_type')
            ->selectRaw('users.email, users.name, users.phone')
            ->selectRaw('usage_t.usage')
            ->selectRaw('payment_status.payment_amount')
            ->join('contract', function ($join) {
                $join->on('contract.customer_code', 'billing.customer_code');
                $join->on('contract.supplypoint_code', 'billing.supplypoint_code');
            })
            ->join('users', function ($join) {
                $join->on('contract.customer_code', 'users.customer_code');
            })
            ->join('usage_t', function ($join) {
                $join->on('usage_t.supplypoint_code', 'billing.supplypoint_code');
                $join->on('usage_t.customer_code', 'billing.customer_code');
                $join->on('usage_t.usage_date', 'billing.usage_date');
            })
            ->leftJoin('payment_status', function ($join) {
                $join->on('payment_status.supplypoint_code', 'billing.supplypoint_code');
                $join->on('payment_status.billing_date', 'billing.billing_date');
            })
            ->where('billing.supplypoint_code', $supplypointCode)
            ->where('billing.customer_code', $customerCode)
            ->where('billing.usage_date', $yearMonth)
            // ->where('billing.billing_date', $yearMonth)
        ;
        return $results->first();
    }
}
