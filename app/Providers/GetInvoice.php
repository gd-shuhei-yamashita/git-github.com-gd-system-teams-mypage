<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * サービスプロバイダー
 * ex. LaravelのFacade（ファサード）でオリジナルの処理クラスを定義する入門編  
 * https://www.ritolab.com/entry/88
 */
class GetInvoiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->bind(
            'getinvoice',
            'App\Http\Components\GetInvoice'
        );        
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
