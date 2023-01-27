<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 強制的にhttpsへの記述に改める
        // ex. https://stackoverflow.com/questions/24426423/laravel-generate-secure-https-url-from-route
        if (in_array( $this->app->environment() , array('production','staging'))) {
            //$this->app['request']->server->set('HTTPS', true );
            URL::forceScheme('https');
        }

        // ex. Laravel の Validation を正しく拡張する
        // https://qiita.com/moobay9/items/f1cdd3c8f995fdcf0963
        $this->app['validator']->resolver(function($translator, $data, $rules, $messages, $attributes) {
            return new \App\Services\CustomValidator($translator, $data, $rules, $messages, $attributes);
        });        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
