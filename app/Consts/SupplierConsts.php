<?php

namespace App\Consts;

/**
 * サプライヤーの定数クラス
 */
class SupplierConsts
{
    public const SUPPLIER_TYPE_ELECTRIC = 1;
    public const SUPPLIER_TYPE_GAS = 2;

    public const SUPPLIER_TYPE_LIST = [
        self::SUPPLIER_TYPE_ELECTRIC => '小売電気事業者',
        self::SUPPLIER_TYPE_GAS => 'ガス小売事業者',
    ];

    public const GRANDATA_ELECTRIC_INDEX = '1';
    public const GRANDATA_GAS_INDEX = '2';
    public const SAISAN_GAS_INDEX = '3';
    public const FAMILY_NET_JAPAN_GAS_INDEX = '4';
    public const HTB_ENERGY_ELECTRIC_INDEX = '5';

    public const GRANDATA_ELECTRIC = 'A0476';
    public const GRANDATA_GAS = 'A0087';
    public const SAISAN_GAS = 'A0023';
    public const FAMILY_NET_JAPAN_GAS = 'A0058';
    public const HTB_ENERGY_ELECTRIC = 'A0172';

    public const SUPPLIER_TYPE_INDEX = 0;
    public const NAME_INDEX = 1;
    public const ZIP_CODE_INDEX = 2;
    public const ADDRESS_INDEX = 3;
    public const PHONE_INDEX = 4;
    public const RECEPTION_TIME_INDEX = 5;
    public const PRIVACY_POLICY_INDEX = 6;

    public const SUPPLIER_LIST = [
        self::GRANDATA_ELECTRIC => [self::SUPPLIER_TYPE_ELECTRIC, '株式会社グランデータ', '171-0022', '東京都豊島区南池袋2丁目9−9', '0570-070-336', '10:00～18:00（定休日：年末年始）', 'https://grandata.jp/privacy/'],
        self::GRANDATA_GAS => [self::SUPPLIER_TYPE_GAS, '株式会社グランデータ', '171-0022', '東京都豊島区南池袋2丁目9−9', '0570-070-336', '10:00～18:00（定休日：年末年始）', 'https://grandata.jp/privacy/'],
        self::SAISAN_GAS => [self::SUPPLIER_TYPE_GAS, '株式会社サイサン', '330-0854', '埼玉県さいたま市大宮区桜木町1-11-5', '0120-41-3030', '24時間', 'https://www.saisan.net/toshigas/privacy.html'],
        self::FAMILY_NET_JAPAN_GAS => [self::SUPPLIER_TYPE_GAS, '株式会社ファミリーネット・ジャパン', '105-6229', ' 東京都港区愛宕二丁目５番１号', '0120-554-841', '9:00～17:00　（定休日：土日祝、年末年始）', 'https://www.fnj.co.jp/privacy/'],
        self::HTB_ENERGY_ELECTRIC => [self::SUPPLIER_TYPE_ELECTRIC, 'HTBエナジー株式会社', '810-0001', '福岡県中央区天神3-9-25　東晴天神ビル', '050-3852-1193', '10:00～18:00（定休日：土日祝、年末年始）', 'https://htb-energy.com/policy.html'],
    ];
}
