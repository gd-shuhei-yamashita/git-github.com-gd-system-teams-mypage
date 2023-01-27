<?php
// ヘルパー
// ex. Laravel 5へ自作のヘルパー関数を追加するベストプラクティス
// https://s8a.jp/laravel-custom-helper#composer%E3%81%A7%E3%82%AA%E3%83%BC%E3%83%88%E3%83%AD%E3%83%BC%E3%83%89%E3%81%99%E3%82%8B%E6%96%B9%E6%B3%95

/**
 * 
 */
// function mail_escape($mail){
//     $validator=validator(
//         ["mail"=> $mail ],
//         ["mail"=>'email']
//     );
//     if($validator->fails()){
//         //\log::debug( preg_replace("/^([^@]+)/",'"\1"', $mail) );
//         //問題のあるメールアドレスの場合はエスケープ
//         return preg_replace("/^([^@]+)/",'"\1"', $mail);
//     }
//     return $mail;
// }

/**
 * 一部ドメインにメールエイリアスに当たるものを許可する。  
 */
function mail_alias_replace($mail) {
    // メールアドレスを判断し、該当するメールにだけ置き換えを実施します。  
    // test+01@po.hikari.co.jpでは、+01 にあたる位置のものを置き換えます。  
    if ( preg_match('/^(.*?)\+[a-zA-Z0-9_-]*(\@.*?.hikari\.co\.jp)$/i', $mail,$matches) ) {
        $mail = $matches[1] . $matches[2] ;
    }

    return $mail;
}

// ex. CSRF の安全なトークンの作成方法
// https://www.websec-room.com/2013/03/05/443
// http://php.net/manual/ja/function.openssl-random-pseudo-bytes.php
// 32バイトのCSRFトークンを作成
function get_csrf_token() {
  $TOKEN_LENGTH = 16;//16*2=32バイト
  $bytes = openssl_random_pseudo_bytes($TOKEN_LENGTH);
  return bin2hex($bytes);
}

