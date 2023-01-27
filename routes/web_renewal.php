<?php

// ログイン
Route::get('/' , 'Auth\LoginController@loginForm')->name('login');
Route::post('/', 'Auth\LoginController@loginForm');
Route::get('login', 'Auth\LoginController@loginForm');

Route::post('authenticate', 'Auth\LoginController@login')->name('authenticate');
Route::post('password_reminder_addtional_auth', 'Auth\PasswordReminderController@addtional_auth')->name('password_reminder_addtional_auth');


// ログイン後処理
Route::get('/redirect_url', 'AuthController@redirect_url');
Route::post('/redirect_url', 'AuthController@redirect_url');

// ログアウト（管理）
Route::get('/logout', 'AuthController@logout')->name('logout');

/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth', 'can:user-higher']], function () {
    // ホーム画面
    Route::get('home', 'User\HomeController@index')->name('home');
    Route::post('home/notices', 'User\HomeController@ajaxNotices')->name('home_notices');
    Route::post('home/billing_informations', 'User\HomeController@ajaxBillingInformations')->name('home_billing_informations');

    // モバイルwifi機器の配達日時変更
    Route::get('delivery_wifi', 'User\DeliveryController@wifi')->name('delivery_wifi');

    /* --- 使用量・請求金額  --- */
    Route::get('confirm_usagedata', 'User\UsageStatusController@index')->name('confirm_usagedata');
    // 使用場所一覧取得プルダウン
    Route::post('confirm_usagedata/pulldown' ,'User\UsageStatusController@pulldown')->name('confirm_usagedata_pulldown');
    // 請求金額一覧データ取得
    Route::post('confirm_usagedata/billing', 'User\UsageStatusController@monthly_billing')->name('confirm_usagedata_billing');
    // 請求一覧を一括出力
    Route::post('confirm_usagedata/export_chart' ,'User\UsageStatusController@export_chart')->name('confirm_usagedata_export_chart');
    // 利用期間を指定して一括出力
    Route::post('confirm_usagedata/export_chart_original' ,'User\UsageStatusController@export_chart_original')->name('confirm_usagedata_export_chart_original');

    // 請求金額詳細
    Route::get('confirm_usagedata/detail', 'User\UsageStatusController@detail')->name('confirm_usagedata_detail');

    // 明細PDF出力
    Route::get('/receipt_pdf/{supplypoint_code}/{date}', 'User\UsageStatusController@receipt')->name('receipt_pdf');
    Route::get('/specification_pdf/{supplypoint_code}/{date}', 'User\UsageStatusController@specification')->name('specification_pdf');
});


/*
|--------------------------------------------------------------------------
| 管理者
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth', 'can:admin-higher']], function () {
    // テストデータ
    Route::get('develop/entry', 'Admin\DevelopmentController@entry');
    Route::post('develop/entry', 'Admin\DevelopmentController@entry_complete');
});