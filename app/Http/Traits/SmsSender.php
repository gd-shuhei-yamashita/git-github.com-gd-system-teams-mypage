<?php

namespace App\Http\Traits;
use GuzzleHttp\Client;
use App\Http\Traits\Encryption;
use App\Http\Traits\StringFormatter;
use Illuminate\Support\Facades\Log;
use App;

/**
 * Class SmsSender
 *
 * @package App\Http\Traits
 */
trait SmsSender
{
    public static function LoginInfoSmsSend($sms_send_phone_number, $sms_send_customer_code)
    {
        $status = 2;
        $login_info_url = self::createLoginInfoUrl($sms_send_customer_code);
        if (App::environment('local')) {// 開発のみ
            Log::info(StringFormatter::removeHyphen($sms_send_phone_number));
            Log::info(self::setMessage('', $login_info_url));
            return [
                'http_status'   => 200,
                'status'        => 1,
            ];
        }
        $short_url = self::shortenFB($login_info_url);

        $request = [
            'message'      => self::setMessage("",$short_url),
            'phoneNumber'  => StringFormatter::removeHyphen($sms_send_phone_number),
            'smsid'       => date("YmdHis"),
        ];

        $client = new Client();
        $options = [
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' =>  'Basic ' . self::getAuthBasic()
            ],
            'query' => [
                'smsid' => $request["smsid"],
                'expansion' => '1',
                'format' => 'json',
                'mobilenumber' => $request["phoneNumber"],
                'smstext' => $request["message"],
            ]
        ];

        if (self::receivableSmsMobile($sms_send_phone_number)) {
            $res = $client->request(
                'GET',
                self::getRequestUrl(),
                $options
            );
        } else {
            $data = [
                'http_status'   => 200,
                'customer_code' => $sms_send_customer_code,
                'phone_number'  => $request["phoneNumber"],
                'short_url'     => $short_url,
                'message'       => $request["message"],
                'status'        => $status,
                'api_name'      => self::getApiName(),
                'request_json'  => json_encode($options),
                'response_code' => 110,
                'response_json' => 'Mobile number is invalid',
                'sms_id'        => $request['smsid'],
                'exceptions'    => 'Mobile number is invalid',
            ];
            return $data;
        }

        if ($res->getStatusCode() == "200") {
            $body = $res->getBody()->getContents();
            $decoded_body = json_decode($body,true);
            $response_code = isset($decoded_body[0]['status']) && !empty($decoded_body[0]['status']) ? $decoded_body[0]['status'] : "";
            $response_message = "";
            if ($response_code == 200) {
                $status = 1;
            } else {
                $response_message = $decoded_body[0]['text'];
            }

            $data = [
                'http_status'   => $res->getStatusCode(),
                'customer_code'   => $sms_send_customer_code,
                'phone_number'  => $request["phoneNumber"],
                'short_url'     => $short_url,
                'message'       => $request["message"],
                'status'        => $status,
                'api_name'      => self::getApiName(),
                'request_json'  => json_encode($options),
                'response_code' => $response_code,
                'response_json' => json_encode($body),
                'sms_id'        => $request['smsid'],
                'exceptions'    => $response_message,
            ];
            return $data;
        } else {
            $data = [
                'http_status'   => $res->getStatusCode(),
                'customer_id'   => $sms_send_customer_id,
                'phone_number'  => $request["phoneNumber"],
                'short_url'     => $short_url,
                'message'       => $request["message"],
                'status'        => $status,
                'api_name'      => self::getApiName(),
                'request_json'  => json_encode($options),
                'response_code' => "",
                'response_json' => "",
                'sms_id'        => $request['smsid'],
                'exceptions'    => $res->getStatusCode(),
            ];
            return $data;
        }

    }

    //SMSを受信できる携帯電話を判別
    private static function receivableSmsMobile($tel){

        if(preg_match('/(070|080|090)\d{4}\d{4}/', $tel)){
            return true;
        }

        return false;
    }

    private static function getRequestUrl()
    {
        return "https://api-v2.short-message-service.jp/11815grandata2/c/sendsms";
    }

    private static function getToken()
    {
        return "1e6f3e748fa8bd452e0f19fc8348ed1a";
    }

    private static function getClientId()
    {
        return "2233";
    }

    private static function getSmsCode()
    {
        return "82779";
    }

    private static function getApiName()
    {
        return "FunFusion";
    }

    private static function getShortenWebApiKey()
    {
        return "AIzaSyDlN82D8IdCiR_o7srrdStUVTDKEsi9vIk";
    }

    private static function getShortenDynamicLinkDomain()
    {
        return "grandata.page.link";
    }

    private static function getShortenUrl()
    {
        return "https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=";
    }

    private static function getUsername()
    {
        return "gd_api";
    }

    private static function getPassword()
    {
        return "himawari111";
    }

    private static function getAuthBasic()
    {
        return "Z2RfYXBpOlg3Y2M0TUQ1";
    }

    private static function createClientTag($plain_text)
    {
        return openssl_encrypt($plain_text, 'AES-128-ECB', $ssl_key = 'himawari_smssendprogram');
    }

    private static function ssl_decrypt($data)
    {
        return openssl_decrypt($data, 'AES-128-ECB', $ssl_key = 'himawari_smssendprogram');
    }

    private static function setMessage($career = "", $short_url = "", $call_phone_number = "")
    {
        // $call_phone_number = "0570-070-336";
        // $contact_url = "https://grandata-grp.co.jp/contact/contracted";

        $sms_send_message = "(株)グランデータです。\n";
        $sms_send_message .= "URLから認証を行い、マイページのログインIDとパスワードをご確認ください。\n";
        $sms_send_message .= $short_url;

        return $sms_send_message;
    }


    private static function base64url_encode($data) 
    {
        return rtrim(str_replace(array('+', '/'), array('-', '_'), $data), '=');
    }

    private static function createLoginInfoUrl($customer_code)
    {
        $param_arr = [
            "customer_code" => $customer_code
        ];
        if (App::environment('product')) {
            // 本番環境
            $http_host = config('const.ProdLoginInfoURL');
        }elseif(App::environment('staging')) {
            // 検証環境
            $http_host = config('const.DevLoginInfoURL');
        }else {
            // ローカル
            $http_host = config('const.DevLoginInfoURL');
        }

        $param_str = "param";
        $url = "";
        $url = self::create_json_param_url($http_host, $param_str, $param_arr);

        return $url;
    }

    private static function create_json_param_url($add_url, $param_str, $param_arr)
    {
        $result = "";
        // 引数に情報が入っているかチェック
        if (empty($add_url) ||
            empty($param_str) ||
            empty($param_arr) ||
            !is_array($param_arr)
        ) {
            return "error1";
        }

        // パラメータが全て入っているかチェック
        foreach ($param_arr as $key => $value) {
            if (empty($value)) {
                return "error2";
            }
        }

        $param_data = self::base64url_encode(Encryption::encode( json_encode($param_arr, JSON_UNESCAPED_UNICODE) ));

        if (empty($param_data)) {
            return "error3";
        }

        $result = $add_url."?".$param_str."=".$param_data;
        return $result;

    }

    private static function shortenFB($long_url){
        // ウェブAPIキー
        $web_api_key = self::getShortenWebApiKey();
        // ダイナミックリンクURL
        $dynamic_link_domain = self::getShortenDynamicLinkDomain();
        // 短縮URL
        $shorten_url = self::getShortenUrl();
        // 短縮URLの設定
        $url = $shorten_url . $web_api_key;
        $data = [
        "dynamicLinkInfo" => [
            "dynamicLinkDomain" => $dynamic_link_domain,
            "link" => $long_url
        ],
        "suffix" => [
            "option" => "SHORT"
        ]
        ];

        $client = new Client();
        $options = [
            'verify' => false,
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'json' => $data
        ];
        $res = $client->request(
            'POST',
            $url,
            $options
        );

        $short_url = "";
        if($res->getStatusCode() == 200){

            $body = $res->getBody()->getContents();
            // 短縮URLを取り出す
            $result_obj = json_decode($body);
            $short_url = $result_obj->shortLink;
        }
        return $short_url;
    }
}


