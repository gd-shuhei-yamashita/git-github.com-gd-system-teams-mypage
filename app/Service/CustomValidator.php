<?php

namespace App\Services;

/**
 * Laravel5 バリデータ挙動の適正化パッチ
 * 
 * カスタムバリデータ alpha,alpha_num,alpha_dashを日本語に対応する
 * ex. [Laravel]バリデーションalpha_num指定しても全角文字が通ってしまう問題
 * https://qiita.com/meso_/items/f738c128af1e384931a8
 * ex2. Laravel の Validation を正しく拡張する
 * https://qiita.com/moobay9/items/f1cdd3c8f995fdcf0963
 */
class CustomValidator extends \Illuminate\Validation\Validator 
{
    /**
     * alpha
     *
     * @param string $attribute
     * @param string $value
     * @return true
     */
    public function validateAlpha($attribute, $value)
    {
        return (preg_match("/^[a-z]+$/i", $value));
    }

    /**
     * alpha_dash
     *
     * @param string $attribute
     * @param string $value
     * @return true
     */
    public function validateAlphaDash($attribute, $value)
    {
        return (preg_match("/^[a-z0-9_-]+$/i", $value));
    }

    /**
     * alpha_num
     *
     * @param string $attribute
     * @param string $value
     * @return true
     */
    public function validateAlphaNum($attribute, $value)
    {
        return (preg_match("/^[a-z0-9]+$/i", $value));
    }
}