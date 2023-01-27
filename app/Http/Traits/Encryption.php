<?php

namespace App\Http\Traits;

/**
 * Class Encryption
 *
 * @package App\Http\Traits
 */
trait Encryption
{
    /**
     * 暗号化
     *
     * @param string|null $plain
     * @return string
     */
    public static function encode(?string $plain): string
    {
        if (empty($plain)) {
            return '';
        }

        return base64_encode(openssl_encrypt($plain, self::opensslMethod(), self::opensslEncryptKey(), true, self::opensslEncryptIv()));
    }

    /**
     * @return string
     */
    private static function opensslMethod(): string
    {
        return 'AES-256-CBC';
    }

    /**
     * 32byte
     *
     * @return string
     */
    private static function opensslEncryptKey(): string
    {
        return '8tx7turh9pwszj5gx4lh23SVhvyejit7';
    }

    /**
     * 16byte
     *
     * @return string
     */
    private static function opensslEncryptIv(): string
    {
        return 'ytsxs3Qw6ad7qWmu';
    }

    /**
     * 復号化
     *
     * @param string $encrypted
     * @return string
     */
    public static function decrypt(string $encrypted): string
    {
        return openssl_decrypt(base64_decode($encrypted), self::opensslMethod(), self::opensslEncryptKey(), true, self::opensslEncryptIv());
    }
}
