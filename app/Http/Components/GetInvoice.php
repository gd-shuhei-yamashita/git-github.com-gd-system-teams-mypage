<?php
namespace App\Http\Components;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Assignment;
use App\Billing;
use App\BillingItemize;
use App\UsageT;
use App\Contract;

/**
 * ファサード : 供給地点特定番号 supplypoint_code 一覧
 * 
 * ex. - LaravelのFacade（ファサード）でオリジナルの処理クラスを定義する入門編  
 * https://www.ritolab.com/entry/88  
 */
class GetInvoice
{
   /**
   * ユーザが見る権限のある供給地点特定番号(supplypoint_code)一覧を取得する
   * 
   * @param $customer_code 対象ユーザ
   * @param $sort ソート対象カラム
   * @param $order 並び順
   */
  public function get_supplypoint_list($customer_code, $sort = null, $order = null)
  {
    // 譲渡データをもとに
    // ToDo:譲渡データの一覧を元に除外、追加を実施する
    // 一覧取得 （アルゴリズムは同様）
    $results = Assignment::where('assignment_after_customer_code', $customer_code)->
    orderBy('assignment_date', 'asc')->get();
    Log::debug( $results );   

    $supplypoint = [];
    foreach ($results as $temp_assignment) {
        $supplypoint[] = $temp_assignment["supplypoint_code"];
        Log::debug('譲渡:' . $temp_assignment["supplypoint_code"] );
    }

    // 契約データ contract から 使用場所読み込み
    $contracts = [];
    
    $req1 = session('user_now.customer_code');
    // バッチからの起動用
    if (empty($req1)) {
        $req1 = $customer_code;
    }
    // $req2 = ['9999999999999999999003'];

    // 自分の契約
    $results_own = Contract::where("customer_code", $req1)
    ->orderBy('supplypoint_code', 'asc')->get();
    Log::debug('自分の契約:' . $results_own );

    $supplypoints_onw = [];
    foreach ($results_own as $temp_contract_own) {
        $supplypoints_onw[] = $temp_contract_own["supplypoint_code"];
        Log::debug('契約:' . $temp_contract_own["supplypoint_code"] );
    }

    // 契約一覧
    $query_all = Contract::where("customer_code", '<>' , $req1)
    ->where(function ($query) use ( $supplypoints_onw ) {
        $query->whereNotIn("supplypoint_code", $supplypoints_onw);
    })
    ->where(function ($query) use ( $supplypoint ) {
        $query->whereIn("supplypoint_code", $supplypoint);
    })
    ->orWhere("customer_code", $req1);
    if (!empty($sort) && !empty($order)) {
        $query_all->orderBy($sort, $order);
    } else {
        $query_all->orderBy('supplypoint_code', 'asc');
    }

    $results_all = $query_all->get();
    Log::debug('契約一覧:' . $results_all );
    Log::debug($query_all->toSql());

    // 必ずセッションのキーを元に出す 
    // 無名関数利用
    // ex. Laravel5で「.. or ...) and (..」みたいな複雑な条件を書く  
    // https://qiita.com/Hwoa/items/542456b63e51895f9a55
    // $results = Contract::where("customer_code", $req1)
    // ->orWhere(function ($query) use ( $supplypoint ) {
    //     $query->WhereIn("supplypoint_code", $supplypoint);
    // })
    // ->orderBy('supplypoint_code', 'asc')->get();
    // Log::debug("マージ結果：" . $results );

    foreach ($results_all as $temp_contract) {
        $contracts[] = [
        "customer_code"       => $temp_contract->customer_code,
        "supplypoint_code"    => $temp_contract->supplypoint_code,
        "contract_code"       => $temp_contract->contract_code,
        "pps_type"            => $temp_contract->pps_type,
        "contract_name"       => $temp_contract->contract_name,
        "address"             => $temp_contract->address,
        "plan"                => $temp_contract->plan,
        "shop_name"           => $temp_contract->shop_name,
        "switching_scheduled_date" => $temp_contract->switching_scheduled_date,
        "brand_id" => $temp_contract->brand_id,
        ];
    }

    return $contracts;
  }    

    /**
     * 各種一覧 で 表示してよい期間をraw用に生成する
     * 関係：旧  electro.php / function getJoutoBilling
     */
    public function get_assignment_whereraw($supplypoint_code, $customer_code)
    {
      $sql = "";

      // before
      $results1 = Assignment::where('supplypoint_code', $supplypoint_code)->
      where('assignment_before_customer_code', $customer_code)->
      orderBy('assignment_date', 'asc')->first(); // 直近側を１件

      $assignment_before = [];
      if ($results1 !== null) {
          $assignment_before = [
          "before_customer_code"   => $results1->assignment_before_customer_code,
          "after_customer_code"    => $results1->assignment_after_customer_code,
          "after_contract_name"    => $results1->assignment_after_contract_name,
          "after_address"          => $results1->assignment_after_address,
          "after_plan"             => $results1->assignment_after_plan,
          "date"                   => $results1->assignment_date,
          "before_customer_billing_end"  => $results1->before_customer_billing_end,
          "after_customer_billing_start" => $results1->after_customer_billing_start,
          ];
      }
      // Log::debug($assignment_before);
      
      // after（アルゴリズムは同様）
      $results2 = Assignment::where('supplypoint_code', $supplypoint_code)->
      where('assignment_after_customer_code', $customer_code)->
      orderBy('assignment_date', 'asc')->first(); // 直近を１件

      $assignment_after = [];
      if ($results2 !== null) {
          $assignment_after = [
          "before_customer_code"   => $results2->assignment_before_customer_code,
          "after_customer_code"    => $results2->assignment_after_customer_code,
          "after_contract_name"    => $results2->assignment_after_contract_name,
          "after_address"          => $results2->assignment_after_address,
          "after_plan"             => $results2->assignment_after_plan,
          "date"                   => $results2->assignment_date,
          "before_customer_billing_end"  => $results2->before_customer_billing_end,
          "after_customer_billing_start" => $results2->after_customer_billing_start,
          ];
      }
      // Log::debug($assignment_after);

      // 譲渡タイプ 1:譲渡した 2:譲渡された これについては両方の条件を満たす場合がある。
      // return $assignment;
      
      // 1回入る 1回離脱まで  
      // 4パターン別に形成
      if ($assignment_before == null && $assignment_after != null) {
          // afterのみ（譲渡された） 一定期間以後に手放したケース
          Log::debug("Case A");
          $sql = "usage_date >= " . $assignment_after["after_customer_billing_start"];
      } elseif ($assignment_before != null && $assignment_after == null) {
          // beforeのみ（譲渡した） 一定期間以後に所持しているケース
          Log::debug("Case B");
          $sql = "usage_date <= " . $assignment_before["before_customer_billing_end"];
      } elseif($assignment_before == null && $assignment_after == null) {
          // 該当なしの場合はすべて可
          Log::debug("Case E");
          $sql = "1 = 1";
      } elseif ($assignment_before['before_customer_billing_end'] > $assignment_after["after_customer_billing_start"]) {
          // before > after（譲渡されてから譲渡した） 一定期間のみ所持していたケース
          Log::debug("Case C");
          $sql = "( usage_date <= " . $assignment_before["before_customer_billing_end"];
          $sql.= " and usage_date >= " . $assignment_after["after_customer_billing_start"] . " )";
      } elseif ($assignment_after["after_customer_billing_start"] > $assignment_before['before_customer_billing_end']) {
          // after > before（譲渡してから譲渡された）  一定期間手放していたケース
          Log::debug("Case D");
          $sql = "( usage_date <= " . $assignment_before["before_customer_billing_end"];
          $sql.= " or usage_date >= " . $assignment_after["after_customer_billing_start"] . " )";
      } else {
          // 該当なしの場合はすべて可
          Log::debug("Case E");
          $sql = "1 = 1";
      }
      Log::debug($sql);
      return $sql;
  }

    /**
     * 電力請求詳細の取得
     */
    public function get_billing_detail($req)
    {
        // supplypoint_code 
        // billing_date     
        Log::debug("customer_code:" . $req['customer_code']); // 供給地点番号
        Log::debug("supplypoint_code:" . $req['supplypoint_code']); // 供給地点番号
        Log::debug("date:" . $req['date']);                         // 対象年 + 対象月

        // billing (請求データ)をまずは取得
        $billing = [];
        $result = Billing::where("supplypoint_code", $req['supplypoint_code'])->
        where("customer_code", $req["customer_code"])->
        where("usage_date", $req['date'])->first();   // 利用年月（※請求年月は重複し得る）
        if ($result == null) {
            return ["status"=> false];
        }

        $billing["supplypoint_code"] = $result["supplypoint_code"];
        $billing["customer_code"]    = $result["customer_code"];
        $billing["billing_code"]     = $result["billing_code"];
        $billing["itemize_code"]     = $result["itemize_code"];

        $billing["start_date"]   = $result["start_date"];
        $billing["end_date"]     = $result["end_date"];
        $billing["billing_date"] = $result["billing_date"];
        $billing["billing_amount"]   = $result["billing_amount"];
        $billing["tax"]              = $result["tax"];
        $billing["payment_type"]     = $result["payment_type"];
        $billing["power_percentage"] = $result["power_percentage"];
        $billing["metering_date"]      = $result["metering_date"];
        $billing["next_metering_date"] = $result["next_metering_date"];
        $billing["main_indicator"]            = $result["main_indicator"];
        $billing["main_indicator_last_month"] = $result["main_indicator_last_month"];
        $billing["meter_multiply"]     = $result["meter_multiply"];
        $billing["difference"]         = $result["difference"];
        $billing["payment_date"]       = $result["payment_date"];
        $billing["usage_date"]         = $result["usage_date"];

        // contract (契約データ)を取得
        $resultc = Contract::where("supplypoint_code", $req['supplypoint_code'])
        ->where("customer_code", $req["customer_code"])
        ->first();

        $billing["contract_name"]   = $resultc["contract_name"];
        $billing["address"]         = $resultc["address"];
        $billing["plan"]            = $resultc["plan"];
        $billing["shop_name"]       = $resultc["shop_name"];
        
        // billing_itemize (内訳データ)を取得  / 一段料金  1111
        $resultbi = BillingItemize::where('itemize_code', $result["itemize_code"])
        ->orderBy('itemize_code', 'asc')        // 請求番号
        ->orderBy('itemize_order','asc')        // 明細表示順
        ->get();
        $billing_itemize = [];
        foreach($resultbi as $temp_itemize) {
            // $billing_itemize.= $temp_itemize["itemize_name"] . " : " . $temp_itemize["itemize_bill"] . "\n";
            $billing_itemize[] = $temp_itemize;
        }
        // $billing["billing_itemize"] = $billing_itemize;
        
        // UsageT
        $resultus = UsageT::where('supplypoint_code', $req['supplypoint_code'])
        ->where("customer_code", $req["customer_code"])
        ->where("usage_date", $req['date'])->first();

        if (!empty($resultus)) {
            $billing["usage"] = $resultus["usage"];
        }

        Log::debug($billing);

        return ["status"=> true, "billing" => $billing , "billing_itemize" => $billing_itemize ];

    }
}