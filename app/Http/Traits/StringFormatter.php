<?php

namespace App\Http\Traits;

/**
 * Trait StringFormatter
 *
 * @package App\Http\Traits
 */
trait StringFormatter
{
    /**
     * 空白で連結されたフルネームを性と名に分割
     *
     * @ref:https://qiita.com/kazu56/items/23500fd2b91f20afd3f1
     * @param $name
     * @return array
     */
    public static function splitName($name): array
    {
        // 全角スペースを半角スペースに変換
        $name = str_replace('　', ' ', $name);
        // 前後のスペース削除（trimの対象半角スペースのみなので半角スペースに変換後行う）
        $name = trim($name);
        // 連続する半角スペースを半角スペースひとつに変換
        $name = preg_replace('/\s+/', ' ', $name);
        // 姓と名で分割
        $name = explode(' ', $name);

        $lastName = $firstName = null;

        if (!empty($name[0])) {
            $lastName = $name[0];
        }

        if (!empty($name[1])) {
            $firstName = $name[1];
        }

        return [$lastName, $firstName];
    }

    /**
     * 郵便番号にハイフンを付ける
     * ハイフンがあったりなかったりするので、一度ハイフンを消した上で区切り箇所に再配置する
     *
     * @param string|null $str
     * @return string
     */
    public static function zipCodeHyphenResetting(?string $str): string
    {
        $resultStr = '';
        if (isset($str) && ($str !== '')) {
            $plainZipCode = self::removeHyphen(self::replaceHankaku($str));
            $resultStr = substr($plainZipCode, 0, 3) . '-' . substr($plainZipCode, 3);
        }

        return $resultStr;
    }

    /**
     * ハイフンを取り除く
     *
     * @param string|null $str
     * @return string
     */
    public static function removeHyphen(?string $str): string
    {
        $resultStr = '';
        if (isset($str) && ($str !== '')) {
            $resultStr = str_replace(self::hyphens(), '', $str);
        }

        return $resultStr;
    }

    /**
     * ハイフンとして扱う文字を返却
     *
     * @return array
     */
    private static function hyphens(): array
    {
        return ['-', '﹣', '－', '−', '⁻', '₋', '‐', '‑', '‒', '–', '—', '―', '﹘'];
    }

    /**
     * 半角変換
     *
     * @param string|null $str
     * @return string
     */
    public static function replaceHankaku(?string $str): string
    {
        $resultStr = '';
        if (isset($str) && ($str !== '')) {
            $resultStr = self::replaceHankakuHyphen(mb_convert_kana($str, 'kvas', 'UTF-8'));
        }

        return $resultStr;
    }

    /**
     * 半角ハイフン変換
     *
     * @param string|null $str
     * @return string
     */
    public static function replaceHankakuHyphen(?string $str): string
    {
        $resultStr = '';
        if (isset($str) && ($str !== '')) {
            foreach (self::hyphens() as $hyphen) {
                $resultStr = mb_ereg_replace($hyphen, '-', $str);
            }
        }

        return $resultStr;
    }

    /**
     * 任意の位置から文字列を切り取る
     * 
     * @param string|null $str
     * @return string
     */
    public static function cutStringFromAnyPosition(string $str, $start = 0, $end = 0): string
    {
        $resultStr = '';
        if (isset($str) && ($str !== '')) {
            $resultStr = substr($str, $start, $end);
        }

        return $resultStr;
    }

    /**
     * 全角変換
     * 
     * @param string|null $str
     * @return string
     */
    public static function replaceZenkaku($str): string
    {
        $resultStr = '';
        if (isset($str) && ($str !== '')) {
            $resultStr = self::replaceHankakuHyphen(mb_convert_kana($str, 'KVAS', 'UTF-8'));
        }

        return $resultStr;
    }

    /**
     * 全角ハイフン変換
     *
     * @param string|null $str
     * @return string
     */
    private static function replaceZenkakuHyphen(?string $str): string
    {
        $resultStr = '';
        if (isset($str) && ($str !== '')) {
            foreach (self::hyphens() as $hyphen) {
                $resultStr = mb_ereg_replace($hyphen, '－', $str);
            }
        }

        return $resultStr;
     }
         
    /**
     * ハイフンを起点に分割したものを返却する
     * @param string $id
     * @return string
     */
    public static function hyphenSplitting(string $data, $key): string
    {
        $hyphens = self::hyphens();
        foreach($hyphens as $value){
            $replace = str_replace($value, '−', $data);
            if(strpos($replace,'−') !== false){
                $sport = explode("−",$replace);
                return $sport[$key];
            }
        }
        return '';
    }
  
    /**
     * DBのDATE型カラムが「0000-00-00」で登録されていることがあるので、
     * 0000年00月00日をそのまま取り扱いたくない場合に本メソッドを使用して空白文字にする
     *
     * @param string|null $dateStr
     * @return string
     */
    public static function replace0000YearsToBlankString(?string $dateStr): string
    {
        $resultStr = $dateStr ?? '';

        if ($resultStr === '0000-00-00') {
            return '';
        }

        return $resultStr;
    }


    /**
     * 姓名の間に全角スペースを挿入する
     *
     * @param string|null $str
     * @return string
     */
    public static function insertZenkakuSpace(string $first, $second): string
    {
        $result = '';
        if (isset($first) && ($first !== '') && isset($second) && ($second !== '')) {
            $result = $first .'　'. $second;
        }

        return $result;
    }
}
