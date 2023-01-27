<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // ex. LaravelのGate(ゲート)機能で権限(ロール)によるアクセス制限を実装する  
        // https://www.ritolab.com/entry/56

        // 開発者のみ許可
        Gate::define('system-only', function ($user) {
            return ($user->role == 1);
        });
        // 管理者以上（管理者＆システム管理者）に許可
        Gate::define('admin-higher', function ($user) {
            return ($user->role > 0 && $user->role <= 5);
        });
        // 一般ユーザ以上（つまり全権限）に許可
        Gate::define('user-higher', function ($user) {
            return ($user->role > 0 && $user->role <= 10);
        });        
    }
}
