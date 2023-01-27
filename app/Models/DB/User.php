<?php

namespace App\Models\DB;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use App\Models\DB\BaseModel;
use Illuminate\Support\Facades\Log;


// class User extends BaseModel
class User  extends Authenticatable implements MustVerifyEmail
{
    use SoftDeletes;

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    // protected $table = 'users';
    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token', 'password_reminder'
    ];

    /**
     * モデルのタイムスタンプを更新するかの指示
     *
     * @var bool
     */
    public $timestamps = false;


    const ROLE_LIST = [
        1 => 'システム管理者',
        2 => '主催者',
        3 => 'SA',
        9 => '一般',
    ];


    /**
     * 顧客コードから顧客データを取得
     * @param string $code 顧客コード
     * @return bool
     */
    public static function getByCustomerCode($code) {
        return self::where('customer_code', $code)
            ->whereNull('deleted_at')
            ->first();
    }

    /**
     * contractをwithで使用する
     */
    public function contract()
    {
        return $this->hasMany('App\Models\DB\Contract', 'customer_code', 'customer_code');
    }

    /**
     * パスワード変更
     * @param int $userId
     * @param string $newPassword
     * @return bool
     */
    public static function changePassword($userId, $newPassword) {
        try {
            $User = self::find($userId);
            if (empty($User->email)) {
                throw new EmailNotSetException;
            }
            $User->password = bcrypt($newPassword);
            $User->updated_user_id = BaseModel::getOperatorId();
            return $User->save();
        } catch (\PDOException $e) {
            Log::error($e);
            return false;
        }
    }

    /**
     * メールアドレス変更
     * @param int $userId
     * @param string $newEmail
     * @return bool
     */
    public static function changeEmail($userId, $newEmail) {
        try {
            $User = self::find($userId);
            if (empty($User)) {
                Log::error('ユーザが見つかりませんでした');
                return false;
            }
            $User->email = $newEmail;
            $User->updated_user_id = BaseModel::getOperatorId();
            return $User->save();
        } catch (\PDOException $e) {
            Log::error($e);
            return false;
        }
    }


    /**
     * 通知用メールアドレス変更
     * @param int $userId
     * @param string $notificationEmail
     * @return bool
     */
    public static function changeNotificationEmail($userId, $notificationEmail) {
        try {
            $User = self::find($userId);
            if (empty($User)) {
                Log::error('ユーザが見つかりませんでした');
                return false;
            }
            $User->notification_email = $notificationEmail;
            $User->updated_user_id = BaseModel::getOperatorId();
            return $User->save();
        } catch (\PDOException $e) {
            Log::error($e);
            return false;
        }
    }


    /**
     * 新規ユーザ登録
     * @param array $params
     * @return bool
     */
    public static function create($params) {
        $user = new self();
        $user->name = $params['name'];
        $user->email = $params['email'];
        $user->password = Hash::make($params['password']);
        $user->customer_code = $params['customer_code'];
        if ($params['zip_code']) $user->zip_code = $params['zip_code'];
        if ($params['phone']) $user->phone = $params['phone'];
        $user->created_user_id = BaseModel::getOperatorId();
        return $user->save();
    }

}
