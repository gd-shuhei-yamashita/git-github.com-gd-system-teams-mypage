<?php
namespace App\Http\Controllers\Api;

use Request;
use App\Mail\ReminderMail;
use App\Mail\TemporaryPasswordMail;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

use App\Consts\SupplierConsts;

use App\Http\Traits\SmsSender;

use Exception;
use RuntimeException;

// eloquent
use App\User;
use App\UserSub;
use App\Contract;

use App\CustomerOrdered;

class PasswordReminderController extends Controller {

    /**
     * ユーザのリマインドメールからの取得 / 
     *
     * @return Response
     */
    function store(Request $request) {

      Log::debug("Controller: PasswordReminderController.php");

      # (Ajax)メール送信対象の取得
      $email = Request::input('email');
      $uuid = get_csrf_token(); // from common/helper.php  
      $remember_token = password_hash($uuid, PASSWORD_DEFAULT);
      Log::debug("remember_token hash : " . $remember_token);
      $email_ok = 0;
      $addtional_auth_flag = false;
      $temporary_password_flag = false;
      // データベースを2系統使うコンフィグの場合、両方で走査を行い返信する。 
      // データベースの1
      try {
        DB::beginTransaction();
        $results = user::on('mysql')->where('email', $email)
        ->whereNull('deleted_at')->first();
        if (isset($results->id)) { // マイページに存在する
          $results->reminder_expired_at = DB::raw('DATE_ADD(NOW(), INTERVAL 24 HOUR)'); // 24時間を期限
          $results->password_reminder  = $remember_token;
          $results->updated_user_id = "anyone";
          $results->save();

          $url= route('password_init') . "?remember_token={$uuid}&email=" . urlencode($email);
          
          // リマインダーメール送信
          Mail::to(mail_alias_replace($email))->send(new ReminderMail($url));

          Log::debug("OK  email:" . $email . " / UUID:" . $uuid);
        } else { // マイページに存在しない
          // Mallieチェック
          $mallie_user = CustomerOrdered::where('mail_address', $email)->first();
          if (isset($mallie_user)) { //Mallieには存在する
            $this->regist_user_from_mallie($mallie_user);
            $temporary_password_flag = true;
          } else { //Mallieにも存在しない
            $addtional_auth_flag = true;
          }
        }
        // セキュリティ上DBにアクセスできたことだけ返す
        $email_ok ++;
        DB::commit();
      } catch (\PDOException $e) {
        Log::error("[ERROR] database connection failed :");
        Log::error($e);
        DB::rollback();
        return false;
      }

      // データベースの2 (マルチタイプ_マスター の場合のみ)
      if (config('const.DBPlacement') == 'multi_master' ) {
        try {
          $results = user::on('mysql2')->where('email', $email)->first();
          if (isset($results->id)) {
            $results->reminder_expired_at = DB::raw('DATE_ADD(NOW(), INTERVAL 24 HOUR)'); // 24時間を期限
            $results->password_reminder  = $remember_token;
            $results->updated_user_id = "anyone";
            $results->save();

            $url= route('password_init2') . "?remember_token={$uuid}&email=" . urlencode($email);
            
            // リマインダーメール送信
            Mail::to(mail_alias_replace($email))->send(new ReminderMail($url));

            Log::debug("OK  email:" . $email . " / UUID:" . $uuid);
          } else {
            Log::debug("NG  email:" . $email . " / UUID:" . $uuid);
          }
          $status = 0;
        } catch (\PDOException $e) {
          Log::error("[ERROR] database connection failed :");
          Log::error($e);
          // セキュリティ上DBにアクセスできなかったことだけ返す
          //$status ++;
          // セキュリティ上DBにアクセスできたことだけ返す
          $email_ok ++;
        }
      }

      // // 帰り値
      // if ($email_ok == 0) {
      //   $result = ["status" => 1]; // 送信失敗
      // } else {
      //   $result = ["status" => 0]; // 該当したユーザにメール送信した。
      // }

      $result = ["status" => 0, "addtional_auth_flag" => $addtional_auth_flag, "temporary_password_flag" => $temporary_password_flag, "email" => $email];
      // 戻り値は、DBと正しくやり取りできたかどうかのみを返すようにする。
      return json_encode($result);
    }

    /**
     * 追加認証(生年月日、携帯番号)
     */
    function addtional_auth(Request $request) {
      $phone_num = Request::input('phone_num');
      $mobile_phone = substr($phone_num, 0, 3) . '-' . substr($phone_num, 3, 4) . '-' . substr($phone_num, 7, 4);
      $birthday = Request::input('year') . '-' . Request::input('month') . '-' . Request::input('day');
      $multiple_contract_flg = false;
      $error_msg = '';
      try {
        // Mallieチェック
        $mallie_query = CustomerOrdered::where('mobile_phone', $mobile_phone)
        ->where('birthday', $birthday);
        $users_count = $mallie_query->count();
        if ($users_count > 1) { // 複数顧客見つかった場合、エラーモーダル表示
          $multiple_contract_flg = true;
        } elseif ($users_count == 1) { // 顧客が一意になった場合
          DB::beginTransaction();
          $mallie_user = $mallie_query->first();
          // マリーの情報をマイページに反映
          $this->regist_user_from_mallie($mallie_user);
          // ログインID、パスワード確認のURLをSMSで送信
          $sms_responses = SmsSender::LoginInfoSmsSend($phone_num, $mallie_user->code);
          if ($sms_responses['http_status'] != "200" || $sms_responses['status'] != 1) {
            Log::error($sms_responses['response_json']);
            throw new Exception('SMS送信失敗');
          }
          DB::commit();
        } else {
          $error_msg = 'ご本人様情報が確認できませんでした。入力内容をお確かめください。';
        }
      } catch (\PDOException $e) {
        Log::error("[ERROR] database connection failed :");
        Log::error($e);
        DB::rollback();
        return false;
      } catch (Exception $e) {
        Log::error($e);
        DB::rollback();
        return false;
      }

      $result = ["status" => 0, "multiple_contract_flg" => $multiple_contract_flg, "error_msg" => $error_msg];
      return json_encode($result);
    }

    /**
     * Mallieのユーザ情報をマイページへ登録する
     */
    function regist_user_from_mallie($mallie_user){
      // Mallieとマイページ紐づけチェック
      $mypage_user = user::on('mysql')->where('customer_code', $mallie_user->code)
      ->whereNull('deleted_at')->first();
      if (isset($mypage_user)) { //マイページに紐づいたレコードあり
        $mypage_user->email = $mallie_user->mail_address;
        $mypage_user->password = Hash::make($mallie_user->login_password);
        $mypage_user->updated_user_id = "anyone";
        $mypage_user->save();

      } else { //マイページに紐づいたレコードなし
        // マイページに登録する情報をMallieから取得
        $mallie_query = DB::connection('mysql_mallie')->table('HalueneContract AS HC');
        $mallie_query->join('CustomerOrdered AS CO', 'HC.customer_id', 'CO.id');
        $mallie_query->join('HaluenePowerPlan AS HPP', 'HC.power_plan_id', 'HPP.id');
        $mallie_query->select('CO.code AS customer_code');
        $mallie_query->addSelect('HC.power_customer_location_number AS supplypoint_code');
        $mallie_query->addSelect('HC.code AS contract_code');
        $mallie_query->addSelect('HC.pps_business_number AS pps_business_number');
        $mallie_query->addSelect('HC.power_customer_name AS contract_name');
        $mallie_query->addSelect('HC.switching_scheduled_date AS switching_scheduled_date');
        $mallie_query->addSelect('HPP.name_printed AS plan');
        $mallie_query->addSelect('CO.mail_address AS mail_address');
        $mallie_query->selectRaw(
          'CASE HC.power_location_address_type
          WHEN 1 THEN CONCAT(IFNULL(CO.prefecture, ""), IFNULL(CO.city, ""), IFNULL(CO.town, ""), IFNULL(CO.street_number_choume, ""), IF(CHAR_LENGTH(CO.street_number_choume) != 0,"　",""), IFNULL(CO.street_number_banchi, ""), IF(CHAR_LENGTH(CO.building_name) != 0, "　", ""), IFNULL(CO.building_name, ""))
          ELSE CONCAT(IFNULL(HC.power_prefecture, ""), IFNULL(HC.power_city, ""), IFNULL(HC.power_town, ""), IFNULL(HC.power_street_number_choume, ""), IF(CHAR_LENGTH(HC.power_street_number_choume) != 0, "　", ""), IFNULL(HC.power_street_number_banchi, ""), IF(CHAR_LENGTH(HC.power_building_name) != 0, "　", ""), IFNULL(HC.power_building_name, ""), IFNULL(HC.power_room_number, ""))
          END AS address'
        );
        $mallie_query->where('HC.status', 1);
        $mallie_query->where('CO.code', $mallie_user->code);

        if ($mallie_query->count() == 0) {
          throw new \Exception("couldn't get Mallie's record for register on MyPage");
        }

        $count = 0;
        foreach ($mallie_query->get() as $mallie_data) {

          if ($count == 0) {
            // userレコード追加
            $insert_user = new User();
            $insert_user->name = $mallie_data->contract_name;
            $insert_user->email = $mallie_data->mail_address;
            $insert_user->password = Hash::make($mallie_user->login_password);
            $insert_user->customer_code = $mallie_data->customer_code;
            $insert_user->created_user_id = "anyone";
            $insert_user->save();
          }
          
          // Contractレコード追加
          $insert_contract = new Contract();
          $insert_contract->customer_code = $mallie_data->customer_code;
          $insert_contract->supplypoint_code = empty($mallie_data->supplypoint_code) ? $count : $mallie_data->supplypoint_code;
          $insert_contract->contract_code = $mallie_data->contract_code;
          if ($mallie_data->pps_business_number == SupplierConsts::GRANDATA_ELECTRIC) {
            $insert_contract->pps_type = SupplierConsts::GRANDATA_ELECTRIC_INDEX;
          } else if ($mallie_data->pps_business_number == SupplierConsts::GRANDATA_GAS) {
            $insert_contract->pps_type = SupplierConsts::GRANDATA_GAS_INDEX;
          } else if ($mallie_data->pps_business_number == SupplierConsts::SAISAN_GAS) {
            $insert_contract->pps_type = SupplierConsts::SAISAN_GAS_INDEX;
          } else if ($mallie_data->pps_business_number == SupplierConsts::FAMILY_NET_JAPAN_GAS) {
            $insert_contract->pps_type = SupplierConsts::FAMILY_NET_JAPAN_GAS_INDEX;
          } else if ($mallie_data->pps_business_number == SupplierConsts::HTB_ENERGY_ELECTRIC) {
            $insert_contract->pps_type = SupplierConsts::HTB_ENERGY_ELECTRIC_INDEX;
          }
          $insert_contract->contract_name = $mallie_data->contract_name;
          $insert_contract->address = $mallie_data->address;
          $insert_contract->plan = $mallie_data->plan;
          $insert_contract->shop_name = '';
          $insert_contract->switching_scheduled_date = $mallie_data->switching_scheduled_date;
          $insert_contract->created_user_id = "anyone";
          $insert_contract->save();

          $count = $count + 1;
        }

        // Mallieの情報更新
        $mallie_user->login_id = $mallie_user->code;
        $mallie_user->updater = 'anyone';
        $mallie_user->updatedate = date("Y-m-d H:i:s");
        $mallie_user->save();
      }
    }
}
