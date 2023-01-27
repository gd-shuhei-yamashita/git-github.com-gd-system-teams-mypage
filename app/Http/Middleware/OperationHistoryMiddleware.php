<?php

namespace App\Http\Middleware;

use Closure;
use App\OperationHistory;
use \Route;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

/**
 * ex. Laravel5.6 で操作ログを自動で記録する  
 * https://qiita.com/nobu-maple/items/88bd6620d98bb38413bc
 */
class OperationHistoryMiddleware
{
    /**
     * 着信要求を処理します。
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $this->OperationHistory($request, $response->status());

        return $response;
    }

    /**
     * 作業履歴の自動記録
     */
    public function OperationHistory($request, $status)
    {
        // debugbar系のログは残さない
        if (strpos(Route::currentRouteName(),"debugbar.") !== false) { return 0;}
        
        $data = [
            'user_id' => Session::get('user_login') ? Session::get('user_login.customer_code') : "-",
            'route' => Route::currentRouteName(),
            'url' => $request -> path(),
            'method' => $request -> method(),
            'status' => $status,
            // 先頭400文字
            'message' => count($request->toArray()) != 0 ? substr( json_encode($request->toArray()) , 0, 400) : null,
            'remote_addr' => $request -> ip(),
            'user_agent' => $request -> userAgent(),
            'created_at' => date("Y-m-d H:i:s")
        ];
        // requestsから的確に取り出す (routeに応じて変化させる)
        // 画面ごとの詳細アップロードなどをログ化する
        // ファイル名取得  
        $filename_write_list = ["capture_usagedata_registration", "capture_billingdata_registration"];
        if (in_array(Route::currentRouteName() , $filename_write_list , true) && $request -> method() == "POST") {
            $data["file_name"] = $request -> file_name;
        }
        // フォーム投稿内容のパスワードが残らないように検閲する  
        if ($request -> path() == "login" && $request -> method() == "POST") {
            $data["message"] = json_encode(["email"=> $request -> email, "password"=>"-censored-"]);
        }

        Log::channel('access')->debug(implode(",", $data));
    }

    /** 
     *  作業履歴の手動記録
     * 
     */
    public function OperationHistoryManual($request, $subreq)
    {
        $data = [
            'user_id' => Session::get('user_login') ? Session::get('user_login.customer_code') : "-",
            'route' => Route::currentRouteName(),
            'url' => $request -> path(),
            'method' => $request -> method(),
            'status' => array_key_exists("status", $subreq) ? $subreq["status"] : "0",   // 手動
            'message' => array_key_exists("message", $subreq) ? $subreq["message"] : "", // 手動
            "file_name" => array_key_exists("file_name", $subreq) ? $subreq["file_name"] : "", // 手動
            'remote_addr' => $request -> ip(),
            'user_agent' => $request -> userAgent(),
            'created_at' => date("Y-m-d H:i:s"),
        ];

        Log::channel('access')->debug(implode(",", $data));
    }
}
