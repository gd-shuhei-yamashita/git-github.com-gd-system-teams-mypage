-----------------
-- 1 ユーザー	Customer	users

-- 中間データ作成
drop table if exists sub_token;

CREATE TABLE `sub_token` (
  `seq` int(11) NOT NULL AUTO_INCREMENT,
  `token` text COLLATE utf8_unicode_ci NOT NULL,
  `newmailaddress` text COLLATE utf8_unicode_ci NOT NULL,
  `newpassword` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `login_id` text COLLATE utf8_unicode_ci NOT NULL,
  `limitedsecond` int(11) NOT NULL,
  `accessflag` int(11) NOT NULL DEFAULT '0',
  `createdatetime` datetime NOT NULL,
  `accessdatetime` datetime DEFAULT NULL,
  PRIMARY KEY (`seq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `sub_token` 
  select 
    seq,
    token  ,
    newmailaddress ,
    newpassword ,
    login_id ,
    limitedsecond ,
    accessflag , 
    createdatetime  ,
    accessdatetime  
  from 
    (
        select 
            seq,
            token  ,
            newmailaddress ,
            newpassword ,
            login_id ,
            limitedsecond ,
            accessflag , 
            createdatetime  ,
            accessdatetime 
        from 
            `Token`
        where
            accessflag = 1
    ) as sub2_token
  group by 
    login_id
  having 
    max(createdatetime) = createdatetime
  ;


drop table if exists sub_users; 

CREATE TABLE `sub_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'お客様名',
  `email` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'email',
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'パスワード',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Laravel5 ユーザーログイン維持用',
  `role` tinyint(4) NOT NULL DEFAULT '9' COMMENT 'ユーザ区分:1:システム管理者\r\n2:主催者\r\n3:SA\r\n9:一般',
  `demouser` tinyint(4) NOT NULL DEFAULT '9' COMMENT 'データ区分:1:検証用ユーザ\r\n9:一般',
  `customer_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'お客様番号',
  `zip_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '郵便番号',
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ご連絡先電話番号',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成時刻',
  `created_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '作成ユーザID',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時刻',
  `updated_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '変更ユーザID',
  `email_verified_at` timestamp NULL DEFAULT NULL COMMENT 'email 初回確認日時',
  `password_verified_at` timestamp NULL DEFAULT NULL COMMENT 'パスワード 初回確認日時',
  `reminder_expired_at` timestamp NULL DEFAULT NULL COMMENT 'リマインダー有効期限',
  `password_reminder` varchar(70) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'パスワードリマインダー確認用',
  `email_new` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'email_new',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `users_customer_code_email_id_created_at_index` (`customer_code`,`email`,`id`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ユーザー';

INSERT INTO `sub_users` (
  `name`,
  `email`,
  `password`,
  `customer_code`,
  `zip_code`,
  `phone`,
  `created_at`,
  `created_user_id`,
  `updated_at`,
  `updated_user_id`
)
  select 
    `name`          as `name` , 
    `mailaddress`   as `email` ,
    `loginpassword` as `password` ,
    `code`     as `customer_code` ,
    `zip_code` as `zip_code` ,
    `phone`    as `phone` ,
    `createdate`   as `created_at` ,
    'ADMN000001' as created_user_id ,
    upddate as updated_at,
    'ADMN000001' as updated_user_id 
  from 
      `Customer`
  group by 
    customer_code
  having 
    max(createdate) = createdate
  ;

drop table if exists new_users; 

CREATE TABLE `new_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'お客様名',
  `email` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'email',
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'パスワード',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Laravel5 ユーザーログイン維持用',
  `role` tinyint(4) NOT NULL DEFAULT '9' COMMENT 'ユーザ区分:1:システム管理者\r\n2:主催者\r\n3:SA\r\n9:一般',
  `demouser` tinyint(4) NOT NULL DEFAULT '9' COMMENT 'データ区分:1:検証用ユーザ\r\n9:一般',
  `customer_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'お客様番号',
  `zip_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '郵便番号',
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ご連絡先電話番号',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成時刻',
  `created_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '作成ユーザID',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時刻',
  `updated_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '変更ユーザID',
  `email_verified_at` timestamp NULL DEFAULT NULL COMMENT 'email 初回確認日時',
  `password_verified_at` timestamp NULL DEFAULT NULL COMMENT 'パスワード 初回確認日時',
  `reminder_expired_at` timestamp NULL DEFAULT NULL COMMENT 'リマインダー有効期限',
  `password_reminder` varchar(70) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'パスワードリマインダー確認用',
  `email_new` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'email_new',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_customer_code_unique` (`customer_code`),
  KEY `users_customer_code_email_id_created_at_index` (`customer_code`,`email`,`id`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ユーザー';

-- データ集約
INSERT INTO `new_users` (
  `name`,
  `email`,
  `password`,
  `customer_code`,
  `zip_code`,
  `phone`,
  `created_at`,
  `created_user_id`,
  `updated_at`,
  `updated_user_id`,
  `email_verified_at`,
  `password_verified_at`
)
  select
    c.name as name,
    c.email as email,
    c.password as password,

    c.customer_code as customer_code,
    c.zip_code as zip_code,
    c.phone as phone,
    c.created_at as created_at,
    c.created_user_id as created_user_id,
    c.updated_at as updated_at,
    c.updated_user_id as updated_user_id,

    (CASE WHEN sub_token.seq THEN 1 ELSE 0 END )  as email_verified_at,
    (CASE WHEN sub_token.seq THEN 1 ELSE 0 END )  as password_verified_at
   
  from sub_users as c 
  left join 
    sub_token
    on sub_token.login_id = c.customer_code;



-----------------
-- 2 請求データ	Billing	billing
drop table if exists sub_billing; 

CREATE TABLE `sub_billing` (
  `seq` int(11) NOT NULL ,
  `supplypoint_code` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '供給地点特定番号',
  `customer_code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'お客様番号',
  `billing_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '請求番号',
  `itemize_code` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内訳コード',
  `start_date` date NOT NULL COMMENT '利用開始年月日',
  `end_date` date NOT NULL COMMENT '利用終了年月日',
  `billing_date` int(11) NOT NULL COMMENT '請求年月',
  `billing_amount` int(11) NOT NULL COMMENT '請求額',
  `tax` int(11) NOT NULL COMMENT '消費税相当額',
  `payment_type` smallint(6) NOT NULL COMMENT '支払い種別(1:口座振替、2:クレジットカード、3:コンビニ払い 4:銀行窓口)',
  `power_percentage` text COLLATE utf8mb4_unicode_ci COMMENT '力率',
  `metering_date` date NOT NULL COMMENT '検針月日',
  `next_metering_date` date  COMMENT '次回検針予定日',
  `main_indicator` decimal(8,1) NOT NULL COMMENT '当月指示数',
  `main_indicator_last_month` decimal(8,1) NOT NULL COMMENT '前月指示数',
  `meter_multiply` int(11) DEFAULT NULL COMMENT '計器乗率',
  `difference` decimal(8,1) NOT NULL COMMENT '差引',
  `payment_date` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '当月お支払い予定日',
  `usage_date` int(11) NOT NULL COMMENT '利用年月',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成時刻',
  `created_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '作成ユーザID',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時刻',
  `updated_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '変更ユーザID',
  PRIMARY KEY (`supplypoint_code`,`customer_code`,`billing_code`,`itemize_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='請求データ';


INSERT INTO `sub_billing` 
  select 
    seq,
    supplypoint_code    as supplypoint_code, 
    customer_code   as customer_code,
    billing_code   as billing_code,
    itemize_code   as itemize_code,
    startdate   as start_date,
    enddate   as end_date,
    billing_date   as billing_date,
    billing_amount   as billing_amount,
    tax   as tax,
    payment_type   as payment_type,
    power_percentage   as power_percentage,
    metering_date   as metering_date,
    (CASE WHEN next_metering_date = 0 THEN null ELSE next_metering_date END )   as next_metering_date,
    main_indicator   as main_indicator,
    main_indicator_last_month   as main_indicator_last_month,
    meter_multiply   as meter_multiply,
    difference   as difference,
    payment_date   as payment_date,
    (CASE WHEN usage_date = 0 THEN null ELSE usage_date END )   as usage_date,
    now() as created_at,
    'ADMN000001' as created_user_id,
    now() as updated_at,
    'ADMN000001' as updated_user_id
  from 
    `Billing`
  group by 
    supplypoint_code, customer_code, billing_code, itemize_code 
  having 
    max(seq) = seq
  ;

drop table if exists new_billing; 

CREATE TABLE `new_billing` (
  `supplypoint_code` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '供給地点特定番号',
  `customer_code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'お客様番号',
  `billing_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '請求番号',
  `itemize_code` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内訳コード',
  `start_date` date NOT NULL COMMENT '利用開始年月日',
  `end_date` date NOT NULL COMMENT '利用終了年月日',
  `billing_date` int(11) NOT NULL COMMENT '請求年月',
  `billing_amount` int(11) NOT NULL COMMENT '請求額',
  `tax` int(11) NOT NULL COMMENT '消費税相当額',
  `payment_type` smallint(6) NOT NULL COMMENT '支払い種別(1:口座振替、2:クレジットカード、3:コンビニ払い 4:銀行窓口)',
  `power_percentage` text COLLATE utf8mb4_unicode_ci COMMENT '力率',
  `metering_date` date NOT NULL COMMENT '検針月日',
  `next_metering_date` date COMMENT '次回検針予定日',
  `main_indicator` decimal(8,1) NOT NULL COMMENT '当月指示数',
  `main_indicator_last_month` decimal(8,1) NOT NULL COMMENT '前月指示数',
  `meter_multiply` int(11) DEFAULT NULL COMMENT '計器乗率',
  `difference` decimal(8,1) NOT NULL COMMENT '差引',
  `payment_date` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '当月お支払い予定日',
  `usage_date` int(11) NOT NULL COMMENT '利用年月',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成時刻',
  `created_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '作成ユーザID',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時刻',
  `updated_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '変更ユーザID',
  PRIMARY KEY (`supplypoint_code`,`customer_code`,`billing_code`,`itemize_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='請求データ';


INSERT INTO `new_billing` 
  select 
    supplypoint_code    as supplypoint_code, 
    customer_code   as customer_code,
    billing_code   as billing_code,
    itemize_code   as itemize_code,
    start_date   as start_date,
    end_date   as end_date,
    billing_date   as billing_date,
    billing_amount   as billing_amount,
    tax   as tax,
    payment_type   as payment_type,
    power_percentage   as power_percentage,
    metering_date   as metering_date,
    next_metering_date   as next_metering_date,
    main_indicator   as main_indicator,
    main_indicator_last_month   as main_indicator_last_month,
    meter_multiply   as meter_multiply,
    difference   as difference,
    payment_date   as payment_date,
    usage_date   as usage_date,
    created_at as created_at,
    created_user_id as created_user_id,
    updated_at as updated_at,
    updated_user_id as updated_user_id
  from 
    `sub_billing`
  ;

-----------------
-- 3 内訳データ	Billing_itemize	billing_itemize

drop table if exists sub_billing_itemize; 

CREATE TABLE `sub_billing_itemize` (
  `seq` int(11) NOT NULL ,
  `billing_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '請求番号',
  `itemize_code` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内訳コード',
  `itemize_order` int(11) NOT NULL DEFAULT '0' COMMENT '明細表示順',
  `itemize_name` text COLLATE utf8mb4_unicode_ci COMMENT '内訳名',
  `itemize_bill` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '内訳金額',
  `note` text COLLATE utf8mb4_unicode_ci COMMENT 'ノート',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成時刻',
  `created_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '作成ユーザID',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時刻',
  `updated_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '変更ユーザID',
  PRIMARY KEY (`billing_code`,`itemize_code`,`itemize_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='内訳データ';

INSERT INTO `sub_billing_itemize` 
  select 
    seq,
    billing_code   as billing_code,
    itemize_code   as itemize_code,
    billing_seq_order  as itemize_order,
    itemize_name   as itemize_name,
    itemize_bill   as itemize_bill,
    biko as note,
    createdate as created_at,
    'ADMN000001' as created_user_id,
    now() as updated_at,
    'ADMN000001' as updated_user_id
  from 
    `Billing_itemize`
  group by 
    billing_code, itemize_code, itemize_order 
  having 
    max(seq) = seq
  ;



drop table if exists new_billing_itemize; 

CREATE TABLE `new_billing_itemize` (
  `billing_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '請求番号',
  `itemize_code` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '内訳コード',
  `itemize_order` int(11) NOT NULL DEFAULT '0' COMMENT '明細表示順',
  `itemize_name` text COLLATE utf8mb4_unicode_ci COMMENT '内訳名',
  `itemize_bill` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '内訳金額',
  `note` text COLLATE utf8mb4_unicode_ci COMMENT 'ノート',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成時刻',
  `created_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '作成ユーザID',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時刻',
  `updated_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '変更ユーザID',
  PRIMARY KEY (`billing_code`,`itemize_code`,`itemize_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='内訳データ';

INSERT INTO `new_billing_itemize` 
  select 
    billing_code   as billing_code,
    itemize_code   as itemize_code,
    itemize_order  as itemize_order,
    itemize_name   as itemize_name,
    itemize_bill   as itemize_bill,
    note as note,
    created_at as created_at,
    created_user_id as created_user_id,
    updated_at as updated_at,
    updated_user_id as updated_user_id
  from 
    `sub_billing_itemize`
  ;
  
-----------------
-- 4 譲渡データ	Jouto	assignment
-- 実際は対応不要  
drop table if exists new_assignment; 

CREATE TABLE `new_assignment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `supplypoint_code` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '供給地点特定番号',
  `assignment_before_customer_code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '譲渡元顧客ID',
  `assignment_after_customer_code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '譲渡後顧客ID',
  `assignment_after_contract_name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '譲渡後顧客',
  `assignment_after_address` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '譲渡後顧客アドレス',
  `assignment_after_plan` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '譲渡後顧客プラン',
  `assignment_date` date NOT NULL COMMENT '譲渡日',
  `before_customer_billing_end` int(11) NOT NULL COMMENT '顧客への請求後',
  `after_customer_billing_start` int(11) NOT NULL COMMENT '顧客への請求前',
  `type` tinyint(4) NOT NULL COMMENT '種別  ',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成時刻',
  `created_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '作成ユーザID',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時刻',
  `updated_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '変更ユーザID',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignment_id_supplypoint_code_assignment_date_index` (`id`,`supplypoint_code`,`assignment_date`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='譲渡データ';

INSERT INTO `new_assignment` 
  select 
    id as id,
    location as supplypoint_code ,
    jouto_before_customer_code as assignment_before_customer_code ,
    jouto_after_customer_code as assignment_after_customer_code ,
    jouto_after_contract_name as assignment_after_contract_name ,
    jouto_after_address as assignment_after_address ,
    jouto_after_plan as assignment_after_plan ,
    jouto_after_shop_name as assignment_shop_name ,
    jouto_date as assignment_date ,
    before_customer_billing_end as before_customer_billing_end ,
    after_customer_billing_start as after_customer_billing_start ,
    type as type ,
    jouto_date as created_at,
    'ADMN000001' as created_user_id,
    now() as updated_at,
    'ADMN000001' as updated_user_id
  from 
    `Jouto`
  ;

-----------------
-- 5 親子顧客関係	Parent_Child	parent_child
-- 実際は対応不要  
drop table if exists new_parent_child; 

CREATE TABLE `new_parent_child` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_customer_code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '親_顧客ID',
  `child_customer_code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '子_顧客ID',
  `change_result` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '変更詳細',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成時刻',
  `created_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '作成ユーザID',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時刻',
  `updated_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '変更ユーザID',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_child_id_parent_customer_code_child_customer_code_index` (`id`,`parent_customer_code`,`child_customer_code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='親子顧客関係';

-----------------
-- 6 使用率	UsageT	usage_t
drop table if exists sub_usage_t; 

CREATE TABLE `sub_usage_t` (
  `seq` int(11) NOT NULL ,
  `supplypoint_code` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '供給地点特定番号',
  `usage_date` int(11) NOT NULL COMMENT '利用年月',
  `customer_code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'お客様番号',
  `usage` int(11) NOT NULL COMMENT '使用量',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成時刻',
  `created_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '作成ユーザID',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時刻',
  `updated_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '変更ユーザID',
  PRIMARY KEY (`customer_code`,`supplypoint_code`,`usage_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='使用率';

INSERT INTO `sub_usage_t` 
  select 
    seq,
    supplypoint_code  as supplypoint_code ,
    useage_date       as usage_date ,
    customer_code     as customer_code ,
    useage            as `usage` ,
    (CASE WHEN createdate > "2000/1/1 00:00:00" THEN createdate ELSE now() END )        as created_at ,
    'ADMN000001' as created_user_id,
    now() as updated_at,
    'ADMN000001' as updated_user_id
  from 
    `UsageT`
  group by 
    supplypoint_code, usage_date, customer_code 
  having 
    max(seq) = seq
  ;

drop table if exists new_usage_t; 

CREATE TABLE `new_usage_t` (
  `supplypoint_code` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '供給地点特定番号',
  `usage_date` int(11) NOT NULL COMMENT '利用年月',
  `customer_code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'お客様番号',
  `usage` int(11) NOT NULL COMMENT '使用量',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成時刻',
  `created_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '作成ユーザID',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時刻',
  `updated_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '変更ユーザID',
  PRIMARY KEY (`customer_code`,`supplypoint_code`,`usage_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='使用率';

INSERT INTO `new_usage_t` 
  select 
    supplypoint_code  as supplypoint_code ,
    usage_date        as usage_date ,
    customer_code     as customer_code ,
    `usage`           as `usage` ,
    created_at        as created_at ,
    created_user_id  as created_user_id,
    updated_at       as updated_at,
    updated_user_id  as updated_user_id
  from 
    `sub_usage_t`;


-----------------
-- 7 作業履歴		operation_history
-- 対応不要  

-----------------
-- 8 契約データ	Contract	contract
drop table if exists sub_contract; 

CREATE TABLE `sub_contract` (
  `seq` int(11) NOT NULL ,
  `customer_code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'お客様番号',
  `supplypoint_code` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '供給地点特定番号',
  `contract_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '契約名義',
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '住所',
  `plan` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'プラン名',
  `shop_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '店舗名',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成時刻',
  `created_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '作成ユーザID',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時刻',
  `updated_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '変更ユーザID',
  PRIMARY KEY (`customer_code`,`supplypoint_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='契約データ';


INSERT INTO `sub_contract` 
  select 
    seq,
    customer_code as customer_code ,
    supplypoint_code as supplypoint_code ,
    `contract_name` as `contract_name` ,
    `address` as `address` ,
    plan as plan ,
    shop_name as shop_name ,
    createdate as created_at ,
    'ADMN000001' as created_user_id,
    now() as updated_at,
    'ADMN000001' as updated_user_id
  from 
    `Contract`
  group by 
    customer_code, supplypoint_code 
  having 
    max(seq) = seq
  ;



drop table if exists new_contract; 

CREATE TABLE `new_contract` (
  `customer_code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'お客様番号',
  `supplypoint_code` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '供給地点特定番号',
  `contract_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '契約名義',
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '住所',
  `plan` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'プラン名',
  `shop_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '店舗名',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成時刻',
  `created_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '作成ユーザID',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時刻',
  `updated_user_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'undefined' COMMENT '変更ユーザID',
  PRIMARY KEY (`customer_code`,`supplypoint_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='契約データ';

INSERT INTO `new_contract` 
  select 
    customer_code as customer_code ,
    supplypoint_code as supplypoint_code ,
    `contract_name` as `contract_name` ,
    `address` as `address` ,
    plan      as plan ,
    shop_name as shop_name ,
    created_at as created_at ,
    created_user_id as created_user_id,
    updated_at as updated_at,
    updated_user_id as updated_user_id
  from 
    `sub_contract`;

-----------------
-- 9 お知らせ		notice
-- 対応不要  

-----------------
-- 10 置換履歴		replacement_history
-- 対応不要  

-----------------

