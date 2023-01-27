# 実行手法

既存システムからデータをコンバートする手順です。  
new_(テーブル名) を同様のものを一時的に作成して、
旧テーブルから中間データを用いて、csvエクスポートを行う処理を実施します。  

SQL式を流し込んでください。  

## 新テーブルの作成  

## 新テーブルのCSVデータインポート

usage_Tのカラム「usage」はMySQLの予約語の為、csvインポートでエラーになることがある。
その場合は一時的にカラム名を変更してcsvインポートを行う。
完了後カラム名を戻すのを忘れないこと。
alter table usage_T change column `usage` `temp` int;
alter table usage_T change column `temp` `usage` int;


