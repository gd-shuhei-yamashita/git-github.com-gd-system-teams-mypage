<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
//use App\Notifications\CustomPasswordReset;
use Illuminate\Support\Facades\Log;

class UserNow extends Authenticatable implements MustVerifyEmail
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
    ];

    /**
     * 増設 コンストラクタ
     */
    public function __construct(array $attributes = [])	{
        parent::__construct($attributes);
        if (session()->get('db_accesspoint_now', '0') == 2) {
            // 初期DBを定義 DB_DATABASE
            // $this->connection = config('database.connections.mysql2.database');
            $this->connection = 'mysql2';
            Log::debug( "db-database2:" . config('database.connections.mysql2.database') );
        }
    }

    /**
     * contractを引き出す
     */
    public function contract()
    {
        return $this->hasMany('App\Contract', 'customer_code', 'customer_code');
    }

}
