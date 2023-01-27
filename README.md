# グランデータ マイページ laravel5 
リニューアル版  
テーブル構造、機能についてもあわせて変更。  

## システム構成

laravel 5.7 の要件に依存します。  

- PHP >= 7.1.3
- OpenSSL PHP拡張
- PDO PHP拡張
- Mbstring PHP拡張
- Tokenizer PHP拡張
- XML PHP拡張
- Ctype PHP拡張
- JSON PHP拡張
- BCMath PHP拡張

参照：Laravel 5.7 インストール  
https://readouble.com/laravel/5.7/ja/installation.html

## リポジトリからgit clone して設置後に行う手順  
以下のリポジトリで管理されております。  
https://ap-northeast-1.console.aws.amazon.com/codesuite/codecommit/repositories/himawari_renew

個人ユーザのような権限での設定、取得することを推奨します。  

### composer installを実行してvendorの各種ライブラリを取得する  
まず、適切にcomposerがインストールされていること。  
ユーザは、強い権限のec2-userではなく、個人ユーザのようなものであることなどを確認する。  
設置したカレントディレクトリで、下記を実行します。  

```
composer install  
```


### アプリケーションキーの初期化  
初期設定されていないため、  
Application key を生成してください。  

```
php artisan key:generate
```

参考：  
- laravel プロジェクトで RuntimeException No application encryption key has been specified.  
https://qiita.com/pugiemonn/items/641718fd241320384972  

## 環境設定 .envのコピー、調整    
参考元がありますので、こちらプロジェクトに合わせて参考にされてください。  
データベース設定など特に必須となります。  
VIEW_THAME で選択されるビューのテーマが切り替わります。  
ViewSwitchMiddleware.phpを参照ください。  

```
cp .env.example .env  
```

.env.himawari グランデータ用  

### メールサーバ設定について
開発環境の場合、メール送信をしても届かない可能性があります（信頼できるメール規制に関連している可能性あり）  
メール送信に伴う検証は、stg環境、あるいはlogに出力して対応する
手順にする必要があるかもしれません。  


## 設置時のパーミッション設定
bod ユーザにて、共有グループは webmaster の場合。  
最初だけec2-userなど、強い権限のあるユーザでの操作を必要とします。  

```
sudo groupadd webmaster
cat /etc/group

sudo gpasswd -a bod webmaster
sudo gpasswd -a apache webmaster

sudo service httpd restart
```
apacheの再起動は必要。  

gpasswdに -aを忘れるとグループに追加されるのではなく、所属するセカンダリグループが置き換えられてしまいます。

参加できているか groups で確認する。  

```
[ec2-user@****** ~]$ groups bod
bod : bod ec2-user apache webmaster
[ec2-user@****** ~]$ groups apache
apache : apache ec2-user
```

これを見るに、同じグループの ec2-user または apache であれば解決するはず。  
ユーザのパーミッション設定

- 初期ディレクトリパーミッション設定の変更  
SGIDを設定します。（誰がどう作成しても、作成されたファイル・ディレクトリの所有グループが設定したとおりになります）  
一部領域に適用します。  

```
sudo find ./ -type d -exec chmod 755 {} \;
sudo find ./ -type f -exec chmod 644 {} \;

sudo find ./storage -type d -exec chmod 775 {} \;
sudo find ./storage -type f -exec chmod 664 {} \;

sudo chown -R :webmaster ./storage
sudo chown -R :webmaster ./bootstrap/cache

```

- SGIDの設定  
SGIDを設定する（誰がどう作成しても、作成されたファイル・ディレクトリの所有グループが設定したとおりになります）

```
cd /var/www/domains/bod.bod-develop.com

cd himawari_demo (該当プロジェクト)

sudo find ./storage -type d -exec chmod g+s {} \;
sudo find ./bootstrap/cache -type d -exec chmod g+s {} \;
```

確認手順は以下。  
ls の実行をすると、パーミッションにsが表示されます。  
+はACL設定行った後に表示されます。  

```
ls -la ./storage
(ディレクトリのパーミッション表示が drwxrwsr-x+ 、 ".." も確認すること。)
```

- ACLのデフォルト設定変更
配下に新規ファイルやディレクトリが作られるとき、ディレクトリは775に、ファイルは664に自動的に設定してくれるように設定します。

```
cd /var/www/domains/bod.bod-develop.com

cd himawari_demo (該当プロジェクト)

sudo setfacl -R -d -m g::rwx ./storage
sudo setfacl -R -d -m g::rwx ./bootstrap/cache

```

確認手順は以下。  

```
find ./ -perm -2000
(結果に ./storage 以下が含まれること)

```


参考：  
- CentOS/Apache/Laravel使用時のpermission設定方法  Laravel用にユーザーグループを作成する  
https://qiita.com/Todate/items/d0d5c38f62621711a79b

- Linuxでユーザーをグループに追加する  
https://qiita.com/orangain/items/056db6ffc16d765a8187


## マイグレーション
データベースの初期化時には行います。  
.envの設定も正しく行っておく必要がございます。  

```
php artisan migrate

php artisan db:seed
```

初期管理者ユーザも含む。  


失敗時は全テーブル削除で履歴は消せます。  

```
php artisan migrate:rollback
```

Seederを追加した際には、必ず、

```
composer dump-autoload
```

を実施してください。

設置時に複数DBでの並行運用を行う際には、
master/slaveを.envで切り替えます。  

master側では
```
php artisan migrate

php artisan db:seed
```

slave側では
```
php artisan migrate
```


## 自動テスト
phpunit で画面表示のテストを実施可能です。  

```
vendor/bin/phpunit
```

結果例：  
```
[***@****** himawari_demo]$ vendor/bin/phpunit
PHPUnit 7.5.8 by Sebastian Bergmann and contributors.

..........                                                        10 / 10 (100%)

Time: 270 ms, Memory: 16.00 MB

OK (10 tests, 10 assertions)
```

## 更新時
route:cache はプログラム構造の都合失敗する場合があります。  

キャッシュの更新を行います。  

```
composer dump-autoload

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

対するキャッシュ実施コマンドは。  

```
composer dump-autoload --optimize

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

# 運用メンテナンス関連

## メンテナンスモードへの切り替え
Laravel5における、一般的な手順です。  

1. 503 メッセージになります。メッセージ表示は場合によって変えるかどうかは考える必要があります。
2. php artisan down / php artisan up でメンテナンスモードになる、解除など。
3. メンテナンスモード時のIP開放先は、作業者関係者限定である必要はある。

表示を許可する例など  
```
php artisan down --allow=192.168.0.0/16
php artisan down --allow=127.0.0.1 --allow=192.168.0.0/16
```

