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
<h1>
【{{$service_name}}】パスワード変更完了のお知らせ
</h1>
<p>
    <br/>
    {{$service_name}}のサービスをご利用いただき誠にありがとうございます。<br/>
    パスワード設定の変更が完了いたしました。<br>
</p>
<p>
<hr  >
本メールは送信専用のため、ご返信いただいてもお答えできませんのでご了承ください。<br/>
また、本メールにお心当たりのない場合は、本メールを破棄くださいますようお願いいたします。<br/>
<br/>
【発行元】{{$service_name}}<br/>
<hr  >
</p>


</body>
</html>
