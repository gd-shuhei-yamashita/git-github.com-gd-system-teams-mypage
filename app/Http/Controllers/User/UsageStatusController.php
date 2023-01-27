<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// eloquent
use App\Models\DB\Billing;
use App\Models\DB\BillingItemize;
use App\Models\DB\UsageT;
use App\Models\DB\User;

use App\Models\Services\Service;
use App\Models\Services\Mobile;


/**
 *   使用量・請求金額画面
 */
class UsageStatusController extends LoginedController
{

    /**
     * 初期画面表示
     * @param Illuminate\Http\Request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // $Service = Service::getInstance();
        // $contracts = $Service->getContracts();
        return view('renewal.usage.index');
    }

    /**
     * 表示するプルダウン内容を取得
     * @param Illuminate\Http\Request
     * @return json
     */
    public function pulldown(Request $request)
    {
        // 契約情報取得
        $Service = Service::getInstance();
        $contracts = $Service->getContracts();

        // 各プルダウン用のデータ作成
        try {
            $contractList    = [];
            foreach ($contracts as $key => $contract) {
                $supplypointCode = $contract['supplypoint_code'];
                $contractList[$supplypointCode] = [
                    'supplypoint_code' => $supplypointCode,
                    'type' => $contract['type'],
                    'address' => $contract['address'],
                    'plan_name'   => $contract['plan'],
                    'use_period'  => Billing::getBillingRange($this->customerCode, $supplypointCode),
                    'user_name'   => $contract['contract_name'],
                    'status_name' => $contract['status_name'],
                    'status_code' => $contract['status_code'],
                ];
            }
            $viewData = [
                'serviceTypeList' => array_unique(array_column($contractList, 'type')),
                'addressList'     => array_unique(array_column($contractList, 'address')),
                'contractList'    => $contractList
            ];
        } catch (\Throwable $th) {
            return $this->customAjaxError('エラーが発生しました');
        }

        return $this->customAjaxResponse([
            'html' => view('renewal/usage/plan', $viewData)->render(),
            'contract_list' => $contractList,
            'range' => $billingRange = Billing::getBillingRangeAll($this->customerCode)
        ]);
    }

    /**
     * 月別のデータを取得
     * @param Illuminate\Http\Request
     * @return json
     */
    public function monthly_billing(Request $request)
    {
        $year = $request->get('year');
        $supplypointCode = $request->get('supplypoint_code');

        $results = Billing::getSupplypointYearList($this->customerCode, $supplypointCode, $year);
        if (count($results) === 0) {
            return $this->customAjaxError('データが見つかりませんでした');
        }

        $usages = UsageT::getUsages($this->customerCode, $supplypointCode, $year);

        foreach ($results as $billing) {
            $billing['billing_amount'] = number_format($billing['billing_amount']);
            $billing['usage'] = isset($usages[$billing['usage_date']]) ? $usages[$billing['usage_date']] : '';
            $billings[] = $billing;
        }

        $type = Service::getContractServiceName('', $supplypointCode);
        $downloadable = !in_array($type, [Service::SERVICE_TYPE_MOB, Service::SERVICE_TYPE_OPT]);

        return $this->customAjaxResponse([
            'html' => view('renewal/usage/billing_list', ['billingList' => array_reverse($billings), 'downloadable' => $downloadable])->render(),
            'billing_list' => $billings
        ]);
    }

    /**
     * 4 CSV/XLS 一括出力
     * 使用場所、対象年月に従ったcsv/xlsを出力。
     * @param Illuminate\Http\Request
     * @return json
     */
    public function export_chart(Request $request)
    {
        $year = $request->get('year');
        $supplypointCode = $request->get('supplypoint_code');
        $results = Billing::getSupplypointYearList($this->customerCode, $supplypointCode, $year);
        if (count($results) === 0) {
            return $this->customAjaxError('データが見つかりませんでした');
        }
        $itemizeCodeList = array_unique(array_column($results, 'itemize_code'));
        $itemizeList = BillingItemize::getByItemizeCodes($itemizeCodeList);

        $csvHeaders = [
            '利用年月', '請求年月', '基本料金', '電力量　１段料金', '電力量　２段料金', '電力量　３段料金'
        ];
        foreach(array_unique(array_column($itemizeList, 'itemize_name')) as $itemizeName) {
            if (!in_array($itemizeName, $csvHeaders)) {
                $csvHeaders[] = $itemizeName;
            }
        }
        $csvHeaders[] = '合計金額';
        // csv data作成
        $csv = [];
        $csv[] = implode(',', $csvHeaders);
        foreach($results as $monthData) {
            $itemizeCode = $monthData['itemize_code'];
            $csvRow = [];
            foreach($csvHeaders as $columnName) {
                $value = '';
                switch ($columnName) {
                    case '利用年月':
                        $value = $monthData['usage_date'];
                        break;
                    case '請求年月':
                        $value = $monthData['billing_date'];
                        break;
                    case '合計金額':
                        $value = $monthData['billing_amount'];
                        break;
                    default:
                        foreach($itemizeList as $k => $itemize) {
                            if ($itemize['itemize_name'] === $columnName && $itemize['itemize_code'] === $itemizeCode) {
                                $value = $itemize['itemize_bill'];
                                unset($itemizeList[$k]);
                                break;
                            }
                        }
                        break;
                }
                $csvRow[] = $value;
            }
            $csv[] = implode(',', $csvRow);
        }

        return $this->customAjaxResponse([
            'csv_data_encode' =>  base64_encode( implode("\r\n", $csv) ),
            'file_name' => '請求一覧_' . $year . '.csv'
        ]);
    }

    /**
     * 5 CSV/XLS 一括出力オリジナル
     * 使用場所、対象年月に従ったcsv/xlsを出力。
     * @param Illuminate\Http\Request
     * @return json
     */
    public function export_chart_original(Request $request)
    {
        $year = $request->get('year');
        $month = $request->get('month');
        if (!$year || !$month) {
            return $this->customAjaxError('データが見つかりませんでした');
        }
        $results = Billing::getYearMonthList($this->customerCode, $year.$month);
        // if (count($results) === 0) {
        //     return $this->customAjaxError('データが見つかりませんでした');
        // }
        $itemizeList = [];
        if (count($results) > 0) {
            $itemizeCodeList = array_unique(array_column($results, 'itemize_code'));
            $itemizeList = BillingItemize::getByItemizeCodes($itemizeCodeList);
        }

        $csvHeaders = [
            'マイページID',
            'ご契約者名',
            'ご利用者名',
            '使用場所住所',
            '供給地点特定番号',
            '請求月',
            'ご利用期間',
            '検針月日',
            '次回検針予定日',
            '当月お支払い予定日',
            '請求金額合計',
            '使用量',
            'プラン名'
        ];
        foreach(array_unique(array_column($itemizeList, 'itemize_name')) as $itemizeName) {
            if (!in_array($itemizeName, $csvHeaders)) {
                $csvHeaders[] = $itemizeName;
            }
        }

        // csv data作成
        $csv = [];
        $csv[] = implode(',', $csvHeaders);
        foreach($results as $monthData) {
            $type = Service::getContractServiceName($monthData['pps_type'], $monthData['supplypoint_code']);
            $billingCode = $monthData['billing_code'];
            $itemizeCode = $monthData['itemize_code'];
            $csvRow = [];
            // マイページID
            $csvRow[] = $monthData['customer_code'];
            // ご契約者名
            $csvRow[] = $monthData['name'];
            // ご利用者名
            $csvRow[] = $monthData['contract_name'];
            // 使用場所住所
            $csvRow[] = $monthData['address'];
            // 供給地点特定番号
            $csvRow[] = $monthData['supplypoint_code'];
            // 請求月
            $csvRow[] = $monthData['billing_date'];
            // ご利用期間
            $csvRow[] = $monthData['start_date'].'～'.$monthData['end_date'];
            // 検針月日
            $csvRow[] = $monthData['metering_date'];
            // 次回検針予定日
            $csvRow[] = $monthData['next_metering_date'];
            // 当月お支払い予定日
            $csvRow[] = $monthData['payment_date'];
            // 請求金額合計
            $csvRow[] = $monthData['billing_amount'];
            // 使用量
            $csvRow[] = $monthData['usage']. Service::getUsageUnit($monthData);
            // プラン名
            $csvRow[] = $monthData['plan'];
            // その他料金
            foreach($csvHeaders as $k => $columnName) {
                if ($k < 13) continue;
                foreach($itemizeList as $k => $itemize) {
                    if (
                        $itemize['billing_code'] === $billingCode &&
                        $itemize['itemize_name'] === $columnName &&
                        $itemize['itemize_code'] === $itemizeCode
                    ) {
                        $csvRow[] = $itemize['itemize_bill'];
                        unset($itemizeList[$k]);
                        break;
                    }
                }
            }
            $csv[] = implode(',', $csvRow);
        }

        return $this->customAjaxResponse([
            'csv_data_encode' =>  base64_encode( implode("\r\n", $csv) ),
            'file_name' => '利用料内訳_' . $year . $month . '.csv'
        ]);
    }


    /**
     * 詳細画面
     */
    public function detail(Request $request)
    {
        $supplypointCode = $request->get('supplypoint_code');
        $usageDate = $request->get('date');
        if (!$supplypointCode || !$usageDate) {
            abort(400);
        }

        $Service = Service::getInstance();
        $detail = $Service->getDetail($supplypointCode, $usageDate);
        if (!$detail) {
            abort(400);
        }
        $itemizeList = $Service->getDetailItemize($detail);

        return view('renewal.usage.detail')
            ->with('billing', $detail)
            ->with('billing_itemize', $itemizeList)
            ->with('service', $detail['type'])
            ->with('downloadable', !in_array($detail['type'], [Service::SERVICE_TYPE_MOB, Service::SERVICE_TYPE_OPT]));
    }

    /**
     * 領収書PDF出力
     */
    public function receipt(Request $request, $supplypointCode, $date)
    {
        try {
            $year = substr($date, 0, 4);
            $results = Billing::getSupplypointYearList($this->customerCode, $supplypointCode, $year);
            if (empty($results)) {
                abort(400);
            }
            $Service = Service::getInstance();
            $detail = $Service->getDetail( $supplypointCode, $results[0]['usage_date']);
            if (empty($detail)) {
                abort(400);
            }
            // 応急処置
            if ($mallieContract = $this->_getMallieContract($supplypointCode)) {
                $detail['apply_number'] = $mallieContract['apply_number'];
                $detail['power_zip_code'] = $mallieContract['power_zip_code'];
                $detail['contract_capacity'] = $mallieContract['contract_capacity'];
            }

            $issueDate = date('Y年m月d日');
            $fileName = 'receipt.pdf';
            return \PDF::loadView('renewal.pdf.receipt', [
                    'contract' => $detail,
                    'billings' => $results,
                    'date' => $issueDate
                ])
                ->setOption('encoding', 'utf-8')
                ->inline($fileName)
            ;

        } catch (\Throwable $th) {
            return back()->with('status', 'エラーが発生しました');
        }
    }

    /**
     * 明細PDFダウンロード
     * @param Illuminate\Http\Request
     * @param string $supplypointCode 供給地点特定番号
     * @param string $date 利用年月
     */
    public function specification(Request $request, $supplypointCode, $date)
    {
        try {
            $Service = Service::getInstance();
            $detail = $Service->getDetail($supplypointCode, $date);
            if (empty($detail)) {
                abort(400);
            }
            $itemizeList = $Service->getDetailItemize($detail);

            if ($mallieContract = $this->_getMallieContract($supplypointCode)) {
                $detail['apply_number'] = $mallieContract['apply_number'];
                $detail['power_zip_code'] = $mallieContract['power_zip_code'];
                $detail['contract_capacity'] = $mallieContract['contract_capacity'];
            }
            switch ($detail['type']) {
                case Service::SERVICE_TYPE_ELE:
                    $filePrefix = '電力';
                    $viewpath = 'renewal.pdf.specification';
                    break;
                case Service::SERVICE_TYPE_GAS:
                    $filePrefix = 'ガス';
                    $viewpath = 'renewal.pdf.specification_gas';
                    break;
                case Service::SERVICE_TYPE_MOB:
                case Service::SERVICE_TYPE_OPT:
                default:
                    $filePrefix = '';
                    $viewpath = 'renewal.pdf.specification';
                    break;
            }
            $user = User::find($this->userId);
            $fileName = $filePrefix.'使用量のお知らせ'.date('Ym').'.pdf';
            $headerHtml = view('renewal.pdf.header_footer', ['left' => '[G01-01]']);
            $footerHtml = view('renewal.pdf.header_footer', ['right' => '審査番号:20291231GD9999']);

            return \PDF::loadView($viewpath, [
                    'user' => $user,
                    'detail' => $detail,
                    'billing_itemize' => $itemizeList,
                ])
                ->setOption('encoding', 'utf-8')
                ->setOption('header-html', $headerHtml)
                ->setOption('footer-html', $footerHtml)
                ->download($fileName)
            ;
        } catch (\Throwable $th) {
            return back()->with('status', 'エラーが発生しました');
        }
    }


    // [応急処置] Mallieから足りない情報を取ってくる
    private function _getMallieContract($supplypointCode) {
        $default = [
                'power_zip_code' => '',
                'apply_number' => '',
                'contract_capacity' => '',
        ];
        try {
            $result = \Illuminate\Support\Facades\DB::connection('mysql_mallie')
                ->table('HalueneContract AS HC')
                ->where('HC.code', $this->customerCode)
                ->where('HC.power_customer_location_number', $supplypointCode)
                ->first()
            ;
            return is_null($result) ? $default : $result;
        } catch (\Throwable $th) {
            return  $default;
        }
    }

}
