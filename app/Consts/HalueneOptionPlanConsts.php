<?php

namespace App\Consts;

/**
 * MallieDB HalueneOptionPlan(オプションプラン)の定数クラス
 */
class HalueneOptionPlanConsts
{
    // payment_type区分
    public const PAYMENT_TYPE_MONTHLY = 1;
    public const PAYMENT_TYPE_YEARLY = 2;
    public const PAYMENT_TYPE_LIST = [
        self::PAYMENT_TYPE_MONTHLY => '月額',
        self::PAYMENT_TYPE_YEARLY => '年額'
    ];

    //ID
    public const ID_DENKIRYO_OSHIRASE = 1;
    public const ID_DENKIRYO_OSHIRASE_FREE = 2;
    public const ID_TSUNAGARU_SYURI_SUPPORT_S = 3;
    public const ID_SMART_CINEMA_UNEXT_LITE_PLAN = 4;
    public const ID_TROUBLE_KAKETSUKE_SERVICE = 5;
    public const ID_OSAIFU_SUPPORT_ERABERUCLUB = 6;
    public const ID_TSUNAGARU_SYURI_SUPPORT_N = 7;
    public const ID_TSUNAGARU_SYURI_SUPPORT_S2 = 8;
    public const ID_UNEXT_SMART_CINEMA_MONTHLY_PLAN = 9;
    public const ID_UNEXT_SMART_CINEMA_POINT_PLAN_OLD = 10;
    public const ID_UNEXT_SMART_CINEMA_POINT_PLAN_NEW = 11;
    public const ID_TSUNAGARU_SYURI_SUPPORT_Z = 12;
    public const ID_MUSICJP_DOUBGA_HIMAWARIDENKIB_COURSE = 13;
    public const ID_ABEMA_PREMIUM = 14;
    public const ID_MUSICJP_MULTI_HIMAWARIDENKIB_COURSE = 15;
    public const ID_KURASHI_ANSHIN_SUPPORT_24 = 16;
    public const ID_KURASHI_ANSHIN_SUPPORT_24_HALF = 17;
    public const ID_KURASHI_ANSHIN_SUPPORT_24_INKURU = 18;
    public const ID_BAISYO_SEKININ_HOKEN = 19;
    public const ID_TSUNAGARU_SYURI_SUPPORT_M = 20;
    public const ID_P_SAPO = 21;
    public const ID_REPAIR_PASS = 22;
    public const ID_REPAIR_PASS_5 = 23;
    public const ID_TROUBLE_SOUDAN_SERVICE = 24;
    public const ID_OKAIMONO_YUTAI_SERVICE_L = 25;
    public const ID_PET_HEART = 26;
    public const ID_PET_HEART_HEART_PREMIUM = 27;
    public const ID_WIFI_SUEOKI_TEST = 28;
    public const ID_WIFI_MOBILE_TEST = 29;
    public const ID_KADEN_SYURI_SUPPORT = 30;
    public const ID_MOBILE_SYURI_SUPPORT = 31;
    public const ID_MOBILE_SYURI_SUPPORT_PULS = 32;
    public const ID_MUSICJP_DOUBGA_COURSE = 33;
    public const ID_MUSICJP_MANGA_COURSE = 34;

    // サンキューレター出力対象：料金欄
    public const TL_RYOKIN_DISPLAY_LIST = [
        self::ID_DENKIRYO_OSHIRASE,
        self::ID_DENKIRYO_OSHIRASE_FREE,
        self::ID_TROUBLE_KAKETSUKE_SERVICE,
        self::ID_OSAIFU_SUPPORT_ERABERUCLUB,
        self::ID_TSUNAGARU_SYURI_SUPPORT_Z,
        self::ID_KURASHI_ANSHIN_SUPPORT_24,
        self::ID_KURASHI_ANSHIN_SUPPORT_24_HALF,
        self::ID_TSUNAGARU_SYURI_SUPPORT_M,
        self::ID_P_SAPO,
        self::ID_REPAIR_PASS,
        self::ID_REPAIR_PASS_5,
        self::ID_TROUBLE_SOUDAN_SERVICE,
        self::ID_KADEN_SYURI_SUPPORT,
        self::ID_MOBILE_SYURI_SUPPORT,
        self::ID_MOBILE_SYURI_SUPPORT_PULS,
        self::ID_MUSICJP_DOUBGA_COURSE,
        self::ID_MUSICJP_MANGA_COURSE
    ];

    // サンキューレター出力対象：付帯サービス・オプションサービス欄
    public const TL_FUTAI_DISPLAY_LIST = [
        self::ID_TSUNAGARU_SYURI_SUPPORT_S,
        self::ID_SMART_CINEMA_UNEXT_LITE_PLAN,
        self::ID_TROUBLE_KAKETSUKE_SERVICE,
        self::ID_OSAIFU_SUPPORT_ERABERUCLUB,
        self::ID_TSUNAGARU_SYURI_SUPPORT_N,
        self::ID_TSUNAGARU_SYURI_SUPPORT_S2,
        self::ID_UNEXT_SMART_CINEMA_MONTHLY_PLAN,
        self::ID_UNEXT_SMART_CINEMA_POINT_PLAN_OLD,
        self::ID_UNEXT_SMART_CINEMA_POINT_PLAN_NEW,
        self::ID_TSUNAGARU_SYURI_SUPPORT_Z,
        self::ID_MUSICJP_DOUBGA_HIMAWARIDENKIB_COURSE,
        self::ID_ABEMA_PREMIUM,
        self::ID_KURASHI_ANSHIN_SUPPORT_24,
        self::ID_KURASHI_ANSHIN_SUPPORT_24_HALF,
        self::ID_KURASHI_ANSHIN_SUPPORT_24_INKURU,
        self::ID_BAISYO_SEKININ_HOKEN,
        self::ID_TSUNAGARU_SYURI_SUPPORT_M,
        self::ID_P_SAPO,
        self::ID_REPAIR_PASS,
        self::ID_REPAIR_PASS_5,
        self::ID_TROUBLE_SOUDAN_SERVICE,
        self::ID_PET_HEART,
        self::ID_PET_HEART_HEART_PREMIUM,
        self::ID_KADEN_SYURI_SUPPORT,
        self::ID_MOBILE_SYURI_SUPPORT,
        self::ID_MOBILE_SYURI_SUPPORT_PULS,
        self::ID_MUSICJP_DOUBGA_COURSE,
        self::ID_MUSICJP_MANGA_COURSE
    ];

}
