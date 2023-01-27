<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;


// eloquent
use App\UsageT; 

// PhpSpreadsheet 
// ex. https://phpspreadsheet.readthedocs.io/en/latest/
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * 使用量データ取込
 */
class AdminCaptureUsagedataController extends Controller
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
        return view('admin/capture_usagedata');
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

      Log::channel('importlog')->debug("[post] capture_usagedata");

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

      // col が4列でなければエラーを返す
      if ( $colMax != 4 ) {
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
        // 1 : supplypoint_code 供給地点特定番号 0300111001183222404031 22桁
        if (!(mb_strlen($sheetData[$r][1] , '8bit') >= 10 && mb_strlen($sheetData[$r][1] , '8bit') <= 22) || (!preg_match('/^(\d{10,22})$/',$sheetData[$r][1]))) {
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

        // 2 : usage_date 利用年月 2018/12 YYYY/MM
        if (!preg_match('/^(\d{4}\/\d{2})$/',$sheetData[$r][2])) {
          $tempv = $sheetData[$r];
          $tempv[2] = "<div class='red'>" . $tempv[2] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "2列目が利用年月(YYYY/MM)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 3 : usage 使用量 76  数値
        if (!preg_match('/^(\d+)$/',$sheetData[$r][3])) {
          $tempv = $sheetData[$r];
          $tempv[3] = "<div class='red'>" . $tempv[3] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "3列目が使用量(数字)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 4 : customer_code マイページID MC00000042 桁数
        if (mb_strlen($sheetData[$r][4] , '8bit') > 10 || mb_strlen($sheetData[$r][4] , '8bit') < 8) {
          $tempv = $sheetData[$r];
          $tempv[4] = "<div class='red'>" . $tempv[4] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "4列目がマイページID(8~10桁)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }
        
        // 4 : customer_code マイページID MC00000042  アルファベット/数値
        if (!preg_match('/^([A-Z]{2})(\d+)$/',$sheetData[$r][4])) {
          // // テストデータ用
          // if (!preg_match('/^ADMN(\d+)$/',$sheetData[$r][4]) 
          //   && !preg_match('/^BOD(\d+)$/',$sheetData[$r][4])) {
          $tempv = $sheetData[$r];
          $tempv[4] = "<div class='red'>" . $tempv[4] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "4列目がマイページID(ローマ字2文字+数字8桁)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
          // }
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

      // データ代入 使用率 usage_t
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
        // リストにあるusage_tをすべて追加
        foreach ($sheetData as $temp_data) {

          $UsageT = UsageT::firstOrNew([
            "supplypoint_code" => $temp_data[1],
            "usage_date" => (int)str_replace("/","",$temp_data[2]),
            "customer_code" => $temp_data[4]
          ], [
            "created_user_id" => $user_code,
            "updated_user_id" => $user_code,
          ]);

          // 新規か既存かの判定 ex. Laravel firstOrNew how to check if it's first or new?
          // https://stackoverflow.com/questions/30686880/laravel-firstornew-how-to-check-if-its-first-or-new
          if ($UsageT->exists) {
            // Update
            $UsageT->usage = $temp_data[3];
            $UsageT->updated_user_id = $user_code;
            $resUpd ++;
            // throw new Exception('ゼロによる除算。');
          } else {
            // Insert
            $UsageT->usage = $temp_data[3];
            $UsageT->created_user_id = $user_code;
            $UsageT->updated_user_id = $user_code;
            $resIns ++;
          }
          $UsageT->save();

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
