<?php
/**
 * 雑多コンフィグ
 * 
 * ex. https://qiita.com/qwe001/items/96a83fadcfaeb3cd4a6e   local / testing / staging / production
 */
return [
    /**
     * メールアドレス表示等を適切にする
     */
    'MailBaseAddress' => env('MAIL_BASE_ADDRESS', 'example.com'),
    'MailExampleAddress' => env('MAIL_EXAMPLE_ADDRESS', 'info@example.com'),
    
    /**
     * お問合せ先を設定しログイン後のホーム画面からリンクを導く。  
     */
    'ContactInformationURL' => env('CONTACT_INFORMATION_URL',''),
    
    /**
     * 下記における ****_2 は副データベースにつながっている場合の表示についてを指します
     */
    /** タイトル名 htmlタグなどで使われるタイトル */
    'TitleName'   => env('TITLE_NAME',   'グランデータマイページ'),

    /** サービス名 テンプレート内で使われるサービス名 */
    'ServiceName'   => env('SERVICE_NAME',   'でんき'),

    /** タイトルロゴ画像 ファイルパスを示す */
    'TitleLogo'  => env('TITLE_LOGO'  , 'img/common/logo.png'),

    /** 表示テーマ -> ViewSwitchMiddleware 追加読み出し対象を 読み込み順に追加するために"連想配列"に追加する */
    'ViewThame'  => [ env('VIEW_THAME'  , null) ], // でんきスキン

    /** DB配置種別  single / multi_master / multi_slave   */
    'DBPlacement'    =>  env('DB_PLACEMENT', 'single') , // 標準はシングル構成

    /** Mallie支払い方法登録URL */
    'ProdPaymentMethodRegistURL' => 'https://entry.grandata-service.jp/entry_toss/payment_select.php', //本番
    'DevPaymentMethodRegistURL' => 'https://entry-test.grandata-service.jp/entry_toss/payment_select.php', //開発
    /** Mallie支払い方法変更URL */
    'ProdPaymentMethodModifyURL' => 'https://entry.grandata-service.jp/entry_toss/skip_agreement_payment_select.php', //本番
    'DevPaymentMethodModifyURL' => 'https://entry-test.grandata-service.jp/entry_toss/skip_agreement_payment_select.php', //開発
    /** Mobile支払い方法変更URL */
    'ProdPaymentMethodModifyMobileURL' => 'https://entry.grandata-service.jp/payment/mobile/credit_card_form_change', //本番
    'DevPaymentMethodModifyMobileURL' => 'https://entry-test.grandata-service.jp/payment/mobile/credit_card_form_change', //開発

    /** Mobile配送日時変更URL */
    'ProdDeliveryDateMobileURL' => 'https://entry.grandata-service.jp/payment/mobile/delivery', //本番
    'DevDeliveryDateMobileURL' => 'https://entry-test.grandata-service.jp/payment/mobile/delivery', //開発

    /** ログイン情報URL */
    'ProdLoginInfoURL' => 'https://entry.grandata-service.jp/entry_toss/mypage_login_information.php', //本番
    'DevLoginInfoURL' => 'https://entry-test.grandata-service.jp/entry_toss/mypage_login_information.php', //開発
    
];
