<?php

namespace App\Consts;

/**
 * MallieDB PaymentOrdered(決済・請求書送付先(申込))の定数クラス
 */
class PaymentOrderedConsts
{
    // payment_type区分
    public const PAYMENT_TYPE_UNREGISTERED = 0;
    public const PAYMENT_TYPE_CONVENIENCE_STORE = 11;
    public const PAYMENT_TYPE_COUNTER = 12;
    public const PAYMENT_TYPE_CONVENIENCE_STORE_UNREGISTERED = 13;
    public const PAYMENT_TYPE_CONVENIENCE_STORE_REVERSAL_NG = 14;
    public const PAYMENT_TYPE_ACCOUNT_TRANSFER = 21;
    public const PAYMENT_TYPE_ACCOUNT_TRANSFER_WEB = 22;
    public const PAYMENT_TYPE_JPBANK_TRANSFER = 23;
    public const PAYMENT_TYPE_JPBANK_TRANSFER_WEB = 24;
    public const PAYMENT_TYPE_CREDIT = 31;
    public const PAYMENT_TYPE_HI_BIT = 41;
    public const PAYMENT_TYPE_ENEPLA = 44;
    public const PAYMENT_TYPE_HI_HO = 45;
    public const PAYMENT_TYPE_SMAMOBI = 46;
    public const PAYMENT_TYPE_LIST = [
        self::PAYMENT_TYPE_UNREGISTERED => '未登録',
        self::PAYMENT_TYPE_CONVENIENCE_STORE => 'コンビニ請求書',
        self::PAYMENT_TYPE_COUNTER => '窓口払い',
        self::PAYMENT_TYPE_CONVENIENCE_STORE_UNREGISTERED => 'コンビニ請求（支払未登録）',
        self::PAYMENT_TYPE_CONVENIENCE_STORE_REVERSAL_NG => 'コンビニ請求（洗替NG）',
        self::PAYMENT_TYPE_ACCOUNT_TRANSFER => '口座振替',
        self::PAYMENT_TYPE_ACCOUNT_TRANSFER_WEB => '口座振替(Web)',
        self::PAYMENT_TYPE_JPBANK_TRANSFER => 'ゆうちょ自振',
        self::PAYMENT_TYPE_JPBANK_TRANSFER_WEB => 'ゆうちょ自振(Web)',
        self::PAYMENT_TYPE_CREDIT => 'クレジットカード',
        self::PAYMENT_TYPE_HI_BIT => 'Hi-bit合算',
        self::PAYMENT_TYPE_ENEPLA => 'エナプラ合算',
        self::PAYMENT_TYPE_HI_HO => 'hi-ho合算',
        self::PAYMENT_TYPE_SMAMOBI => 'スマモバ合算'
    ];

    public const PAYMENT_TYPE_MSG_LIST = [
        self::PAYMENT_TYPE_UNREGISTERED => '',
        self::PAYMENT_TYPE_CONVENIENCE_STORE => '払込兼コンビニ請求書に記載の支払期限に準拠',
        self::PAYMENT_TYPE_COUNTER => '毎月26日（非営業日の場合は翌営業日）',
        self::PAYMENT_TYPE_CONVENIENCE_STORE_UNREGISTERED => '払込兼コンビニ請求書に記載の支払期限に準拠',
        self::PAYMENT_TYPE_CONVENIENCE_STORE_REVERSAL_NG => '',
        self::PAYMENT_TYPE_ACCOUNT_TRANSFER => '毎月26日（非営業日の場合は翌営業日）',
        self::PAYMENT_TYPE_ACCOUNT_TRANSFER_WEB => '毎月26日（非営業日の場合は翌営業日）',
        self::PAYMENT_TYPE_JPBANK_TRANSFER => '毎月26日（非営業日の場合は翌営業日）',
        self::PAYMENT_TYPE_JPBANK_TRANSFER_WEB => '毎月26日（非営業日の場合は翌営業日）',
        self::PAYMENT_TYPE_CREDIT => 'クレジットカード会社に準拠',
        self::PAYMENT_TYPE_HI_BIT => '',
        self::PAYMENT_TYPE_ENEPLA => '',
        self::PAYMENT_TYPE_HI_HO => '',
        self::PAYMENT_TYPE_SMAMOBI => ''
    ];

    public const PAYMENT_TYPE_THANKYOU_LETTER_LIST = [
        self::PAYMENT_TYPE_UNREGISTERED => '未設定',
        self::PAYMENT_TYPE_CONVENIENCE_STORE => '払込兼コンビニ請求書',
        self::PAYMENT_TYPE_COUNTER => '窓口払い',
        self::PAYMENT_TYPE_CONVENIENCE_STORE_UNREGISTERED => '払込兼コンビニ請求書（支払方法未登録のため）',
        self::PAYMENT_TYPE_CONVENIENCE_STORE_REVERSAL_NG => 'コンビニ請求',
        self::PAYMENT_TYPE_ACCOUNT_TRANSFER => '口座振替',
        self::PAYMENT_TYPE_ACCOUNT_TRANSFER_WEB => '口座振替',
        self::PAYMENT_TYPE_JPBANK_TRANSFER => 'ゆうちょ銀行自動払込み（口座振替）',
        self::PAYMENT_TYPE_JPBANK_TRANSFER_WEB => 'ゆうちょ銀行自動払込み（口座振替）',
        self::PAYMENT_TYPE_CREDIT => 'クレジットカード',
        self::PAYMENT_TYPE_HI_BIT => 'Hi-bit合算',
        self::PAYMENT_TYPE_ENEPLA => 'エナプラ合算',
        self::PAYMENT_TYPE_HI_HO => 'hi-ho合算',
        self::PAYMENT_TYPE_SMAMOBI => 'スマモバ合算'
    ];
}
