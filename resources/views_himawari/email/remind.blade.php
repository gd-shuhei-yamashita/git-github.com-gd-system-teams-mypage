<!DOCTYPE html>
<html lang="ja">
<style>
  body {
    background-color: #ffffff;
  }
  h1 {
    font-size: 16px;
    color: #ff6666;
  }
  #button {
    width: 200px;
    text-align: center;
  }
  #button a {
    padding: 10px 20px;
    display: block;
    border: 1px solid #2a88bd;
    background-color: #FFFFFF;
    color: #2a88bd;
    text-decoration: none;
    box-shadow: 2px 2px 3px #f5deb3;
  }
  #button a:hover {
    background-color: #2a88bd;
    color: #FFFFFF;
  }
</style>
<body>
<p>
    <br/>
    マイページをご利用いただき誠にありがとうございます。<br/>
    下記URLをクリックしてパスワードを設定してください。<br/>
    ※URLの有効期限は24時間です。<br/>
    ※有効期限切れとなった場合は、再度リマインダーメール送信を行ってください。<br/>
</p>
<p>
<a href="{{ $reset_url }}">{{ $reset_url }}</a><br>
</p>
<p>
<hr  >
本メールは送信専用のため、ご返信いただいてもお答えできませんのでご了承ください。<br/>
また、本メールにお心当たりのない場合は、本メールを破棄くださいますようお願いいたします。<br/>
<br/>
【発行元】株式会社グランデータ<br/>
 〒171-0022 東京都豊島区南池袋二丁目9番9号<br/>
 <br/>
 https://grandata-service.jp/<br/>
<hr  >
</p>


</body>
</html>
