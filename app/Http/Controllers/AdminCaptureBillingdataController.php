<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

// eloquent
use App\Billing; 

// PhpSpreadsheet 
// ex. https://phpspreadsheet.readthedocs.io/en/latest/
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * 請求データ取込
 */
class AdminCaptureBillingdataController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /** 削除 */
    public function remove_cachefile($inputFileName)
    {
        // 一時ファイルの削除
        if (file_exists($inputFileName)){
        // ok
        unlink($inputFileName);
        return 0;
        } else {
        // error
        Log::channel('importlog')->debug( "内部エラー: file can't delete : ". $inputFileName );
        return 1;
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // // ユーザー情報を取得
        // $users = $request->session()->get('users', array());
        return view('admin/capture_billingdata');
    }

    /** 表示上限 */
    const RETURN_MAX = 100;
    

    /** 
     * csv登録
     */
    public function Registration(Request $request)
    {
      ini_set("max_execution_time",300); // タイムアウトを300秒にセット ※これをこえると「Maximum execution time」エラーが発生する
      ini_set("max_input_time",180); // パース時間を180秒にセット 
      ini_set('memory_limit', '1500M');
            
      /** Insert件数 */
      $resIns = 0;
      /** Update件数 */
      $resUpd = 0;
      /** NG件数 */
      $resNg  = 0;
      /** エラー行を返すための表 */
      $sheetDataRetutn = [];

      Log::channel('importlog')->debug("[post] capture_billingdata");

      $req = $request->all();
      Log::channel('importlog')->debug("filename : " . $req['file_name']);

      if (strlen($req['btn_upload_section']) > 0) {
        $xls_temp = explode( ",", $req['btn_upload_section']);
        $xls_bin  = base64_decode($xls_temp[1]);          
        $xls_bin_utf8 = mb_convert_encoding($xls_bin, 'UTF-8', 'Shift_JIS');
        // ファイルの管理
        // jsonエンコード後に一時領域に出力する
        $inputFileName = storage_path() . "/temp/" . getmypid().".csv";
        file_put_contents($inputFileName , $xls_bin_utf8);
      } else {
        $value["status"] = "500";
        $value["message"] = "file upload NG";
        return $value;
      }
  
      // ファイルロード ファイルの解析読み込み
      // 正しければOK,正しくなければエラー

      // ex. https://phpspreadsheet.readthedocs.io/en/latest/topics/reading-files/
      //     https://qiita.com/yuiqiiii/items/faa4d2e6fe2681dffd1a

      /** Load $inputFileName to a Spreadsheet Object  q*/
      $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
      // $reader->setInputEncoding('SJIS'); // 読み込む前に宣言する必要
      $spreadsheet   = $reader->load($inputFileName);

      $sheet_shozoku = $spreadsheet->getSheet(0);

      // rowとcolのデータ領域を確認
      /** 合計件数として戻す */ 
      $rowMax = $sheet_shozoku->getHighestRow();
      $colMaxStr = $sheet_shozoku->getHighestColumn();
      /** 最大列数 */
      $colMax = Coordinate::columnIndexFromString($colMaxStr);

      // col が19列でなければエラーを返す
      if ( $colMax != 19 ) {
          $this->remove_cachefile($inputFileName);
          // error
          $value = [];
          $value["status"] = "409";
          $value["sheet_data"] = "";
          $value["results"] = [0,0,0,0];
          $value["message"] = "csvテンプレートが異なります";

          // 作業ログに記録： 戻り値の説明追加
          $req["status"] = $value["status"];
          $req["message"] = $value["message"] ;
          $req["file_name"] = $request -> file_name;
          $operation_history_log = new \App\Http\Middleware\OperationHistoryMiddleware;
          $operation_history_log->OperationHistoryManual($request, $req);
          
          return $value;
      }

      /** 配列へ、1セルごとにcsvデータを取得 */
      $sheetData = [ ];
      for($r = 1; $r <= $rowMax; $r ++) {
          for($c = 1; $c <= $colMax; $c ++) {
              $cell = $sheet_shozoku->getCellByColumnAndRow($c, $r);
              $sheetData[$r][$c] = $cell->getValue();
          }
      }
      
      /** 個別項目をすべて判定してエラーの一覧を残す */
      $errorList = [ ];
      for($r = 1; $r <= $rowMax; $r ++) {
        // 1 supplypoint_code 請求番号 0300111001183222404031
        if (!(mb_strlen($sheetData[$r][1] , '8bit') >= 10 && mb_strlen($sheetData[$r][1] , '8bit') <= 22) ) {
          $tempv = $sheetData[$r];
          $tempv[1] = "<div class='red'>" . $tempv[1] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "1列目が供給地点特定番号(10桁~22桁)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 2 customer_code マイページID MC00000042
        if (mb_strlen($sheetData[$r][2] , '8bit') > 10 || mb_strlen($sheetData[$r][2] , '8bit') < 8) {
          $tempv = $sheetData[$r];
          $tempv[2] = "<div class='red'>" . $tempv[2] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "2列目がマイページID(8~10桁)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 3 billing_code 請求番号 DENKIT0000027201812  19文字
        if (mb_strlen($sheetData[$r][3] , '8bit') != 19 ) {
          $tempv = $sheetData[$r];
          $tempv[3] = "<div class='red'>" . $tempv[3] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "3列目が請求番号(19桁)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 4 itemize_code 内訳コード DENKIT0000027201812030011100118322240403120181220181126  55文字
        if (mb_strlen($sheetData[$r][4] , '8bit') != 55 && mb_strlen($sheetData[$r][4] , '8bit') != 50 && mb_strlen($sheetData[$r][4] , '8bit') != 43) {
          $tempv = $sheetData[$r];
          $tempv[4] = "<div class='red'>" . $tempv[4] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "4列目が内訳コード(43桁,50桁または55桁)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 5 start_date 利用開始年月日 2018/11/26
        if (!preg_match('/^(\d{4}\/\d{2}\/\d{2})$/',$sheetData[$r][5])) {
          $tempv = $sheetData[$r];
          $tempv[5] = "<div class='red'>" . $tempv[5] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "5列目が利用開始年月日(YYYY/MM/DD)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }
        
        // 6 end_date 利用終了年月日 2018/12/21
        if (!preg_match('/^(\d{4}\/\d{2}\/\d{2})$/',$sheetData[$r][6])) {
          $tempv = $sheetData[$r];
          $tempv[6] = "<div class='red'>" . $tempv[6] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "6列目が利用終了年月日(YYYY/MM/DD)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 7 billing_date 請求年月 2019/01
        if (!preg_match('/^(\d{4}\/?\d{2})$/',$sheetData[$r][7])) {
          $tempv = $sheetData[$r];
          $tempv[7] = "<div class='red'>" . $tempv[7] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "7列目が請求年月(YYYY/MM or YYYYMM)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 8 billing_amount 請求額 2388
        if (!preg_match('/^(\d+)$/',$sheetData[$r][8])) {
          $tempv = $sheetData[$r];
          $tempv[8] = "<div class='red'>" . $tempv[8] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "8列目が請求額(数字)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 9 tax 消費税相当額 176
        if (!preg_match('/^(\d+)$/',$sheetData[$r][9])) {
          $tempv = $sheetData[$r];
          $tempv[9] = "<div class='red'>" . $tempv[9] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "9列目が消費税相当額(数字)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 10 payment_type 支払い種別 2
        if (!preg_match('/^(\d+)$/',$sheetData[$r][10])) {
          $tempv = $sheetData[$r];
          $tempv[10] = "<div class='red'>" . $tempv[10] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "10列目が支払い種別(数字)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 11 power_percentage 力率 text

        // 12 metering_date 検針月日 2018/12/22
        if (!preg_match('/^(\d{4}\/\d{2}\/\d{2})$/',$sheetData[$r][12])) {
          $tempv = $sheetData[$r];
          $tempv[12] = "<div class='red'>" . $tempv[12] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "12列目が検針月日(YYYY/MM/DD)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 13 next_metering_date 次回検針予定日 2019/01/25 (ヌルは許可)
        if ((!preg_match('/^(\d{4}\/\d{2}\/\d{2})$/',$sheetData[$r][13])) && (strlen(trim($sheetData[$r][13])) != 0)) {
          $tempv = $sheetData[$r];
          $tempv[13] = "<div class='red'>" . $tempv[13] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "13列目が次回検針予定日(YYYY/MM/DD)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 14 main_indicator 当月指示数 2076.4
        if (!preg_match('/^([0-9.]*)$/',$sheetData[$r][14])) {
          $tempv = $sheetData[$r];
          $tempv[14] = "<div class='red'>" . $tempv[14] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "14列目が当月指示数(数値)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 15 main_indicator_last_month 前月指示数 2000.1
        if (!preg_match('/^([0-9.]*)$/',$sheetData[$r][15])) {
          $tempv = $sheetData[$r];
          $tempv[15] = "<div class='red'>" . $tempv[15] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "15列目が前月指示数(数値)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 16 meter_multiply 計器乗率 
        // 判定なし

        // 17 difference 差引 76.3
        if (!preg_match('/^([0-9.]*)$/',$sheetData[$r][17])) {
          $tempv = $sheetData[$r];
          $tempv[17] = "<div class='red'>" . $tempv[17] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "17列目が差引(数値)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 18 payment_date 当月お支払い予定日 ご契約のクレジットカード会社に準拠
        // 判定なし

        /// 19 usage_date 利用年月 
        if (!preg_match('/^(\d{4}\/\d{2})$/',$sheetData[$r][19])) {
          $tempv = $sheetData[$r];
          $tempv[19] = "<div class='red'>" . $tempv[19] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "19列目が利用年月(YYYY/MM)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

      }
      // ここでエラーが１個でもあれば不正データとして戻す
      if (count($errorList) > 0) {
        // 戻り値
        $value = [];
        $value["status"] = "400";
        $value["sheet_data"] = $errorList;
        $value["results"] = [$rowMax, $resIns, $resUpd, $resNg ];
        $value["message"] = "不正なデータです。該当行を確認してください。";

        // 作業ログに記録： 戻り値の説明追加
        $req["status"] = $value["status"];
        $req["message"] = $value["message"] ;
        $req["file_name"] = $request -> file_name;
        $operation_history_log = new \App\Http\Middleware\OperationHistoryMiddleware;
        $operation_history_log->OperationHistoryManual($request, $req);

        return $value;
      }

      // データ代入 請求データ billing
      /** 戻す代表値 */
      $ReturnData = [ ];
      /** 作業者記載 ユーザコード */
      $user_code = Session::get('user_login.customer_code',"");

      // begin transaction
      if (Session::get('db_accesspoint_now' ,0) == 2) {
        DB::connection('mysql2')->beginTransaction();
      } else {
        DB::beginTransaction();
      }

      Log::channel('importlog')->debug("Transaction Start");
      try
      {
        // リストにあるbillingをすべて追加
        foreach ($sheetData as $temp_data) {
          Log::channel('importlog')->debug($temp_data);

          $Billing = Billing::firstOrNew([
            "supplypoint_code" => $temp_data[1],
            "customer_code" => $temp_data[2],
            "billing_code" => $temp_data[3],
            "itemize_code" => $temp_data[4]            
          ], [
            "start_date"=> $temp_data[5],
            "end_date"  => $temp_data[6],
            "billing_date"    => (int)str_replace("/","",$temp_data[7]),
            "billing_amount"  => $temp_data[8],
            "tax"             => $temp_data[9],
            "payment_type"    => $temp_data[10],
            "power_percentage"=> $temp_data[11],
            "metering_date"   => $temp_data[12],
            "next_metering_date"   => $temp_data[13],
            "main_indicator"       => 0+$temp_data[14],
            "main_indicator_last_month"   => 0+$temp_data[15],
            "meter_multiply"   => $temp_data[16],
            "difference"     => 0+$temp_data[17],
            "payment_date"   => $temp_data[18],
            "usage_date" => (int)str_replace("/","",$temp_data[19]),
            // "created_user_id" => $user_code,
            // "updated_user_id" => $user_code,
          ]);

          // 新規か既存かの判定 ex. Laravel firstOrNew how to check if it's first or new?
          // https://stackoverflow.com/questions/30686880/laravel-firstornew-how-to-check-if-its-first-or-new
          if ($Billing->exists) {
            // Update
            // $Billing->usage = $temp_data[3];
            $Billing->updated_user_id = $user_code;
            $resUpd ++;
            // throw new Exception('ゼロによる除算。');
          } else {
            // Insert
            // $Billing->usage = $temp_data[3];
            $Billing->created_user_id = $user_code;
            $Billing->updated_user_id = $user_code;
            $resIns ++;
          }
          $Billing->save();

          // 先頭RETURN_MAX行返す
          if (($resUpd + $resIns) <= self::RETURN_MAX) {
            $tempv = $temp_data;
            array_unshift($tempv , ($resUpd + $resIns));
            array_push($tempv, "");
            $ReturnData[] = $tempv;
          }
        }
  
        // Commit
        if (Session::get('db_accesspoint_now' ,0) == 2) {
          DB::connection('mysql2')->commit();
        } else {
          DB::commit();
        }

        Log::channel('importlog')->debug("Transaction OK");
      }
      // catch (\PDOException $e)
      catch (\Exception $e)
      {
        // Rollback
        Log::channel('importlog')->debug("Transaction Failed. Rollback Start.");
        DB::rollBack();
        Log::channel('importlog')->debug($e);
        // session()->put("error", $e);
        // 判断
        // 一時ファイルの削除
        $this->remove_cachefile($inputFileName);

        // 戻り値
        $value = [];
        $value["status"] = "409";
        $value["sheet_data"] = $temp_data;
        $value["results"] = [$rowMax, $resIns, $resUpd, $resNg ];
        $value["message"] = "トランザクション失敗 ";

        // 作業ログに記録： 戻り値の説明追加
        $req["status"] = $value["status"];
        $req["message"] = $value["message"] . ($resIns + $resUpd) .":" . substr( $e , 400) ; // 先頭400文字だけ
        $req["file_name"] = $request -> file_name;
        $operation_history_log = new \App\Http\Middleware\OperationHistoryMiddleware;
        $operation_history_log->OperationHistoryManual($request, $req);

        Log::channel('importlog')->debug("Transaction Rollback OK.");
        return $value;

      }

      // 一時ファイルの削除
      $this->remove_cachefile($inputFileName);

      // 戻り値
      $value = [];
      $value["status"] = "200";
      $value["sheet_data"] = $ReturnData;
      $value["results"] = [$rowMax, $resIns, $resUpd, $resNg ];
      $value["message"] = "OK";
      return $value;
    }    
}
