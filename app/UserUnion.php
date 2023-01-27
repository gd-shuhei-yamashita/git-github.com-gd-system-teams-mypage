<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
//use App\Notifications\CustomPasswordReset;
use Illuminate\Support\Facades\Log;

class UserUnion extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use SoftDeletes;
    
    protected $table = 'users';
        
    // protected $appends = ['itemize'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'password_reminder'
    ];

    /**
     * 増設 コンストラクタ
     */
    public function __construct(array $attributes = [])	{
        parent::__construct($attributes);
        if (config('const.DBPlacement') == 'multi_master' ) {
            // 読み出し対象テーブルを定義
            $this->table = 'user_union';
            Log::debug( "DBPlacement:" . config('const.DBPlacement') );
        }
    }
    
    /**
     * contractを引き出す
     */
    public function contract()
    {
        return $this->hasMany('App\Contract', 'customer_code', 'customer_code');
    }

    /**
     * itemize_code 配列を返す
     */
    // public function itemize_list()
    // {
    //     $temp_billings = [];
    //     foreach ( $this->billing()->get() as $results2){
    //         // Log::debug("Results2:");
    //         // Log::debug( $results2 );
    //         $temp_billings[] = $results2["itemize_code"];
    //     }

    //     return $temp_billings;
    // }

    /**
     * 
     * ex. Laravelのモデル(Eloquent)にバーチャル(仮想/カスタム)フィールドを追加する($appends)
     * https://info.yama-lab.com/laravel%E3%81%AE%E3%83%A2%E3%83%87%E3%83%ABeloquent%E3%81%AB%E3%83%90%E3%83%BC%E3%83%81%E3%83%A3%E3%83%AB%E4%BB%AE%E6%83%B3-%E3%82%AB%E3%82%B9%E3%82%BF%E3%83%A0%E3%83%95%E3%82%A3%E3%83%BC%E3%83%AB/
     * 
     */
    // public function getItemizeAttribute()
    // {
    //     // return $this->itemize_list();
    //     $temp_billings = [];
    //     foreach ( $this->billing()->get() as $results2){
    //         // Log::debug("Results2:");
    //         // Log::debug( $results2 );
    //         $temp_billings[] = $results2["itemize_code"];
    //     }

    //     return $temp_billings;        
    // }
}
