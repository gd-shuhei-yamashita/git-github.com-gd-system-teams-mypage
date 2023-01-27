<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ex. LaravelのGate(ゲート)機能で権限(ロール)によるアクセス制限を実装する  
// https://www.ritolab.com/entry/56

/*
|--------------------------------------------------------------------------
| 共通
|--------------------------------------------------------------------------
*/

// 指定なし（マイページログイン）
Route::get('/'                            ,'AuthController@login');
Route::post('/'                           ,'AuthController@login');

Route::get('/login'                       ,'AuthController@login')->name('login');

// ログイン後処理
Route::get('/redirect_url'                ,'AuthController@redirect_url');
Route::post('/redirect_url'               ,'AuthController@redirect_url');

// ログアウト（管理）
Route::get('/logout'                      ,'AuthController@logout')->name('logout');

// パスワード登録
Route::get('/password_init'                ,'AuthController@password_init')->name('password_init');
Route::get('/password_init/{cid?}'         ,'AuthController@password_init');
Route::post('/password_init_change'        ,'AuthController@password_init_change')->name('password_init_change');
// SubDBのアクセス版をURLを別に作る
Route::get('/password_init2'                ,'AuthController@password_init')->name('password_init2');
Route::get('/password_init2/{cid?}'         ,'AuthController@password_init');
Route::post('/password_init2_change'        ,'AuthController@password_init_change')->name('password_init2_change');

// 初回メール確認 Laravel5.7 Verify を　Middlewareのみ流用
// ex. Laravel5.7のEmail Verificationを読む  
// https://qiita.com/yamaji_daisuke/items/731868a4de6037794976  
// phase1 初回email登録 (24hが待ち時間上限)  
Route::get('email/first',  'Auth\VerificationController@show')->name('verification.notice');
Route::post('email/first', 'Auth\VerificationController@update')->name('verification.notice.update');

// phase2 初回パスワード変更  
// 初回メール確認 email_reminder  
Route::get('email/email_reminder',  'AuthController@email_reminder')->name('verification.email_reminder');
Route::post('email/email_reminder', 'AuthController@email_reminder_update')->name('verification.email_reminder.update');
// SubDBのアクセス版をURLを別に作る
Route::get('email2/email_reminder',  'AuthController@email_reminder')->name('verification.email2_reminder');
Route::post('email2/email_reminder', 'AuthController@email_reminder_update')->name('verification.email2_reminder.update');

// phase3 設定完了
Route::get('email/complete',  'AuthController@email_complete')->name('verification.email_complete');
// SubDBのアクセス版をURLを別に作る
Route::get('email2/complete',  'AuthController@email_complete')->name('verification.email2_complete');

//約款情報
Route::get('/contract_information', 'ContractInformationController@index')->name('contract_information');


/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
*/

// 全ユーザ
Route::group(['middleware' => ['auth', 'can:user-higher']], function () {
    // 0:0 ホーム画面  Home
    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('/home', 'HomeController@getlist')->name('home_getlist');
    Route::post('/home/billing_amount', 'HomeController@billing_amount')->name('home_billing_amount');
    Route::post('/home/billing_range', 'HomeController@billing_range')->name('home_billing_range');

    // 0:1 パスワード変更     PasswordChange
    Route::get('/password_change', 'PasswordChangeController@create')->name('password_change');
    Route::post('/password_change', 'PasswordChangeController@store')->name('password_change_post');

    // 0:2 メールアドレス変更 (管理者以上では表示しない)  ChangeEmailAddress
    Route::get('/change_email_address', 'ChangeEmailAddressController@index')->name('change_email_address');
    Route::post('/change_email_address', 'ChangeEmailAddressController@update')->name('change_email_address_post');
    
    // 0:3 親子関係の一覧 (ここから親子関係で一連の操作可能に)
    Route::get('/parent_child', 'ParentChildController@index')->name('parent_child');
    Route::get('/parent_child/users_peek', 'ParentChildController@users_peek')->name('parent_child_users_peek');
    Route::get('/parent_child/peek_logout', 'ParentChildController@peek_logout')->name('parent_child_peek_logout');

    // 3:0 請求金額・使用量状況の確認    ConfirmUsagedata
    Route::get('/confirm_usagedata', 'ConfirmUsagedataController@index')->name('confirm_usagedata');
    //     請求金額・使用量状況の確認：1 使用場所一覧取得プルダウン
    Route::post('/confirm_usagedata/pulldown'          ,'ConfirmUsagedataController@pulldown')->name('confirm_usagedata_pulldown');
    //     請求金額・使用量状況の確認：2 対象年月日プルダウン
    Route::post('/confirm_usagedata/billings_pulldown' ,'ConfirmUsagedataController@billings_pulldown')->name('confirm_usagedata_billings_pulldown');
    //     請求金額・使用量状況の確認：3 電力情報の取得  
    Route::post('/confirm_usagedata/billings_getlist' ,'ConfirmUsagedataController@billings_getlist')->name('confirm_usagedata_billings_getlist');
    //     請求金額・使用量状況の確認：4 CSV/XLS 一括出力  
    Route::post('/confirm_usagedata/export_chart' ,'ConfirmUsagedataController@export_chart')->name('confirm_usagedata_export_chart');
    //     請求金額・使用量状況の確認：5 CSV/XLS 一括出力  
    Route::post('/confirm_usagedata/export_chart_original' ,'ConfirmUsagedataController@export_chart_original')->name('confirm_usagedata_export_chart_original');

    //     請求金額・使用量状況の確認    ConfirmUsagedataDetail
    Route::get('/confirm_usagedata/detail', 'ConfirmUsagedataController@detail')->name('confirm_usagedata_detail');

    // 3:1 契約情報の確認    ConfirmApplicationInformation
    Route::get('/confirm_application_information', 'ConfirmApplicationInformationController@index')->name('confirm_application_information');

    //契約更新のお知らせ
    Route::get('/contract_renewal', 'ContractRenewalController@index')->name('contract_renewal');
    Route::get('/contract_renewal/{supplypoint_code}', 'ContractRenewalController@detail')->name('contract_renewal_detail');

    // サンキューレター
    Route::get('/contract_notice', 'ContractNoticeController@index')->name('contract_notice');
    Route::get('/thankyou_letter/{supplypoint_code}', 'ContractNoticeController@output_thankyou_letter')->name('thankyou_letter');

    //オプション解約
    Route::get('/option_close/{option_contract_id}', 'OptionPlanCloseController@index')->name('option_close');
    Route::post('/option_close', 'OptionPlanCloseController@confirm')->name('option_close_confirm');

    //支払い方法の変更
    Route::get('/payment_method', 'PaymentMethodController@index')->name('payment_method');
    Route::get('/payment_method_electric_gas', 'PaymentMethodController@electric_gas')->name('payment_method_electric_gas');
    Route::get('/payment_method_wifi', 'PaymentMethodController@wifi')->name('payment_method_wifi');

    //契約解約
    Route::get('/contract_close', 'ContractCloseController@index')->name('contract_close');
    Route::get('/contract_close_confirm', 'ContractCloseController@confirm')->name('contract_close_confirm_validate');
    Route::post('/contract_close_confirm', 'ContractCloseController@confirm')->name('contract_close_confirm');
    Route::post('/contract_close_thanks', 'ContractCloseController@thanks')->name('contract_close_thanks');

    // 支払い状況
    Route::get('/payment_status', 'PaymentStatusController@index')->name('payment_status');
    Route::get('/payment_status/detail', 'PaymentStatusController@detail')->name('payment_status_detail');
    Route::post('/payment_status/billing_list', 'PaymentStatusController@billing_list')->name('payment_status_billing_list');
});


/*
|--------------------------------------------------------------------------
| 管理者
|--------------------------------------------------------------------------
*/

// 管理者以上
Route::group(['middleware' => ['auth', 'can:admin-higher']], function () {
    // db切り替え
    Route::get('admin/toggle_db', 'AdminToggleDBController@index')->name('toggle_db');

    // 1:3 申込情報取込(顧客D)      CaptureApplicationInformation
    Route::get('admin/capture_application_information', 'AdminCaptureApplicationInformationController@index')->name('capture_application_information');
    Route::post('admin/capture_application_information', 'AdminCaptureApplicationInformationController@registration')->name('capture_application_information_registration');    

    // 1:4 申込情報取込(契約D)      CaptureApplicationInformation2
    Route::get('admin/capture_application_information2', 'AdminCaptureApplicationInformation2Controller@index')->name('capture_application_information2');
    Route::post('admin/capture_application_information2', 'AdminCaptureApplicationInformation2Controller@registration')->name('capture_application_information2_registration2');    

    // 1:0 使用量データ取込  CaptureUsagedata
    Route::get('admin/capture_usagedata', 'AdminCaptureUsagedataController@index')->name('capture_usagedata');
    Route::post('admin/capture_usagedata', 'AdminCaptureUsagedataController@registration')->name('capture_usagedata_registration');

    // 1:1 請求データ取込    CaptureBillingdata
    Route::get('admin/capture_billingdata', 'AdminCaptureBillingdataController@index')->name('capture_billingdata');
    Route::post('admin/capture_billingdata', 'AdminCaptureBillingdataController@registration')->name('capture_billingdata_registration');

    // 1:2 内訳データ取込    CaptureItems
    Route::get('admin/capture_items', 'AdminCaptureItemsController@index')->name('capture_items');
    Route::post('admin/capture_items', 'AdminCaptureItemsController@registration')->name('capture_items_registration');

    // 2:0 顧客ID紐付変更    ChangeCustomerIDLinkage
    Route::get('admin/change_customer_id_linkage', 'AdminChangeCustomerIDLinkageController@index')->name('change_customer_id_linkage');
    Route::post('admin/change_customer_id_linkage', 'AdminChangeCustomerIDLinkageController@getlist')->name('change_customer_id_linkage_getlist');
    Route::post('admin/change_customer_id_linkage/store', 'AdminChangeCustomerIDLinkageController@store')->name('change_customer_id_linkage_store');

    // 2:1 供給地点特定番号紐付変更    ChangeSupplypointLinkage
    Route::get('admin/change_supplypoint_linkage', 'AdminChangeSupplypointLinkageController@index')->name('change_supplypoint_linkage');
    Route::post('admin/change_supplypoint_linkage', 'AdminChangeSupplypointLinkageController@getlist')->name('change_supplypoint_linkage_getlist');
    Route::post('admin/change_supplypoint_linkage/store', 'AdminChangeSupplypointLinkageController@store')->name('change_supplypoint_linkage_store');

    // 2:2 譲渡変更        ChangeTransfer
    Route::get('admin/change_transfer', 'AdminChangeTransferController@index')->name('change_transfer');
    Route::post('admin/change_transfer', 'AdminChangeTransferController@getlist')->name('change_transfer_getlist');
    Route::post('admin/change_transfer/store', 'AdminChangeTransferController@store')->name('change_transfer_store');
    Route::get('admin/change_transfer/delete', 'AdminChangeTransferController@delete')->name('change_transfer_delete');

    // 2:3 顧客ID統合      IntegrationCustomerID
    Route::get('admin/integration_customer_id', 'AdminIntegrationCustomerIDController@index')->name('integration_customer_id');
    Route::post('admin/integration_customer_id', 'AdminIntegrationCustomerIDController@getlist')->name('integration_customer_id_getlist');
    Route::post('admin/integration_customer_id/store', 'AdminIntegrationCustomerIDController@store')->name('integration_customer_id_store');
    Route::get('admin/integration_customer_id/delete', 'AdminIntegrationCustomerIDController@delete')->name('integration_customer_id_delete');

    // 2:5 申込情報変更    ChangeApplicationInfomarion
    Route::get('admin/change_application_infomarion', 'AdminChangeApplicationInfomarionController@index')->name('change_application_infomarion');
 
    // 2:6 申込情報検索    SearchApplicationInformation
    Route::get('admin/search_application_information', 'AdminSearchApplicationInformationController@index')->name('search_application_information');
    Route::post('admin/search_application_information', 'AdminSearchApplicationInformationController@users_getlist')->name('search_application_information_users_getlist');
    Route::get('admin/search_application_information/users_peek', 'AdminSearchApplicationInformationController@users_peek')->name('search_application_information_users_peek');
    Route::get('admin/search_application_information/peek_logout', 'AdminSearchApplicationInformationController@peek_logout')->name('search_application_information_peek_logout');
    Route::post('admin/search_application_information/store', 'AdminSearchApplicationInformationController@store')->name('search_application_information_store');

    // 2:7 操作履歴検索    SearchOperationHistory
    Route::get('admin/search_operation_history', 'AdminSearchOperationHistoryController@index')->name('search_operation_history');
    Route::post('admin/search_operation_history', 'AdminSearchOperationHistoryController@getlist')->name('search_operation_getlist');
    
    // 2:9 バッチ実行
    Route::get('admin/upload/menu', 'AdminBatchController@menu')->name('upload_menu');
    Route::get('admin/upload/usagedata', 'AdminBatchController@uploadusage')->name('upload_usagedata');
    Route::post('admin/upload/usagedata', 'AdminBatchController@storeusage')->name('store_usagedata');
    Route::get('admin/upload/billingdata', 'AdminBatchController@uploadbilling')->name('upload_billingdata');
    Route::post('admin/upload/billingdata', 'AdminBatchController@storebilling')->name('store_billingdata');
    Route::get('admin/upload/meisaidata', 'AdminBatchController@uploadmeisai')->name('upload_meisaidata');
    Route::post('admin/upload/meisaidata', 'AdminBatchController@storemeisai')->name('store_meisaidata');
    Route::get('admin/batch/usagedata', 'AdminBatchController@usagedata')->name('batch_usagedata');
    Route::get('admin/batch/billingdata', 'AdminBatchController@billingdata')->name('batch_billingdata');
    Route::get('admin/batch/meisaidata', 'AdminBatchController@meisaidata')->name('batch_meisaidata');
});

/*
|--------------------------------------------------------------------------
| システム管理者
|--------------------------------------------------------------------------
*/

// システム管理者のみ
Route::group(['middleware' => ['auth', 'can:system-only']], function () {
    // 2:4 管理者登録      RegistAdministrator
    Route::get('system/regist_administrator', 'SystemRegistAdministratorController@index')->name('regist_administrator');
    Route::post('system/regist_administrator/store', 'SystemRegistAdministratorController@store')->name('regist_administrator_store');

    // 2:8 お知らせ登録    RegistNotice
    Route::get('admin/regist_notice', 'AdminRegistNoticeController@index')->name('regist_notice');
    Route::post('admin/regist_notice', 'AdminRegistNoticeController@getlist')->name('regist_notice_getlist');
    Route::post('admin/regist_notice/store', 'AdminRegistNoticeController@store')->name('regist_notice_store');
    Route::get('admin/regist_notice/delete', 'AdminRegistNoticeController@delete')->name('regist_notice_delete');
    Route::post('admin/regist_notice/download', 'AdminRegistNoticeController@download')->name('regist_notice_download');

});


/*
|--------------------------------------------------------------------------
| 後ろに設置する
|--------------------------------------------------------------------------
*/

// ex. Laravel のAuth のroute 設定について  
// https://teratail.com/questions/106720
// ex. LaravelのAuth認証機能をカスタマイズし意図した挙動へ変更するTips
// https://www.ritolab.com/entry/86

// // Auth::routes(); を使わないで手動展開
//Auth::routes();
// Auth::routes() の呼び出しで展開されます。  
//Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login'); // Laravel標準
// Route::post('logout', 'Auth\LoginController@logout')->name('logout'); // Laravel標準

// Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
// Route::post('register', 'Auth\RegisterController@register');

// Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request'); // Laravel標準
// Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email'); // Laravel標準

Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset'); // Laravel標準
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
// ToDo:SubDBのアクセス版をURLを別に作る
// Route::get('password/reset2/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password2.reset'); // Laravel標準
// Route::post('password/reset2', 'Auth\ResetPasswordController@reset');

// APIから移動

// Route::resource('password_reminder'           , 'Api\PasswordReminderController');
Route::post('password_reminder'           , 'Api\PasswordReminderController@store')->name('password_reminder_store');
Route::post('password_reminder_addtional_auth', 'Api\PasswordReminderController@addtional_auth')->name('password_reminder_addtional_auth');




// ex. Laravelで独自の動的エラーページ（404/503 etc）を手早く作成する（多言語対応も）
// https://www.ritolab.com/entry/57
// DEBUG: HTTPステータスコードを引数に、該当するエラーページを表示させる
Route::get('error/{code}', function ($code) {
    if (array_search($code,array(0,400,401,403,404,419,500,503) ) != NULL) {
        abort($code);
    } else {
        abort(404);
    }
});


try {
    include_once 'web_renewal.php';
} catch (\Throwable $th) {
    // do not something
}