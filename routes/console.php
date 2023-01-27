<?php

// use Illuminate\Foundation\Inspiring;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\DB;
// use App\mypage\service\MailService;

/*
 * 夜中のバッチ全部動かす
 *
 *  */
Artisan::command('batch:all', function() {
    var_dump("夜間バッチを動作させます");
    \Log::channel("batch")->Info("夜間バッチを動作させます");

    Illuminate\Support\Facades\Artisan::call("import:usagedata");
    Illuminate\Support\Facades\Artisan::call("import:billingdata");
    Illuminate\Support\Facades\Artisan::call("import:meisaidata");

    \Log::channel("batch")->Info("夜間バッチが完了しました");
    var_dump("夜間バッチが完了しました");
});


Artisan::command('import:usagedata {dir?}', function($dir = "") {
    \Log::channel("batch")->Info("使用量データインポート開始");
    \Log::channel("batch")->Info($dir);
    $this->info("使用量データインポート開始");
    $batchControl = new App\BatchControl();

    $exists = $batchControl->where('batch_name','usage_batch')->exists();
    if ($exists) {
        \Log::channel("batch")->Info("多重実行防止");
        return false;
    }

    try {
        // データベースに値をinsert
        $batchControl->create([
            'batch_name' => 'usage_batch',
            'created_at' => now()
        ]);
    } catch (Exception $e) {
        \Log::channel("batch")->Info("テーブルロック失敗");
        return false;
    }

    $paths = [];

    if (empty($dir)) {
        $dir = config("const.UsageDataDir");
    }
    if( is_dir( $dir ) && $handle = opendir( $dir ) ) {
		while( ($file = readdir($handle)) !== false ) {
			if( filetype( $path = $dir . $file ) == "file" ) {
			  // $file: ファイル名
              // $path: ファイルのパス
              $paths[] = $path;
			}
		}
	}
    asort($paths);
    \Log::channel("batch")->Info($paths);

    foreach ($paths as $path) {
        // $this->info($path);
        $batch = new \App\Service\BatchService();
        $result = $batch->ImporUsageData($path);
        if ($result) {
            \Log::channel("batch")->Info("ファイル削除：" . $path);
            unlink($path);
        }
    }
    $exists = $batchControl->where('batch_name','usage_batch')->delete();

    $this->info("使用量データインポート完了");
})->describe("使用量データをインポートします");



Artisan::command('import:billingdata {dir?}', function($dir = "") {
    \Log::channel("batch")->Info("請求データインポート開始");
    \Log::channel("batch")->Info($dir);
    $this->info("請求データインポート開始");
    $batchControl = new App\BatchControl();

    $exists = $batchControl->where('batch_name','billing_batch')->exists();
    if ($exists) {
        \Log::channel("batch")->Info("多重実行防止");
        return false;
    }

    try {
        // データベースに値をinsert
        $batchControl->create([
            'batch_name' => 'billing_batch',
            'created_at' => now()
        ]);
    } catch (Exception $e) {
        \Log::channel("batch")->Info("テーブルロック失敗");
        return false;
    }

    $paths = [];

    if (empty($dir)) {
        $dir = config("const.BillingDataDir");
    }
    if( is_dir( $dir ) && $handle = opendir( $dir ) ) {
		while( ($file = readdir($handle)) !== false ) {
			if( filetype( $path = $dir . $file ) == "file" ) {
			  // $file: ファイル名
			  // $path: ファイルのパス
              $paths[] = $path;
			}
		}
    }
    asort($paths);
    \Log::channel("batch")->Info($paths);

    foreach ($paths as $path) {
        // $this->info($path);
        $batch = new \App\Service\BatchService();
        $result = $batch->ImportBillingData($path);
        if ($result) {
            \Log::channel("batch")->Info("ファイル削除：" . $path);
            unlink($path);
        }
    }

    $exists = $batchControl->where('batch_name','billing_batch')->delete();

    $this->info("請求データインポート完了");
})->describe("請求データをインポートします");

Artisan::command('import:meisaidata {dir?}', function($dir = "") {
    \Log::channel("batch")->Info("明細データインポート開始");
    \Log::channel("batch")->Info($dir);
    $this->info("明細データインポート開始");
    $batchControl = new App\BatchControl();

    $exists = $batchControl->where('batch_name','meisai_batch')->exists();
    if ($exists) {
        \Log::channel("batch")->Info("多重実行防止");
        return false;
    }

    try {
        // データベースに値をinsert
        $batchControl->create([
            'batch_name' => 'meisai_batch',
            'created_at' => now()
        ]);
    } catch (Exception $e) {
        \Log::channel("batch")->Info("テーブルロック失敗");
        return false;
    }

    $paths = [];

    if (empty($dir)) {
        $dir = config("const.MeisaiDdataDir");
    }

	if( is_dir( $dir ) && $handle = opendir( $dir ) ) {
		while( ($file = readdir($handle)) !== false ) {
			if( filetype( $path = $dir . $file ) == "file" ) {
			  // $file: ファイル名
			  // $path: ファイルのパス
              $paths[] = $path;
			}
		}
	}
    asort($paths);
    \Log::channel("batch")->Info($paths);

    foreach ($paths as $path) {
        // $this->info($path);
        $batch = new \App\Service\BatchService();
        $result = $batch->ImportMeisaiData($path);
        if ($result) {
            \Log::channel("batch")->Info("ファイル削除：" . $path);
            unlink($path);
        }
    }

    $exists = $batchControl->where('batch_name','meisai_batch')->delete();

    $this->info("明細データインポート完了");
})->describe("明細データをインポートします");
