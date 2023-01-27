<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Artisan;

/**
 * バッチ実行用コントローラー
 */
class AdminBatchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** メニュー画面 */
    public function menu()
    {
      return view('admin/upload_csv');
    }

    /** 使用量アップロード画面表示 */
    public function uploadusage()
    {
      $dir = public_path() . "/storage/usage/";

      $files = [];
      if( is_dir( $dir ) && $handle = opendir( $dir ) ) {
		    while( ($file = readdir($handle)) !== false ) {
			    if( filetype( $path = $dir . $file ) == "file" ) {
			      // $file: ファイル名
            // $path: ファイルのパス
            $files[] = $file;
			    }
		    }
	    }
      asort($files);
      return view('admin/upload_usage_csv', compact('files'));
    }

    /** 請求アップロード画面表示 */
    public function uploadbilling()
    {
      $dir = public_path() . "/storage/billing/";

      $files = [];
      if( is_dir( $dir ) && $handle = opendir( $dir ) ) {
		    while( ($file = readdir($handle)) !== false ) {
			    if( filetype( $path = $dir . $file ) == "file" ) {
			      // $file: ファイル名
            // $path: ファイルのパス
            $files[] = $file;
			    }
		    }
	    }
      asort($files);
      return view('admin/upload_billing_csv', compact('files'));
    }

    /** 内訳アップロード画面表示 */
    public function uploadmeisai()
    {
      $dir = public_path() . "/storage/meisai/";

      $files = [];
      if( is_dir( $dir ) && $handle = opendir( $dir ) ) {
		    while( ($file = readdir($handle)) !== false ) {
			    if( filetype( $path = $dir . $file ) == "file" ) {
			      // $file: ファイル名
            // $path: ファイルのパス
            $files[] = $file;
			    }
		    }
	    }
      asort($files);
      return view('admin/upload_items_csv', compact('files'));
    }

    /** 使用量CSVアップロード */
    public function storeusage(Request $request){
      // 下記の設定はphp.ini直修正しかダメ
      // ini_set("upload_max_filesize",'30M');
      // ini_set("post_max_size",'30M');
      $files = $request->file('file');

      foreach($files as $file){
        $file_name = $file->getClientOriginalName();
        if (preg_match( "/使用量/", $file_name)) {
          $file->storeAS('public/usage',$file_name);
        }
      }

      // printf("<a href='/admin/batch/usagedata'>取込実行</>");
      // printf('<br>');
      printf("<a href='/admin/upload/usagedata'>戻る</>");
      // return view('admin/upload_usage_csv');
    }

    /** 請求CSVアップロード */
    public function storebilling(Request $request){
      // 下記の設定はphp.ini直修正しかダメ
      // ini_set("upload_max_filesize",'30M');
      // ini_set("post_max_size",'30M');

      
      $files = $request->file('file');

      foreach($files as $file){
        $file_name = $file->getClientOriginalName();
        if (preg_match( "/請求/", $file_name)) {
          $file->storeAS('public/billing',$file_name);
        }
      }

      // printf("<a href='/admin/batch/billingdata'>取込実行</>");
      // printf('<br>');
      printf("<a href='/admin/upload/billingdata'>戻る</>");
      // return view('admin/upload_billing_csv');
    }

    /** 内訳CSVアップロード */
    public function storemeisai(Request $request){
      // 下記の設定はphp.ini直修正しかダメ
      // ini_set("upload_max_filesize",'30M');
      // ini_set("post_max_size",'30M');
      $files = $request->file('file');

      foreach($files as $file){
        $file_name = $file->getClientOriginalName();
        if (preg_match( "/内訳/", $file_name)) {
          $file->storeAS('public/meisai',$file_name);
        }
      }

      // printf("<a href='/admin/batch/meisaidata'>取込実行</>");
      // printf('<br>');
      printf("<a href='/admin/upload/meisaidata'>戻る</>");
      // return view('admin/upload_items_csv');
    }

    /** 使用量 取込 */
    public function usagedata()
    {
      ini_set("max_execution_time",300);
      ini_set('memory_limit', '1500M');
      Artisan::call("import:usagedata", ["dir" => public_path() . "/storage/usage/"]);
      // Illuminate\Support\Facades\Artisan::call("import:usagedata");
      printf("<a href='/admin/upload/menu'>戻る</>");
    }
    
    /** 請求 取込 */
    public function billingdata()
    {
      ini_set("max_execution_time",300);
      ini_set('memory_limit', '1500M');
      Artisan::call("import:billingdata", ["dir" => public_path() . "/storage/billing/"]);
      // Illuminate\Support\Facades\Artisan::call("import:billingdata");
      printf("<a href='/admin/upload/menu'>戻る</>");
    }
    
    /** 内訳 取込 */
    public function meisaidata()
    {
      ini_set("max_execution_time",300);
      ini_set('memory_limit', '1500M');
      Artisan::call("import:meisaidata", ["dir" => public_path() . "/storage/meisai/"]);
      // Illuminate\Support\Facades\Artisan::call("import:meisaidata");
      printf("<a href='/admin/upload/menu'>戻る</>");
    }
}
