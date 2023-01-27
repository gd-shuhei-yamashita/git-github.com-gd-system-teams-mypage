<?php

namespace App\Models\API;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * API接続してデータのやり取りするモデルの継承元
 */
class MobileFileMaker extends BaseApi
{

    private $customerId;
    private $fm_host;
    private $fm_port;
    private $fm_path;
    private $fm_userName;
    private $fm_password;
    private $upload_filePath;
    const DELIVER_TIME_LABEL = [
        '01' => '午前中',
        '12' => '12時～14時',
        '14' => '14時～16時',
        '16' => '16時～18時',
        '18' => '18時～20時',
        '19' => '19時～21時',
        '99' => '午前中',// 指定なし　※時間コードが99(指定なし)の時は、午前中の指定にさせる
    ];
    const WHITE_SPACE = '';

    /**
     * コンストラクタ
     */
    public function __construct()	{
        // ファイルメーカーのSFTP接続設定
        // TODO: 直接書かない。envファイルなどで管理する。
        $this->fm_host = 'fms.grandata-grp.co.jp';
        $this->fm_port = 22;
        $this->fm_path = '/Library/FileMaker Server/Data/Documents/csv/';
        $this->fm_userName = 'grandata';
        $this->fm_password = 'Nori52@?';
        $this->upload_filePath = '';
        $this->powerCustomerNumberBefore = null;
    }


    /**
     * FMサーバーから対象ファイル取得
     * @param string $directory
     * @param string $searchFileName
     * @return string $csv // ダウンロード先のパス
     */
    public function getFileFromFmServer($directory, $searchFileName) {
        // ファイル取得処理
        $src = $this->fm_path. $directory . $searchFileName;
        $csv = storage_path() . '/temp/' . $searchFileName;

        $fh = fopen($csv, 'w');
        if (!$fh){
            throw new \Exception('ファイルが保存できません。');
        }

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_FILE, $fh);

        // プロトコルの設定
        curl_setopt($curl, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);

        // 認証は適宜修正(今回は、id:pass)
        curl_setopt($curl, CURLOPT_SSH_AUTH_TYPES, CURLSSH_AUTH_PASSWORD);
        curl_setopt($curl, CURLOPT_USERPWD, "{$this->fm_userName}:{$this->fm_password}");
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


    /**
     * ファイルのデータを取得
     * @param string $file_path
     * @return array
     */
    public function getCsvFileData($file_path){
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $spreadsheet   = $reader->load($file_path);

        $sheet_shozoku = $spreadsheet->getSheet(0);

        $rowMax = $sheet_shozoku->getHighestRow();
        $colMaxStr = $sheet_shozoku->getHighestColumn();

        $colMax = Coordinate::columnIndexFromString($colMaxStr);

        /** 配列へ、1セルごとにcsvデータを取得 */
        $sheetData = [];
        for($r = 1; $r <= $rowMax; $r ++) {
            for($c = 1; $c <= $colMax; $c ++) {
                $cell = $sheet_shozoku->getCellByColumnAndRow($c, $r);
                $sheetData[$r][$c] = $cell->getValue();
            }
        }
        $this->removeCachefile($file_path);
        return $sheetData;
    }


    /**
     * 取得したcsvを契約テーブルに合わせてフォーマットする
     * @param string $file_path
     * @return int
     */
    public function formatForContract($sheetData){
        $mobile_contract = [];
        foreach ($sheetData as $key => $val) {
            if ($key == 1) continue;
            $plan = explode(':', $val[15], 2);
            $plan_type = explode(':', $val[16], 2);
            $status = explode(':', $val[17]);
            $mobile_param = [
                'customer_code' => $val[1],
                'supplypoint_code' => $val[2],
                'contract_name' => str_replace($this::WHITE_SPACE, '', $val[5]). str_replace($this::WHITE_SPACE, '', $val[6]),
                'address' => $val[8] . $val[9] .$val[10] . $val[11] . $val[12] . $val[13],
                'plan' => $plan[1] . '(' . $plan_type[1] . ')',
                'shop_name' => '',
                'mobile_type' => $val[16],
                'mobile_status' => $val[17],
                'status' => $val[17] === '契約中' ? 1 : 0,
                'mobile_contract_date' => $val[4],
                'service' => 'wifi',
                'contract_code' => 'wifi',
                'pps_type' => 'wifi',
                'delivery_date' => $this->parseDate($val[18]),
                'delivery_time' => $this->getDisplayName($val[19]),
                'yobi_1' => $val[18],
                'yobi_2' => $val[19],
                'yobi_3' => $val[20],
                'yobi_4' => $val[21],
            ];
            array_push($mobile_contract, $mobile_param);
        }
        return $mobile_contract;
    }

    /**
     * @param string $dateString
     * @return string
     */
    private function parseDate($dateString)
    {
        if (strlen($dateString) >= 8) {
            return substr($dateString, 0, 4) . '-' . substr($dateString, 4, 2) . '-' . substr($dateString, 6, 2);
        } else {
            return '';
        }
    }

    /**
     * @param string $deliveryTimeCode
     * @return string
     */
    private function getDisplayName($deliveryTimeCode)
    {
        $name = '';
        if ($deliveryTimeCode && in_array($deliveryTimeCode, array_keys($this::DELIVER_TIME_LABEL))) {
            return $this::DELIVER_TIME_LABEL[$deliveryTimeCode];
        }
        return $name;
    }


    /**
     * 一時ファイルの削除
     * @param string $file_path
     * @return int
     */
    private function removeCachefile($inputFileName)
    {
        if (file_exists($inputFileName)){
            unlink($inputFileName);
            return 0;
        } else {
            return 1;
        }
    }
}
