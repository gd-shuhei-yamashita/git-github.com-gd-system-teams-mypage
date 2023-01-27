<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

// eloquent
use App\User; 
use App\UserNow; 

// PhpSpreadsheet 
// ex. https://phpspreadsheet.readthedocs.io/en/latest/
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * 申込情報取込(顧客データ)取込
 */
class AdminCaptureApplicationInformationController extends Controller
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
        return view('admin/capture_application_information');
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
        // $xls_temp = str_getcsv($req['btn_upload_section']);
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
      // $reader->setDelimiter(',');
      // $reader->setEnclosure('"');
      // $reader->setEscapeCharacter("|");
      $spreadsheet   = $reader->load($inputFileName);

      $sheet_shozoku = $spreadsheet->getSheet(0);

      // rowとcolのデータ領域を確認
      /** 合計件数として戻す */ 
      $rowMax = $sheet_shozoku->getHighestRow();
      $colMaxStr = $sheet_shozoku->getHighestColumn();
      /** 最大列数 */
      $colMax = Coordinate::columnIndexFromString($colMaxStr);

      // col が 6 列を超えたらエラーを返す
      if ( $colMax > 6) {
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
      $colWantMax = 6;
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

        // 1 code     マイページID MC00000999
        if (mb_strlen($sheetData[$r][1] , '8bit') > 10 || mb_strlen($sheetData[$r][1] , '8bit') < 8) {
          $tempv = $sheetData[$r];
          $tempv[2] = str_repeat("* ", mb_strlen($sheetData[$r][2] , '8bit'));
          $tempv[1] = "<div class='red'>" . $tempv[1] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "1列目がマイページID(8~10桁)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 2 password ログインパスワード 123456
        if (mb_strlen($sheetData[$r][2] , '8bit') < 4 ) {
          $tempv = $sheetData[$r];
          $tempv[2] = str_repeat("* ", mb_strlen($sheetData[$r][2] , '8bit'));  
          $tempv[2] = "<div class='red'>" . $tempv[2] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "2列目がパスワードに適切な文字列が含まれておりません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 3 email    メールアドレス testxx@example.com
        // ex. filter_var関数 検証フィルタ http://php.net/manual/ja/filter.filters.validate.php
        // PHPメールチェック正規表現 https://qiita.com/yamikoo@github/items/2cd76f2f993175a365a3
        // 空白も許容する-> [マイページID]@example.com 代入されるようにする  
        if ((! filter_var($sheetData[$r][3], FILTER_VALIDATE_EMAIL) ) && (mb_strlen($sheetData[$r][3] ) > 0 )) {
          $tempv = $sheetData[$r];
          $tempv[2] = str_repeat("* ", mb_strlen($sheetData[$r][2] , '8bit'));  
          $tempv[3] = "<div class='red'>" . $tempv[3] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "3列目がメールアドレスに適切な文字列が含まれておりません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 4 name     お客様名 TEST_名前
        if (mb_strlen($sheetData[$r][4] ) < 1 ) {
          $tempv = $sheetData[$r];
          $tempv[2] = str_repeat("* ", mb_strlen($sheetData[$r][2] , '8bit'));  
          $tempv[4] = "<i class='material-icons'>error</i><div class='red'>" . $tempv[4] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "4列目がお客様名に適切な文字列が含まれておりません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 5 zip_code 郵便番号 100-0001
        if (!preg_match('/^([0-9\-]{6,8})$/',$sheetData[$r][5])) {
          $tempv = $sheetData[$r];
          $tempv[2] = str_repeat("* ", mb_strlen($sheetData[$r][2] , '8bit'));  
          $tempv[5] = "<div class='red'>" . $tempv[5] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "5列目が郵便番号(数字とハイフンのみ)ではありません");
          $resNg ++;
          // 先頭RETURN_MAX行返す
          if ($resNg <= self::RETURN_MAX) {
            $errorList[] = $tempv;
          }
        }

        // 6 phone    ご連絡先電話番号 080-9999-0000
        if (!preg_match('/^([0-9\-]{10,13})$/',$sheetData[$r][6])) {
          $tempv = $sheetData[$r];
          $tempv[2] = str_repeat("* ", mb_strlen($sheetData[$r][2] , '8bit'));  
          $tempv[6] = "<div class='red'>" . $tempv[6] . "</div>";
          array_unshift($tempv , $r);
          array_push($tempv, "6列目がご連絡先電話番号(数字とハイフンのみ)ではありません");
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
        // リストにある billing_itemize をすべて追加
        foreach ($sheetData as $temp_data) {
          Log::channel('importlog')->debug($temp_data);

          // 更新先は、現在接続DBへ
          $User = UserNow::firstOrNew([
            "customer_code" => $temp_data[1],
            // "itemize_code" => $temp_data[2],
          ], [
            // "itemize_name" => $temp_data[4],
          ]);

          // 空白も許容する-> [マイページID]@example.com が代入されるようにする
          if (mb_strlen($temp_data[3]) == 0 ) {
            $temp_data[3] = $temp_data[1]."@example.com";
          }

          // 新規か既存かの判定 ex. Laravel firstOrNew how to check if it's first or new?
          // https://stackoverflow.com/questions/30686880/laravel-firstornew-how-to-check-if-its-first-or-new
          if ($User->exists) {
            // Update
            // $User->password = bcrypt($temp_data[2]);
            // $User->email    = $temp_data[3];
            $User->name     = $temp_data[4];
            $User->zip_code = $temp_data[5];
            $User->phone    = $temp_data[6];

            $User->updated_user_id = $user_code;
            $resUpd ++;
            // throw new Exception('ゼロによる除算。');
          } else {
            // Insert
            $User->customer_code = $temp_data[1];
            $User->password      = bcrypt($temp_data[2]);
            $User->email         = $temp_data[3];
            $User->name          = $temp_data[4];
            $User->zip_code      = $temp_data[5];
            $User->phone         = $temp_data[6];
            
            $User->created_user_id = $user_code;
            $User->updated_user_id = $user_code;
            $resIns ++;
          }
          $User->save();

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
