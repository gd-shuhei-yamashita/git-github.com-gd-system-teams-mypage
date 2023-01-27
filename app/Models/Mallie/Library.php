<?php

namespace App\Models\Mallie;

use App;
use App\Consts\SupplierConsts;

/**
 * Mallie関連の処理でDBを介さないものを記述
 */
class Library
{
    const OPENSSL_METHOD = 'AES-256-CBC';
    const OPENSSL_ENCRYPT_KEY = '8tx7turh9pwszj5gx4lh23SVhvyejit7';
    const OPENSSL_ENCRYPT_IV = 'ytsxs3Qw6ad7qWmu';
    const PARAM_KEY = 'param';

    /**
     * 支払登録URLを取得する
     * @param string $cosutomerId Mallieの顧客ID
     * @param string $paymentId Mallieの支払ID
     * @return string
     */
    public static function getPaymentRegistUrl($cosutomerId, $paymentId) {
        $paramData = self::createParamData( $cosutomerId, $paymentId );
        return config('const.'. self::getConstKey(). 'RegistURL') . '?' . self::PARAM_KEY . '=' . $paramData;
    }

    /**
     * 支払変更URLを取得する
     * @param string $cosutomerId Mallieの顧客ID
     * @param string $paymentId Mallieの支払ID
     * @return string
     */
    public static function getPaymentChangeUrl($cosutomerId, $paymentId) {
        $paramData = self::createParamData( $cosutomerId, $paymentId );
        return config('const.'. self::getConstKey(). 'ModifyURL') . '?' . self::PARAM_KEY . '=' . $paramData;
    }

    /**
     * 
     * @param string
     * @return string
     */
    public static function getConstKey() {
        return (App::environment('product')) ? 'ProdPaymentMethod' : 'DevPaymentMethod';
    }

    /**
     * 支払登録・変更URLに使用するパラメータを作成する
     * @param string $cosutomerId Mallieの顧客ID
     * @param string $paymentId Mallieの支払ID
     * @return string
     */
    public static function createParamData($cosutomerId, $paymentId) {
        $jsonData = json_encode([
            'customer_ordered_id' => $cosutomerId,
            'payment_ordered_id' => $paymentId
        ], JSON_UNESCAPED_UNICODE);

        return self::base64url_encode( self::encrypt( $jsonData ) );
    }

    /**
     * base64url_encode
     * @param string
     * @return string
     */
    public static function base64url_encode($data) {
        return rtrim(str_replace(array('+', '/'), array('-', '_'), base64_encode($data)), '=');
    }

    /**
     * encrypt
     * @param string
     * @return string
     */
    public static function encrypt($data){
        return openssl_encrypt($data, self::OPENSSL_METHOD, self::OPENSSL_ENCRYPT_KEY, true, self::OPENSSL_ENCRYPT_IV);
    }


    /**
     * getPpsType
     * @param string $businessNumber
     * @return int
     */
    public static function getPpsType($businessNumber){
        if ($businessNumber == SupplierConsts::GRANDATA_ELECTRIC) {
            return SupplierConsts::GRANDATA_ELECTRIC_INDEX;
        } else if ($businessNumber == SupplierConsts::GRANDATA_GAS) {
            return SupplierConsts::GRANDATA_GAS_INDEX;
        } else if ($businessNumber == SupplierConsts::SAISAN_GAS) {
            return SupplierConsts::SAISAN_GAS_INDEX;
        } else if ($businessNumber == SupplierConsts::FAMILY_NET_JAPAN_GAS) {
            return SupplierConsts::FAMILY_NET_JAPAN_GAS_INDEX;
        } else if ($businessNumber == SupplierConsts::HTB_ENERGY_ELECTRIC) {
            return SupplierConsts::HTB_ENERGY_ELECTRIC_INDEX;
        }
        return null;
    }
}
