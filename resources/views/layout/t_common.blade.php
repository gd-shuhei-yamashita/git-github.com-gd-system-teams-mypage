<!DOCTYPE HTML>{{-- テンプレートの大本 --}}
<html lang="ja">
<head>
@yield('head')
@if(config('app.env') == 'product' && Session::get('user_now.role') == 9)
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-546HPE1VN9"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-546HPE1VN9');
  gtag('config', 'UA-217463400-1');
</script>
@endif
</head>
<body>
<script>
var application_url = "{{ preg_replace("/^https?\:\/\/(.*?)\//", "/", asset('') ) }}"; // グローバル変数
var _token = "{{ csrf_token() }}"; // トークン値
</script>
<?php if (env("APP_ENV" ) == 'dev') { echo "<style> nav .nav-wrapper { background-color: #DD0000 !important; } </style>"; } ?>
	<input id="token" type="hidden" name="_token" value="{{csrf_token()}}">

<!-- ステータスをトーストで返す -->
@if (session('status'))
<script>
window.onload = function() {
  M.toast({html: '{{ session('status') }}'});
  console.log('Windows onloaded');
}
</script>
@endif	
<!-- /ステータスをトーストで返す -->

@yield('body_header')
@yield('content')
@yield('footer')
</body>
</html>