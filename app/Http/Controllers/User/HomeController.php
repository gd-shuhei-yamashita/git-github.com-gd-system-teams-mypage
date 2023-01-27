<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// ファサード : 請求書関連共通クラス 供給地点特定番号 supplypoint_code
use App\Facades\GetInvoice;

// eloquent
use App\Models\DB\Notice;
use App\Models\DB\Billing;

use App\Models\Services\Electric;
use App\Models\Services\Gas;
use App\Models\Services\Mobile;
use App\Models\Services\Option;
use App\Models\Services\Service;

/**
 * ホーム画面
 */
class HomeController extends LoginedController
{

    /**
     * Show the application dashboard.
     * @param Illuminate\Http\Request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        return view('renewal/home/index');
    }


    /**
     * 各種お知らせを取得
     * @param Illuminate\Http\Request
     * @return json
     */
    public function ajaxNotices(Request $request)
    {
        $limit = 3;
        $notices = Notice::getList($this->customerCode, $limit);

        return $this->customAjaxResponse([
            'pc' => view('renewal/home/notices_pc', compact('notices'))->render(),
            'sp' => view('renewal/home/notices_sp', compact('notices'))->render()
        ]);

    }

    /**
     * ご請求を取得
     * @param Illuminate\Http\Request
     * @return json
     */
    public function ajaxBillingInformations(Request $request)
    {
        // 対象月がない場合（初回表示時）請求期間を取得して最終月のデータを返す
        $billingDate = $request->get('billing_date');
        if (is_null($billingDate)) {
            $billingRange = Billing::getBillingRangeAll($this->customerCode);
            $billingDate = $billingRange['latest_billing_date'] ?: date('Ym');
        }

        $Service = Service::getInstance();
        $contracts = $Service->getBillings($billingDate);

        $contents = view('renewal/home/billing_informations', [
            'billingDate' => $billingDate,
            'contracts' => $contracts,
            'totalAmount' => array_sum(array_column($contracts, 'billing_amount'))
        ])->render();

        return $this->customAjaxResponse([
            'date'     => $billingDate,
            'max_date' => isset($billingRange) ? $billingRange['latest_billing_date'] : '',
            'min_date' => isset($billingRange) ? $billingRange['first_billing_date'] : '',
            'contents' => $contents
        ]);
    }

}
