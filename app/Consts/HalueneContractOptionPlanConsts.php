<?php

namespace App\Consts;

/**
 * MallieDB HalueneContractOptionPlan(契約オプション)の定数クラス
 */
class HalueneContractOptionPlanConsts
{
    // payment_type区分
    public const CLOSE_REASON_PRICE_HIGH = 1;
    public const CLOSE_REASON_TRANSFER = 2;
    public const CLOSE_REASON_DISSATISFACTION_SERVICE = 5;
    public const CLOSE_REASON_DISSATISFACTION_SUPPORT = 6;
    public const CLOSE_REASON_DIFFICULT_USE = 7;
    public const CLOSE_REASON_NOT_USE = 8;
    public const CLOSE_REASON_OTHER = 99;
    public const CLOSE_REASON_LIST = [
        self::CLOSE_REASON_PRICE_HIGH => '料金が高い',
        self::CLOSE_REASON_DISSATISFACTION_SERVICE => 'サービス内容に不満がある',
        self::CLOSE_REASON_DISSATISFACTION_SUPPORT => 'サポートに不満がある',
        self::CLOSE_REASON_DIFFICULT_USE => '利用方法がわかりにくい',
        self::CLOSE_REASON_NOT_USE => '利用しなくなった',
        self::CLOSE_REASON_TRANSFER => '他サービスへ乗り換えのため',
        self::CLOSE_REASON_OTHER => 'その他(理由をご記入ください)'
    ];
}
