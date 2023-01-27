<?php
namespace App\Http\Controllers\Auth;

use Request;
use App\Mail\ReminderMail;
use App\Mail\TemporaryPasswordMail;
use App\Http\Controllers\CustomController;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


use Exception;
use RuntimeException;

// eloquent
use App\Models\DB\User;
use App\Models\DB\Contract;
use App\Models\Mallie\MallieModel;
use App\Models\Mallie\CustomerOrdered;

class PasswordReminderController extends CustomController {

    const STATUS_SUCESS = 'success';
    const STATUS_PENDING = 'pending';
    const STATUS_NOTFOUND = 'not_found';
    const STATUS_ERROR = 'error';
    const STATUS_MESSAGES = [
        self::STATUS_SUCESS   => 'ご入力いただいた携帯電話番号にSMSを送信いたしました。',
        self::STATUS_PENDING  => '対象の契約を絞り込めませんでした。',
        self::STATUS_NOTFOUND => 'ご本人様情報が確認できません。入力内容に誤りがあります。',
        self::STATUS_ERROR    => '認証SMSの送信に失敗しました。',
    ];

    /**
     * 追加認証(生年月日、携帯番号)
     * IDと初期化したパスワードをSMSで送信します。
     * @param Request $request
     * @return json
     */
    function addtional_auth(Request $request) {
        $phone_num = Request::input('phone_num');
        $mobile_phone = substr($phone_num, 0, 3) . '-' . substr($phone_num, 3, 4) . '-' . substr($phone_num, 7, 4);
        $birthday = Request::input('year') . '-' . Request::input('month') . '-' . Request::input('day');
        $status = '';
        if (!$phone_num || $birthday === '--') {
            $status = self::STATUS_NOTFOUND;
        } else {
            // Mallieチェック
            try {
                $mallieCustomers = CustomerOrdered::getForPasswordReminder($mobile_phone, $birthday);
                $usersCount = count($mallieCustomers);
                if ($usersCount === 1) {
                    MallieModel::createMypageData($mallieCustomers[0]);
                    $status = self::STATUS_SUCESS;
                } else if ($usersCount > 1) {
                    $status = self::STATUS_PENDING;
                } else {
                    $status = self::STATUS_NOTFOUND;
                }
            } catch (\PDOException $e) {
                Log::error('[ERROR] database connection failed :'.$this->_createlog($e));
                $status = self::STATUS_ERROR;
            } catch (Exception $e) {
                Log::error('[ERROR] password reminder :'.$this->_createlog($e));
                $status = self::STATUS_ERROR;
            }
        }

        return $this->customAjaxResponse([
            'status' => $status,
            'message' => self::STATUS_MESSAGES[$status]
        ]);
    }

    /**
     * ログ用の文字列を作成します
     * @param Exception $e
     * @return string
     */
    private function _createlog($e) {
        return $e->getFile().'['.$e->getLine().']|'.$e->getMessage();
    }


}
