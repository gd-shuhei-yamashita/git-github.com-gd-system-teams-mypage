<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\Mail;

// eloquent
use App\Notice;
use App\NoticeRelation;
use App\User;

use App\Mail\NoticeMail;

/**
 * お知らせ登録画面
 */
class AdminRegistNoticeController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // 日付
        $forms["date"] =date("Y/m/d");

        // ユーザー情報を取得
        return view('admin/regist_notice', ["forms" => $forms]);
    }

    /**
     * 一覧取得
     */
    public function getlist(Request $request)
    {
        Log::debug("AdminRegistNoticeController : getlist");
        $req = $request->all();

        // 検索条件デバッグ出力
        Log::debug( "検索条件：" );
        Log::debug($req);

        /** 通知 notice 取得 */
        $notice = [];

        // $results = Notice::where("notice_date","<","now()");
        $results = new Notice;

        // 1件取得モード
        if (array_key_exists( "now_cid", $req)) {
            $results = $results->where( "id", $req['now_cid'] );
        }

        // usersの条件での合計数を取得する
        $results_count = $results->count();

        // ページングに関する処理 skip take
        $now_state = 0;
        if ($req["now_state"]) {
            Log::debug("now_state");
            $now_state = $req['now_state'];
            $results = $results->skip($req['now_state']*$req['display_number']);
        }
        if ($req["display_number"]) {
            $results = $results->take($req['display_number']);
        }
        
        // 一覧取得
        $notice = $results->orderBy('notice_date', 'desc')->get();
        foreach ($notice as $key => $value) {
            // 公開範囲チェック
            $notice_result_query = NoticeRelation::where('notice_id', $value->id);
            if ($notice_result_query->count() < 1) {
                $notice[$key]->notice_relation = 0;
            } else {
                $notice[$key]->notice_relation = 1;
            }
        }
        Log::debug( $notice );

        // 戻り値に割り当てられた 請求データ を渡す。
        $value = [];
        $value["status"]  = "200";
        $value["notice"]   = $notice;
        $value["notice_counts"]   = $results_count;
        $value["now_state"]      = $now_state;
        $value["message"] = "OK";
        return $value;    

    }

    /**
     * 記事追加/変更
     */
    public function store(Request $request)
    {
        Log::debug("AdminRegistNoticeController : store");
        $req = $request->all();
        Log::debug($req);
        
        // デフォルト方式でのバリデーション
        $validatedData = $request->validate([
            'notice_date' => 'required|date',
            'url' => 'nullable|url',
            'send_mail' => 'required',
            'notice_relation' => 'required',
            'notice_comment' => 'required',
        ],
        [
            'send_mail.required' => 'メール送信有無は必須です。',
            'notice_relation.required' => '公開対象は必須です。',
        ]);

        $result = ["status" => 0];
        $res = null;
        DB::beginTransaction();
        try {
            $cid = $req["cid"];
            if ($cid == 0) { // 新規
                $validatedData = $request->validate(['btn_upload_section' => 'required_if:notice_relation,1',],
                    ['btn_upload_section.required_if' => '公開対象が一部のとき、公開対象csvは必須です。']);

                $notice = new Notice;
                $notice->notice_date = $req["notice_date"];
                $notice->url = $req["url"];
                $notice->send_email_flag = $req["send_mail"];
                $notice->notice_comment = preg_replace("/\r\n|\r|\n/", '\n', htmlentities($req["notice_comment"]));
                $notice->save();
                // 公開範囲あり
                if ($req["notice_relation"] == 1) {
                    $res = $this->file_upload($notice->id, $req["file_data"]);
                    if ($res['status'] != 200 ) { //csv読み取りエラー
                        return back()->withInput()->with('res', $res);
                    }
                }

                // メール
                $now = date('Y-m-d H:i');
                $notice_date = date('Y-m-d 11:00', strtotime($req["notice_date"]));
                if ($req["send_mail"] == 1 && $notice_date <= $now) {
                    if ($req["notice_relation"] == 1) {
                        $users = User::join('notice_relation', 'notice_relation.customer_code', 'users.customer_code')
                            ->where('notice_relation.notice_id', $notice->id)
                            ->select('users.email')->get();
                    } else {
                        $users = User::select('users.email')->get();
                    }
                    foreach ($users as $value){
                        if (!empty($value->email)) {
                            Mail::to(mail_alias_replace($value->email))->send(new NoticeMail($notice->notice_comment));
                            Log::debug('complete send mail to : ' . $value->email);
                        }
                    }
                }
            } else { // 変更
                $notice = Notice::find( $cid );
                $notice->notice_date = $req["notice_date"];
                $notice->url = $req["url"];
                $notice->send_email_flag = $req["send_mail"];
                $notice->notice_comment = preg_replace("/\r\n|\r|\n/", '\n', htmlentities($req["notice_comment"]));
                $notice->save();
                // 公開範囲あり
                if ($req["notice_relation"] == 1 && !empty($req["file_data"]) ) {
                    $res = $this->file_upload($notice->id, $req["file_data"]);
                    if ($res['status'] != 200 ) { //csv読み取りエラー
                        return back()->withInput()->with('res', $res);
                    }
                } else if ($req["notice_relation"] == 0) {
                    $notice_relation_query = NoticeRelation::where('notice_id', $cid);
                    if ($notice_relation_query->count() > 0) {
                        NoticeRelation::where('notice_id', $cid)->delete(["updated_user_id" ,Session::get('user_login.customer_code',"")]);
                    }
                }
            }
            DB::commit();
        } catch (\PDOException $e) {
            Log::error($e);
            DB::rollBack();
            // セキュリティ上DBにアクセスできなかったことだけ返す
            $result = ["status" => 1];
        }

        // 失敗時は前画面に戻す
        if ($result["status"] == 1) {
            return back()->withInput()->with('status', '更新に失敗いたしました')->with('res', $res);
        }

        Log::debug( config('const.TitleName') );

        // ページ遷移
        return redirect()->route("regist_notice")->with('status', '記事変更しました。')->with('res', $res);
    }


    /**
     * リンク解除（削除）  
     */
    public function delete(Request $request)
    {
        Log::debug("AdminRegistNoticeController : delete");
        $req = $request->all();
        Log::debug($req);
        $cid = $req["cid"];
        $result = ["status" => 0];
        $user_code = Session::get('user_login.customer_code',"");
        try
        {
            // ソフト削除
            $notice = Notice::where( 'id', $cid );
            $notice_first = $notice->first();
            $notice_first->updated_user_id = $user_code;
            $notice_first->save(); // マイページIDを書き込む
            $notice->delete(["updated_user_id" ,$user_code]);

            // 公開範囲削除
            NoticeRelation::where('notice_id', $cid)->delete(["updated_user_id" ,$user_code]);

        } catch (\PDOException $e)
        {
            Log::error($e);
            // セキュリティ上DBにアクセスできなかったことだけ返す
            $result = ["status" => 1];
        }

        // 失敗時は前画面に戻す
        if ($result["status"] == 1) {
            return back()->withInput()->with('status', '更新に失敗いたしました');
        }

        // ページ遷移
        return redirect()->route("regist_notice")->with('status', '記事削除しました。');
    }

    /** 表示上限 */
    const RETURN_MAX = 100;

    /**
     * 公開範囲取込
     */
    public function file_upload($notice_id, $file){
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

        if (strlen($file) > 0) {
            $xls_temp = explode( ",", $file);
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

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $spreadsheet   = $reader->load($inputFileName);
  
        $sheet_shozoku = $spreadsheet->getSheet(0);
  
        // rowとcolのデータ領域を確認
        /** 合計件数として戻す */ 
        $rowMax = $sheet_shozoku->getHighestRow();
        $colMaxStr = $sheet_shozoku->getHighestColumn();
        /** 最大列数 */
        $colMax = Coordinate::columnIndexFromString($colMaxStr);
  
        // col が1列でなければエラーを返す
        if ( $colMax != 1 ) {
            $this->remove_cachefile($inputFileName);
            // error
            $value = [];
            $value["status"] = "409";
            $value["sheet_data"] = "";
            $value["results"] = [0,0,0,0];
            $value["message"] = "csvテンプレートが異なります";
  
            // 作業ログに記録： 戻り値の説明追加
            // $req["status"] = $value["status"];
            // $req["message"] = $value["message"] ;
            // $operation_history_log = new \App\Http\Middleware\OperationHistoryMiddleware;
            // $operation_history_log->OperationHistoryManual($request, $req);
            
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
            if (mb_strlen($sheetData[$r][1] , '8bit') > 10 || mb_strlen($sheetData[$r][1] , '8bit') < 8) {
                $tempv = $sheetData[$r];
                $tempv[1] = $tempv[1];
                array_unshift($tempv , $r);
                array_push($tempv, "1列目がマイページID(8~10桁)ではありません");
                $resNg ++;
                // 先頭RETURN_MAX行返す
                if ($resNg <= self::RETURN_MAX) {
                    $errorList[] = $tempv;
                }
            }

            if (!preg_match('/^([A-Z]{2})(\d+)$/',$sheetData[$r][1])) {
                $tempv = $sheetData[$r];
                $tempv[1] = $tempv[1];
                array_unshift($tempv , $r);
                array_push($tempv, "1列目がマイページID(ローマ字2文字+数字8桁)ではありません");
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
            // $req["status"] = $value["status"];
            // $req["message"] = $value["message"] ;
            // $operation_history_log = new \App\Http\Middleware\OperationHistoryMiddleware;
            // $operation_history_log->OperationHistoryManual($request, $req);

            return $value;
        }
  
        /** 戻す代表値 */
        $ReturnData = [ ];
        /** 作業者記載 ユーザコード */
        $user_code = Session::get('user_login.customer_code',"");
          
        Log::channel('importlog')->debug("Transaction Start");
        try
        {
          // リストにあるusage_tをすべて追加
          foreach ($sheetData as $temp_data) {
  
            $notice_relation = NoticeRelation::firstOrNew([
              "notice_id" => $notice_id,
              "customer_code" => $temp_data[1]
            ], [
              "created_user_id" => $user_code,
              "updated_user_id" => $user_code,
            ]);
  
            // 新規か既存かの判定 ex. Laravel firstOrNew how to check if it's first or new?
            // https://stackoverflow.com/questions/30686880/laravel-firstornew-how-to-check-if-its-first-or-new
            if ($notice_relation->exists) {
              // Update
              $notice_relation->updated_user_id = $user_code;
              $resUpd ++;
              // throw new Exception('ゼロによる除算。');
            } else {
                $resIns++;
            }

            $notice_relation->save();
  
            // 先頭RETURN_MAX行返す
            if (($resUpd + $resIns) <= self::RETURN_MAX) {
              $tempv = $temp_data;
              array_unshift($tempv , ($resUpd + $resIns));
              array_push($tempv, "");
              $ReturnData[] = $tempv;
            }
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
        //   $req["status"] = $value["status"];
        //   $req["message"] = $value["message"] . ($resIns + $resUpd) .":" . substr( $e , 400) ; // 先頭400文字だけ
        //   $operation_history_log = new \App\Http\Middleware\OperationHistoryMiddleware;
        //   $operation_history_log->OperationHistoryManual($request, $req);
  
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

    public function download(Request $request)
    {
        // 
        $req = $request->all();
        
        Log::debug( $req['notice_id'] );

        $notice_relation_query = NoticeRelation::where('notice_id', $req['notice_id']);
        $result = $notice_relation_query->get();

        // csvを元データをもとに生成する
        $encoded_csv = "";
        foreach( $result as $value) {

            $encoded_csv.= $value->customer_code . "\r\n";
        }

        // 戻り値に割り当てられた 請求データ を渡す。
        $value = [];
        $value["status"]       = "200";
        $value["file_name"]    = "送信対象_" . $req['notice_id'] . ".csv";
        $value["encoded_csv"]  = base64_encode( $encoded_csv );
        $value["message"]      = "OK";
        return $value;            
    }

}
