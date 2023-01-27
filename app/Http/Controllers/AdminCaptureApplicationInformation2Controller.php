<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

// eloquent
use App\Contract; 

// PhpSpreadsheet 
// ex. https://phpspreadsheet.readthedocs.io/en/latest/
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * 申込情報取込(契約データ)取込
 */
class AdminCaptureApplicationInformation2Controller extends Controller
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
        // ユーザー情報を取得
        // $users = $request->session()->get('users', array());
        return view('admin/capture_application_information2');
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

      Log::channel('importlog')->debug("[post] capture_items");

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

      /** Load $inputFileName to a Spreadsheet Object  */
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

      // col が 8 列を超えたらエラーを返す
      if ( $colMax > 8  ) {
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
      $colWantMax = 8;
      $sheetData = [ ];
      for($r = 1; $r <= $rowMax; $r ++) {
          for($c = 1; $c <= $colWantMax; $c ++) {
              $cell = $sheet_shozoku->getCellByColumnAndRow($c, $r);
              $sheetData[$r][$c] = $cell->getValue();
          }
      }
      
      /** 個別項目をすべて判定してエラーの一覧を残す */
      $errorList = [ ];
      for($r = 1; $r <= $rowMax; $r ++) {

        // 1 supplypoint_code マイページID MC00000042
        if (mb_strlen($sheetData[$r][1] , '8bit') > 10 || mb_strlen($sheetData[$r][1] , '8bit') < 8) {
          $tempv = $sheetData[$r];
          $tempv[1] = "<div class='red'>" . $tempv[1] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "1列目がマイページID(8~10桁)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 2 customer_code    供給地点特定番号 0300111001183222104031
        if (!(mb_strlen($sheetData[$r][2] , '8bit') >= 10 && mb_strlen($sheetData[$r][2] , '8bit') <= 22) || (!preg_match('/^(\d{10,22})$/',$sheetData[$r][2]))) {
          $tempv = $sheetData[$r];
          $tempv[2] = "<div class='red'>" . $tempv[2] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "2列目が供給地点特定番号(10桁~22桁)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 3 contract_code 契約コード HC99999999
        if (mb_strlen($sheetData[$r][3] , '8bit') != 10) {
          $tempv = $sheetData[$r];
          $tempv[3] = "<div class='red'>" . $tempv[3] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "3列目が契約コード(10桁)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 4 pps_type 小売事業者区分 9
        if (!preg_match('/^(\d+)$/',$sheetData[$r][4])) {
          $tempv = $sheetData[$r];
          $tempv[4] = "<div class='red'>" . $tempv[4] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "4列目が小売事業者区分(数字)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 5 contract_name    契約名義 TestUser
        if (mb_strlen($sheetData[$r][5] ) < 1 ) {
          $tempv = $sheetData[$r];
          $tempv[5] = "<i class='material-icons'>error</i><div class='red'>" . $tempv[5] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "5列目が契約名義に適切な文字列が含まれておりません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }        

        // 6 address   住所 東京都中野区XXXXXXXX
        if (mb_strlen($sheetData[$r][6] ) < 1 ) {
          $tempv = $sheetData[$r];
          $tempv[6] = "<i class='material-icons'>error</i><div class='red'>" . $tempv[6] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "6列目が住所に適切な文字列が含まれておりません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }
        
        // 7 plan      プラン名 ノーマルプラン
        if (mb_strlen($sheetData[$r][7] ) < 1 ) {
          $tempv = $sheetData[$r];
          $tempv[7] = "<i class='material-icons'>error</i><div class='red'>" . $tempv[7] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "7列目がプラン名に適切な文字列が含まれておりません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 6 shop_name 店舗名 中野区東支店
        // 空白を許容します
        // if (mb_strlen($sheetData[$r][6] ) < 1 ) {
        //   $tempv = $sheetData[$r];
        //   $tempv[6] = "<i class='material-icons'>error</i><div class='red'>" . $tempv[6] . "</div>";
        //   array_unshift($tempv , $r);
        //   array_push($tempv, "6列目が店舗名に適切な文字列が含まれておりません");
        //   $resNg ++;
        //   // 先頭RETURN_MAX行返す
        //   if ($resNg <= self::RETURN_MAX) {
        //     $errorList[] = $tempv;
        //   }
        // }
        
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

      // データ代入 内訳データ billing_itemize
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
        foreach ($sheetData as $temp_data) {
          if ($colMax == 7 || $temp_data[8] == null) {
            $temp_data[8] = " ";
          }
          Log::channel('importlog')->debug($temp_data);

          $Contract = Contract::firstOrNew([
            "contract_code" => $temp_data[3]
          ]);

          // 新規か既存かの判定 ex. Laravel firstOrNew how to check if it's first or new?
          // https://stackoverflow.com/questions/30686880/laravel-firstornew-how-to-check-if-its-first-or-new
          if ($Contract->exists) {
            // Update(saveだと複合キーの更新かからない)
            Log::channel('importlog')->debug("AdminCaptureApplicationInformation2Controller : Update");
            $Contract::where('contract_code', $temp_data[3])
            ->update(
              [
                'supplypoint_code' => $temp_data[2],
                'pps_type' => $temp_data[4],
                'contract_name' => $temp_data[5],
                'address' => $temp_data[6],
                'plan' => $temp_data[7],
                'shop_name' => $temp_data[8],
                'updated_user_id' => $user_code
              ]
            );
            
            $resUpd ++;
          } else {
            // Insert
            Log::channel('importlog')->debug("AdminCaptureApplicationInformation2Controller : Insert");
            $Contract->customer_code   = $temp_data[1];
            $Contract->supplypoint_code = $temp_data[2];
            $Contract->contract_code = $temp_data[3];
            $Contract->pps_type = $temp_data[4];
            $Contract->contract_name = $temp_data[5];
            $Contract->address   = $temp_data[6];
            $Contract->plan      = $temp_data[7];
            $Contract->shop_name = $temp_data[8];
            
            $Contract->created_user_id = $user_code;
            $Contract->updated_user_id = $user_code;
            
            $Contract->save();

            $resIns ++;
          }

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
