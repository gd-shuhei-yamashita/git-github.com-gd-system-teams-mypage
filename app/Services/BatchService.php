<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * バッチ処理系
 */
class BatchService {

    /**
     * 使用量データをインポートします
     * @param type $path
     */
    public function ImporUsageData($path) {

        Log::channel("batch")->Info("使用量データインポート呼び出し…");
        Log::channel("batch")->Info("使用量データインポート読み出し対象ファイル={$path}");

        $isValid = $this->validateCsv($path, 1);
        if (!$isValid) {
            Log::channel("batch")->Info("バリデートエラー：インポート終了");
            return false;
        }
        $duplicate_data = [];
        try {
            $this->loadCSVFile($path, function($csv, $index) use(&$duplicate_data) {

                // バリデーション
                // if (!$this->validateUsage($csv, $index)) {
                //     return false;
                // }

                /**
                 * 必須：供給地点番号(数字22桁),
                 * 必須：利用年月(YYYY/MM),
                 * 必須：使用量(数字),
                 * 必須：顧客ID(英字2桁+数字8桁)
                 */
                list($supplypoint_code, $usage_date, $usage, $customer_code) = $csv;

                // データ更新
                $UsageT = \App\UsageT::firstOrNew (
                        [
                            "supplypoint_code" => $supplypoint_code,
                            "usage_date" => (int)str_replace("/","",$usage_date),
                            "customer_code" => $customer_code,
                        ], 
                        [
                            "usage" => isset($usage) ? ($usage == '' ? NULL : $usage) : NULL
                        ]
                );

                if ($UsageT->exists) {
                    // Update
                    $UsageT->usage = isset($usage) ? ($usage == '' ? NULL : $usage) : NULL;
                    $UsageT->updated_user_id = 'batch_service';
                    // $resUpd ++;
                    $duplicate_data[$index] = $supplypoint_code
                                        . ' / ' . str_replace("/","",$usage_date)
                                        . ' / ' . $customer_code;
                  } else {
                    // Insert
                    $UsageT->usage = isset($usage) ? ($usage == '' ? NULL : $usage) : NULL;
                    $UsageT->created_user_id = 'batch_service';
                    $UsageT->updated_user_id = 'batch_service';
                    // $resIns ++;
                  }
                  $UsageT->save();
            });
        } catch (\Exception $e) {
            //ロールバック
            Log::channel("batch")->Error("ロールバックが発生しました");
            Log::channel("batch")->Error($e->getMessage());

            var_dump($e->getMessage());
            // $to = config("const.AdmErrMail");
            // Mail::to(mail_alias_replace($to))->send(new \App\Mail\AdminErrorMail($e->getMessage()));
            return false;
        }
        if (count($duplicate_data) > 0) {
            Log::channel("batch")->Info("更新レコード");
            Log::channel("batch")->Info($duplicate_data);
        }
        Log::channel("batch")->Info("使用量データインポート完了！");
        var_dump("使用量データインポート完了！");
        return true;
    }

    /**
     * 内訳データをインポートします
     * @param type $path
     */
    public function ImportMeisaiData($path) {

        Log::channel("batch")->Info("内訳データインポート呼び出し…");
        Log::channel("batch")->Info("内訳データインポート読み出し対象ファイル={$path}");

        $isValid = $this->validateCsv($path, 2);
        if (!$isValid) {
            Log::channel("batch")->Info("バリデートエラー：インポート終了");
            return false;
        }
        $duplicate_data = [];
        try {
            $this->loadCSVFile($path, function($csv, $index) use(&$duplicate_data) {
                // バリデーション
                // if (!$this->validateMeisai($csv, $index)) {
                //     return false;
                // }
                
                /**
                 * 必須：請求番号(19桁),
                 * 必須：内訳コード(55桁),
                 * 必須：明細表示順(数字),
                 * 必須：内訳名(数字),
                 * 必須：内訳金額(数値),
                 * ノート
                 */
                list(
                    $billing_code,
                    $itemize_code,
                    $itemize_order,
                    $itemize_name,
                    $itemize_bill,
                    $note
                        ) = $csv;

                // データ更新
                $BillingItemize = \App\BillingItemize::firstOrNew([
                    "billing_code" => $billing_code,
                    "itemize_code" => $itemize_code,
                    "itemize_order" => $itemize_order,
                  ], [
                    "itemize_name" => $itemize_name,
                    "itemize_bill" => $itemize_bill,
                    "note"         => $note,
        
                    // "created_user_id" => $user_code,
                    // "updated_user_id" => $user_code,
                  ]);
        
                // 新規か既存かの判定 ex. Laravel firstOrNew how to check if it's first or new?
                // https://stackoverflow.com/questions/30686880/laravel-firstornew-how-to-check-if-its-first-or-new
                if ($BillingItemize->exists) {
                    // Update
                    $BillingItemize->itemize_name = $itemize_name;
                    $BillingItemize->itemize_bill = $itemize_bill;
                    $BillingItemize->note = $note;
                    $BillingItemize->updated_user_id = 'batch_service';
                    // $resUpd ++;
                    $duplicate_data[$index] = $supplypoint_code
                                        . ' / ' . $billing_code
                                        . ' / ' . $itemize_order;
                } else {
                    // Insert
                    $BillingItemize->itemize_name = $itemize_name;
                    $BillingItemize->itemize_bill = $itemize_bill;
                    $BillingItemize->note = $note;
                    
                    $BillingItemize->created_user_id = 'batch_service';
                    $BillingItemize->updated_user_id = 'batch_service';
                    // $resIns ++;
                }
                
                $BillingItemize->save();

                // throw new \Exception;
            });
        } catch (\Exception $e) {
            //ロールバック
            Log::channel("batch")->Error("ロールバックが発生しました");
            Log::channel("batch")->Error($e->getMessage());
            var_dump($e->getMessage());
            // $to = config("const.AdmErrMail");
            // Log::channel("batch")->Error($to);
            // Mail::to(mail_alias_replace($to))->send(new \App\Mail\AdminErrorMail($e->getMessage()));
            return false;
        }
        if (count($duplicate_data) > 0) {
            Log::channel("batch")->Info("更新レコード");
            Log::channel("batch")->Info($duplicate_data);
        }
        Log::channel("batch")->Info("内訳データインポート完了！");
        var_dump("内訳データインポート完了！");
        return true;
    }

    /**
     * 請求データをインポートします
     */
    public function ImportBillingData($path) {
        Log::channel("batch")->Info("請求データインポート呼び出し…");
        Log::channel("batch")->Info("請求データインポート読み出し対象ファイル={$path}");

        $isValid = $this->validateCsv($path, 3);
        if (!$isValid) {
            Log::channel("batch")->Info("バリデートエラー：インポート終了");
            return false;
        }

        $duplicate_data = [];

        try {
            $this->loadCSVFile($path, function($csv, $index) use(&$duplicate_data) {

                // バリデーション
                // if (!$this->validateBilling($csv, $index)) {
                //     return false;
                // }

                /**
                 * 供給地点特定番号,
                 * 顧客ID,
                 * 請求番号,
                 * 内訳コード,
                 * 利用開始年月日,
                 * 利用終了年月日,
                 * 請求年月,
                 * 請求額,
                 * 消費税相当額,
                 * 支払種別,
                 * 力率,
                 * 検針月日,
                 * 次回検針予定日,
                 * 当月指示数,
                 * 前月指示数,
                 * 計器乗率,
                 * 差引,
                 * 当月お支払予定日,
                 * 利用年月
                 */
                list(
                    $supplypoint_code,
                    $customer_code,
                    $billing_code,
                    $itemize_code,
                    $start_date,
                    $end_date,
                    $billing_date,
                    $billing_amount,
                    $tax,
                    $payment_type,
                    $power_percentage,
                    $metering_date,
                    $next_metering_date,
                    $main_indicator,
                    $main_indicator_last_month,
                    $meter_multiply,
                    $difference,
                    $payment_date,
                    $usage_date,
                        ) = $csv;

                // データ更新
                $Billing = \App\Billing::firstOrNew([
                    "supplypoint_code" => $supplypoint_code,
                    "customer_code" => $customer_code,
                    "billing_code" => $billing_code,
                    "itemize_code" => $itemize_code            
                  ], [
                    "start_date"=> $start_date,
                    "end_date"  => $end_date,
                    "billing_date"    => (int)str_replace("/","",$billing_date),
                    "billing_amount"  => $billing_amount,
                    "tax"             => $tax,
                    "payment_type"    => $payment_type,
                    "power_percentage"=> !empty($power_percentage) ? $power_percentage : 0,
                    "metering_date"   => $metering_date,
                    "next_metering_date"   => !empty($next_metering_date) ? $next_metering_date : NULL,
                    "main_indicator"       => !empty($main_indicator) ? $main_indicator :0,
                    "main_indicator_last_month"   => !empty($main_indicator_last_month) ? $main_indicator_last_month : 0,
                    "meter_multiply"   => !empty($meter_multiply) ? $meter_multiply : 0,
                    "difference"     => !empty($difference) ? $difference : 0,
                    "payment_date"   => $payment_date,
                    "usage_date" => (int)str_replace("/","",$usage_date),
                  ]);
        
                  // 新規か既存かの判定 ex. Laravel firstOrNew how to check if it's first or new?
                  // https://stackoverflow.com/questions/30686880/laravel-firstornew-how-to-check-if-its-first-or-new
                  if ($Billing->exists) {
                    // Update
                    $Billing->start_date = $start_date;
                    $Billing->end_date = $end_date;
                    $Billing->billing_date = (int)str_replace("/","",$billing_date);
                    $Billing->billing_amount = $billing_amount;
                    $Billing->tax = $tax;
                    $Billing->payment_type = $payment_type;
                    $Billing->power_percentage = !empty($power_percentage) ? $power_percentage : 0;
                    $Billing->metering_date = $metering_date;
                    $Billing->next_metering_date = !empty($next_metering_date) ? $next_metering_date : NULL;
                    $Billing->main_indicator = !empty($main_indicator) ? $main_indicator : 0;
                    $Billing->main_indicator_last_month = !empty($main_indicator_last_month) ? $main_indicator_last_month : 0;
                    $Billing->meter_multiply = !empty($meter_multiply) ? $meter_multiply : 0;
                    $Billing->difference = !empty($difference) ? $difference :0;
                    $Billing->payment_date = $payment_date;
                    $Billing->usage_date = (int)str_replace("/","",$usage_date);
                    $Billing->updated_user_id = 'batch_service';
                    // $resUpd ++;
                    $duplicate_data[$index] = $supplypoint_code
                                        . ' / ' . $customer_code
                                        . ' / ' . $billing_code
                                        . ' / ' . $itemize_code;
                  } else {
                    // Insert
                    $Billing->created_user_id = 'batch_service';
                    $Billing->updated_user_id = 'batch_service';
                    // $resIns ++;
                  }
                  $Billing->save();
            });
        } catch (\Exception $e) {
            //ロールバック
            Log::channel("batch")->Error("ロールバックが発生しました");
            Log::channel("batch")->Error($e->getMessage());

            var_dump($e->getMessage());
            // $to = config("const.AdmErrMail");
            // Mail::to(mail_alias_replace($to))->send(new \App\Mail\AdminErrorMail($e->getMessage()));
            return false;
        }

        if (count($duplicate_data) > 0) {
            Log::channel("batch")->Info("更新レコード");
            Log::channel("batch")->Info($duplicate_data);
        }
        Log::channel("batch")->Info("請求データインポート完了！");
        var_dump("請求データインポート完了！");
        return true;
    }

    private function loadCSVFile($path, callable $func) {

        if (!file_exists($path)) {
            throw new \Exception("<$path>ファイルが存在しません");
        }

        $data = file_get_contents($path);
        $data = mb_convert_encoding($data, 'UTF-8', 'SJIS-win');
        $temp = tmpfile();
        $meta = stream_get_meta_data($temp);
        fwrite($temp, $data);
        rewind($temp);

        $csv = new \SplFileObject($meta['uri'], 'rb');
        $csv->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE | \SplFileObject::READ_AHEAD);

        // ヘッダー行飛ばす
        // $header = $csv->fgetcsv();
        // Log::channel("batch")->Debug("ヘッダ情報:" . var_export($header, true));
        // var_dump($header);

        DB::transaction(function()
                use($func, $csv) {
            $index = 0;
            while ($row = $csv->fgetcsv()) {
                $index++;
                $func($row, $index);
                // printf("\r %010d 件　処理完了", $index);
            }
            printf("\r %010d 件　処理完了", $index);
        });
    }
    
    private function validateCsv ($path, $mode) {
        if (!file_exists($path)) {
            Log::channel("batch")->Error("<$path>ファイルが存在しません");
            return false;
        }

        $data = file_get_contents($path);
        $data = mb_convert_encoding($data, 'UTF-8', 'SJIS-win');
        $temp = tmpfile();
        $meta = stream_get_meta_data($temp);
        fwrite($temp, $data);
        rewind($temp);

        $csv = new \SplFileObject($meta['uri'], 'rb');
        $csv->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE | \SplFileObject::READ_AHEAD);

        $index = 0;
        $isValid = true;
        while ($row = $csv->fgetcsv()) {
            $index++;
            if ($mode === 1) {
                if (!$this->validateUsage($row, $index)) {
                    $isValid = false;
                }
            } elseif ($mode === 2) {
                if (!$this->validateMeisai($row, $index)) {
                    $isValid = false;
                }
            } elseif ($mode === 3) {
                if (!$this->validateBilling($row, $index)) {
                    $isValid = false;
                }
            } else {
                return false;
            }
        }

        return $isValid;
    }

    /**
     * 使用量csv項目チェック
     */
    private function validateUsage ($csv, $index) {
        // error
        $error = "";

        // col が4列でなければエラーを返す
        if ( count($csv) != 4 ) {
            $error .= "csvフォーマットが異なります";
            printf($error);
            printf('<br>');
            return false;
        }

        list($supplypoint_code, $usage_date, $usage, $customer_code) = $csv;

        // 1 : supplypoint_code 供給地点特定番号 0300111001183222404031 22桁
        if (!(mb_strlen($supplypoint_code , '8bit') >= 17 && mb_strlen($supplypoint_code , '8bit') <= 22) || (!preg_match('/^(\d{17,22})$/', $supplypoint_code))) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "1列目が供給地点特定番号(17桁~22桁)ではありません:" . $supplypoint_code;
        }

        // 2 : usage_date 利用年月 2018/12 YYYY/MM
        if (!preg_match('/^(\d{4}\/\d{2})$/',$usage_date)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "2列目が利用年月(YYYY/MM)ではありません:" . $usage_date;
        }

        // 3 : usage 使用量 76  数値
        if (!preg_match('/^(\d+)$/',$usage) && (strlen(trim($usage)) != 0)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "3列目が使用量(数字)ではありません:" . $usage;
        }

        // 4 : customer_code マイページID MC00000042 桁数
        if (mb_strlen($customer_code , '8bit') > 10 || mb_strlen($customer_code , '8bit') < 8) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "4列目がマイページID(8~10桁)ではありません:" . $customer_code;
        }
      
        // 4 : customer_code マイページID MC00000042  アルファベット/数値
        if (!preg_match('/^([A-Z]{2})(\d+)$/',$customer_code)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "4列目がマイページID(ローマ字2文字+数字)ではありません:" . $customer_code;
        }

        if ($error !== "") {
            Log::channel("batch")->Error($index . "行目：" . $error);
            var_dump($index . "行目：" . $error);
            return false;
        }

        return true;
    }

    /**
     * 内訳csv項目チェック
     */
    private function validateMeisai ($csv, $index) {
        // error
        $error = "";

        // col が 5 or 6 列でなければエラーを返す (note が空白のケースが多い)
        if (count($csv) != 5 && count($csv) != 6) {
            $error .= "csvフォーマットが異なります";
            printf($error);
            printf('<br>');
            return false;
        }

        list(
            $billing_code,
            $itemize_code,
            $itemize_order,
            $itemize_name,
            $itemize_bill,
            $note
                ) = $csv;

        // 1 billing_code 請求番号 DENKIT0000027201812
        if (mb_strlen($billing_code , '8bit') != 19 ) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "1列目が請求番号(19桁)ではありません:" . $billing_code;
        }
  
        // 2 itemize_code 内訳コード DENKIT0000027201812030011100118322240403120181220181126
        if (mb_strlen($itemize_code , '8bit') != 55 && mb_strlen($itemize_code , '8bit') != 50 ) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "2列目が内訳コード(50桁または55桁)ではありません:" . $itemize_code;
        }        
          
        // 3 itemize_order 明細表示順 1
        if (!preg_match('/^(\d+)$/',$itemize_order)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "3列目が明細表示順(数字)ではありません:" . $itemize_order;
        }
  
        // 4 itemize_name 内訳名 "基本料金"
        if (mb_strlen($itemize_name , '8bit') == 0) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "4列目に内訳名の記載がありません:" . $itemize_name;
        }
  
        // 5 itemize_bill 内訳金額 "758.16"
        if (!preg_match('/^([0-9.\-]+)$/',$itemize_bill)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "5列目が内訳金額(数値)ではありません:" . $itemize_bill;
        }
  
        // 6 note ノート 
        // 特になし

        if ($error !== "") {
            Log::channel("batch")->Error($index . "行目：" . $error);
            // var_dump($index . "行目：" . $error);
            return false;
        }

        return true;
    }

    /**
     * 請求csv項目チェック
     */
    private function validateBilling ($csv, $index) {
        // error
        $error = "";

        // col が4列でなければエラーを返す
        if ( count($csv) != 19 ) {
            $error .= "csvフォーマットが異なります";
            printf($error);
            printf('<br>');
            return false;
        }

        list(
            $supplypoint_code,
            $customer_code,
            $billing_code,
            $itemize_code,
            $start_date,
            $end_date,
            $billing_date,
            $billing_amount,
            $tax,
            $payment_type,
            $power_percentage,
            $metering_date,
            $next_metering_date,
            $main_indicator,
            $main_indicator_last_month,
            $meter_multiply,
            $difference,
            $payment_date,
            $usage_date,
                ) = $csv;

        // 1 supplypoint_code 請求番号 0300111001183222404031
        if (!(mb_strlen($supplypoint_code , '8bit') >= 17 && mb_strlen($supplypoint_code , '8bit') <= 22) || (!preg_match('/^(\d{17,22})$/',$supplypoint_code))) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "1列目が供給地点特定番号(17桁~22桁)ではありません:" . $supplypoint_code;
        }
  
        // 2 customer_code マイページID MC00000042
        if (mb_strlen($customer_code , '8bit') > 10 || mb_strlen($customer_code , '8bit') < 8) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "2列目がマイページID(8~10桁)ではありません:" . $customer_code;
        }
  
        // 3 billing_code 請求番号 DENKIT0000027201812  19文字
        if (mb_strlen($billing_code , '8bit') != 19 ) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "3列目が請求番号(19桁)ではありません:" . $billing_code;
        }
  
        // 4 itemize_code 内訳コード DENKIT0000027201812030011100118322240403120181220181126  55文字
        if (mb_strlen($itemize_code , '8bit') != 55 && mb_strlen($itemize_code , '8bit') != 50 ) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "4列目が内訳コード(50桁または55桁)ではありません:" . $itemize_code;
        }
  
        // 5 start_date 利用開始年月日 2018/11/26
        if (!preg_match('/^(\d{4}\/\d{2}\/\d{2})$/',$start_date)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "5列目が利用開始年月日(YYYY/MM/DD)ではありません:" . $start_date;
        }
          
        // 6 end_date 利用終了年月日 2018/12/21
        if (!preg_match('/^(\d{4}\/\d{2}\/\d{2})$/',$end_date)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "6列目が利用終了年月日(YYYY/MM/DD)ではありません:" . $end_date;
        }
  
        // 7 billing_date 請求年月 2019/01
        if (!preg_match('/^(\d{4}\/?\d{2})$/',$billing_date)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "7列目が請求年月(YYYY/MM or YYYYMM)ではありません:" . $billing_date;
        }
  
        // 8 billing_amount 請求額 2388
        if (!preg_match('/^(\d+)$/',$billing_amount) && !preg_match('/^-(\d+)$/',$billing_amount)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "8列目が請求額(数字)ではありません:" . $billing_amount;
        }
  
        // 9 tax 消費税相当額 176
        if (!preg_match('/^(\d+)$/',$tax) && !preg_match('/^-(\d+)$/',$tax)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "9列目が消費税相当額(数字)ではありません:" . $tax;
        }
  
        // 10 payment_type 支払い種別 2
        if (!preg_match('/^(\d)$/',$payment_type)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "10列目が支払い種別(数字)ではありません:" . $payment_type;
        }
  
        // 11 power_percentage 力率 text
  
        // 12 metering_date 検針月日 2018/12/22
        if (!preg_match('/^(\d{4}\/\d{2}\/\d{2})$/',$metering_date)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "12列目が検針月日(YYYY/MM/DD)ではありません:" . $metering_date;
        }
  
        // 13 next_metering_date 次回検針予定日 2019/01/25 (ヌルは許可)
        if ((!preg_match('/^(\d{4}\/\d{2}\/\d{2})$/',$next_metering_date)) && (strlen(trim($next_metering_date)) != 0)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "13列目が次回検針予定日(YYYY/MM/DD)ではありません:" . $next_metering_date;
        }
  
        // 14 main_indicator 当月指示数 2076.4
        if (!preg_match('/^([0-9.]*)$/',$main_indicator)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "14列目が当月指示数(数値)ではありません:" . $main_indicator;
        }
  
        // 15 main_indicator_last_month 前月指示数 2000.1
        if (!preg_match('/^([0-9.]*)$/',$main_indicator_last_month)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "15列目が前月指示数(数値)ではありません:" . $main_indicator_last_month;
        }
  
        // 16 meter_multiply 計器乗率 
        // 判定なし
  
        // 17 difference 差引 76.3
        if (!preg_match('/^([0-9.]*)$/',$difference)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "17列目が差引(数値)ではありません:" . $difference;
        }
  
        // 18 payment_date 当月お支払い予定日 ご契約のクレジットカード会社に準拠
        // 判定なし
  
        /// 19 usage_date 利用年月 
        if (!preg_match('/^(\d{4}\/\d{2})$/',$usage_date)) {
            if ($error !== "") {
                $error .= " / ";
            }
            $error .= "19列目が利用年月(YYYY/MM)ではありません:" . $usage_date;
        }

        if ($error !== "") {
            Log::channel("batch")->Error($index . "行目：" . $error);
            var_dump($index . "行目：" . $error);
            return false;
        }

        return true;
    }
}
