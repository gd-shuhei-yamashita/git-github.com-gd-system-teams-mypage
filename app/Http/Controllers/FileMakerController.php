<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\Session;

class FileMakerController extends Controller
{

    private $customerId;
    private $fm_host;
    private $fm_port;
    private $fm_path;
    private $fm_userName;
    private $fm_password;
    private $upload_filePath;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        // ファイルメーカーのSFTP接続設定
        $this->fm_host = 'fms.grandata-grp.co.jp';
        $this->fm_port = 22;
        $this->fm_path = '/Library/FileMaker Server/Data/Documents/csv/';
        $this->fm_userName = "grandata";
        $this->fm_password = "Nori52@?";
        $this->upload_filePath = "";
        $this->powerCustomerNumberBefore = null;
    }

    /**
     * FMサーバーから対象ファイル取得
     * @param string $directory
     * @param string $searchFileName
     * @return string $csv // ダウンロード先のパス
     * 
     */
    public function downloadForFileMaker($directory, $searchFileName) {
        // ファイル取得処理
        $src = $this->fm_path. $directory . $searchFileName;
        $csv = storage_path() . "/temp/" . $searchFileName;

        $fh = fopen($csv, 'w');
        if (!$fh){
            throw new \Exception("ファイルが保存できません。");
        }

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_FILE, $fh);

        // プロトコルの設定
        curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);

        // 認証は適宜修正(今回は、id:pass)
        curl_setopt($curl, CURLOPT_SSH_AUTH_TYPES, CURLSSH_AUTH_PASSWORD);
        curl_setopt($curl, CURLOPT_USERPWD,"{$this->fm_userName}:{$this->fm_password}");
        curl_setopt($curl, CURLOPT_PORT, $this->fm_port);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

        $url = "sftp://{$this->fm_host}/{$src}";
        curl_setopt($curl, CURLOPT_URL, $url);

        curl_exec($curl);
        fclose($fh);
        curl_close($curl);

        $file_data = file_get_contents($csv);
        $file_data = mb_convert_encoding($file_data, 'UTF-8', 'ASCII,JIS,UTF-8,SJIS-win');
        file_put_contents($csv, $file_data);

        return $csv; //ローカルのファイルパス
    }

    public function read_file($file_path){
  
      $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
      $spreadsheet   = $reader->load($file_path);

      $sheet_shozoku = $spreadsheet->getSheet(0);

      $rowMax = $sheet_shozoku->getHighestRow();
      $colMaxStr = $sheet_shozoku->getHighestColumn();

      $colMax = Coordinate::columnIndexFromString($colMaxStr);


      /** 配列へ、1セルごとにcsvデータを取得 */
      $sheetData = [ ];
      for($r = 1; $r <= $rowMax; $r ++) {
          for($c = 1; $c <= $colMax; $c ++) {
              $cell = $sheet_shozoku->getCellByColumnAndRow($c, $r);
              $sheetData[$r][$c] = $cell->getValue();
          }
      }

      // 一時ファイルの削除
      $this->remove_cachefile($file_path);
      return $sheetData;
    }

    // 取得したcsvをフォーマットする
    public function contract_format($sheetData){
      $mobile_contract = [];
      foreach ($sheetData as $key => $val) {
        if ($key == 1) {
            continue;
        }
        $plan = explode(':', $val[15], 2);
        $plan_type = explode(':', $val[16], 2);
        $mobile_param = [
            "customer_code" => $val[1],
            "supplypoint_code" => $val[2],
            "contract_name" => $val[5] . $val[6],
            "address" => $val[8] . $val[9] .$val[10] . $val[11] . $val[12] . $val[13],
            "plan" => $plan[1] . '(' . $plan_type[1] . ')',
            "shop_name" => '',
            "mobile_type" => $val[16],
            "mobile_status" => $val[17],
            "mobile_contract_date" => $val[4],
            "service" => 'wifi',
            "contract_code" => 'wifi',
            "pps_type" => 'wifi',
        ];
        array_push($mobile_contract, $mobile_param);
      }
      return $mobile_contract;
    }

    public function remove_cachefile($inputFileName)
    {
      if (file_exists($inputFileName)){
        unlink($inputFileName);
        return 0;
      } else {
        return 1;
      }
    }
}
