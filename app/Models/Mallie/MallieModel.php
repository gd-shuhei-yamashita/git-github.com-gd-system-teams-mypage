<?php

namespace App\Models\Mallie;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

use App;
use App\Http\Traits\SmsSender;

use App\Models\Mallie\Library;
use App\Models\DB\User;
use App\Models\DB\Contract;


/**
 * 共通して行う処理などを記述
 * 
 */
class MallieModel extends Model
{

    protected $connection = 'mysql_mallie';

    /**
     * コンストラクタ
     */
    // public function __construct(array $attributes = [])	{
    //     parent::__construct($attributes);
    // }


    /**
     * マリーの情報をマイページに反映してSMSを送信
     * @param object $customerOrdered
     * @param bool
     */
    final public static function createMypageData($customerOrdered) {
        DB::beginTransaction();
        $mypageUser = User::getByCustomerCode($customerOrdered->code);

        if (isset($mypageUser)) {
            $mypageUser->email = $customerOrdered->mail_address;
            $mypageUser->password = Hash::make($customerOrdered->login_password);
            $mypageUser->updated_user_id = 'anyone';
            $mypageUser->save();
        } else {
            // マイページに登録する情報をMallieから取得
            $mallieContracts = DB::connection('mysql_mallie')
                ->table('HalueneContract AS HC')
                ->join('CustomerOrdered AS CO', 'HC.customer_id', 'CO.id')
                ->join('HaluenePowerPlan AS HPP', 'HC.power_plan_id', 'HPP.id')
                ->select('CO.code AS customer_code')
                ->addSelect('HC.power_customer_location_number AS supplypoint_code')
                ->addSelect('HC.code AS contract_code')
                ->addSelect('HC.pps_business_number AS pps_business_number')
                ->addSelect('HC.power_customer_name AS contract_name')
                ->addSelect('HC.switching_scheduled_date AS switching_scheduled_date')
                ->addSelect('HPP.name_printed AS plan')
                ->selectRaw(
                    'CASE HC.power_location_address_type
                    WHEN 1 THEN CONCAT(IFNULL(CO.prefecture, ""), IFNULL(CO.city, ""), IFNULL(CO.town, ""), IFNULL(CO.street_number_choume, ""), IF(CHAR_LENGTH(CO.street_number_choume) != 0,"　",""), IFNULL(CO.street_number_banchi, ""), IF(CHAR_LENGTH(CO.building_name) != 0, "　", ""), IFNULL(CO.building_name, ""))
                    ELSE CONCAT(IFNULL(HC.power_prefecture, ""), IFNULL(HC.power_city, ""), IFNULL(HC.power_town, ""), IFNULL(HC.power_street_number_choume, ""), IF(CHAR_LENGTH(HC.power_street_number_choume) != 0, "　", ""), IFNULL(HC.power_street_number_banchi, ""), IF(CHAR_LENGTH(HC.power_building_name) != 0, "　", ""), IFNULL(HC.power_building_name, ""), IFNULL(HC.power_room_number, ""))
                    END AS address'
                )
                ->where('HC.status', 1)
                ->where('CO.code', $customerOrdered->code)
                ->get();

            if (count($mallieContracts) === 0) {
                throw new \Exception('couldn\'t get Mallie\'s record for register on MyPage');
            }

            foreach ($mallieContracts as $key => $mallieData) {
                // Userレコード追加
                if ($key === 0) {
                    User::create([
                        'name'  => $mallieData->contract_name,
                        'email' => $customerOrdered->mail_address,
                        'password' => $customerOrdered->login_password,
                        'customer_code' => $customerOrdered->code,
                        'zip_code' => $customerOrdered->zip_code,
                        'phone' => $customerOrdered->mobile_phone
                    ]);
                }

                // Contractレコード追加
                Contract::create([
                    'customer_code' => $mallieData->customer_code,
                    'supplypoint_code' => empty($mallieData->supplypoint_code) ? ($key + 1) : $mallieData->supplypoint_code,
                    'contract_code' => $mallieData->contract_code,
                    'pps_type' => Library::getPpsType($mallieData->pps_business_number),
                    'contract_name' => $mallieData->contract_name,
                    'address' => $mallieData->address,
                    'plan' => $mallieData->plan,
                    'shop_name' => '',
                    'switching_scheduled_date' => $mallieData->switching_scheduled_date,
                ]);
            }

            // Mallieの情報更新
            $customerOrdered->login_id = $customerOrdered->code;
            $customerOrdered->updater = 'anyone';
            $customerOrdered->save();
        }

        // ログインID、パスワード確認のURLをSMSで送信
        $mobile_phone = str_replace('-', '', $customerOrdered->mobile_phone);
        $sms_responses = SmsSender::LoginInfoSmsSend($mobile_phone, $customerOrdered->code);
        if ($sms_responses['http_status'] != '200' || $sms_responses['status'] != 1) {
            Log::error($sms_responses['response_json']);
            DB::rollback();
            throw new Exception('SMS送信失敗');
        }
        return DB::commit();
    }

}
