{{-- 初回メール確認画面 --}}
@extends('layout.t_common')

@section('title','初回ログインの方はこちら')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}

{{-- body_contents --}}
@section('content')

@if (session('status'))
<script>
window.onload = function() {
  M.toast({html: '{{ session('status') }}'});
  console.log('Windows onloaded');
}
</script>
@endif

        <div class="l-main">
            <h2>マイページ 初回登録完了<div class="h2-border"></div>
            </h2>

            <div class="input-field-text">
                <a>メールアドレス、パスワードの変更が完了しました。<br/>設定したパスワードでログインしてください。</a>
            </div>


            <div class="confirm-area data">
                
                    <div class="input-field register">
                        <a><button type="button" name="" onclick='location.href="{{ route("login") }}"'>ログイン画面へ<img src="/img/arrow_right_black.svg"></button></a>
                    </div>
            </div>


<!--
<main style="padding-left: 0px;">
<nav>
  <div class="nav-wrapper white text-darken-4 center-align">
    <a href="#" class="brand-logo"><a href="#" class="logo-container">
@include('layout.t_logo')
@yield('logo')
    </a></a>
  </div>
</nav>
<div class="section"></div>
<div class="section"></div>

<div class="container center row">
  <!-- step3 -- >
  <div class="col s12 m8 offset-m2 row" style="">
    <div class="col s4 m3 btn-large green" >1.メールアドレス</div>
    <div class="col s4 offset-m1 m3 btn-large green" >2.パスワード</div>
    <div class="col s4 offset-m1 m3 btn-large green" >3.登録完了</div>
  </div>
  <!-- /step3 -- >

  <!-- メッセージ -- >
  <div class="z-depth-1 grey lighten-4 row  error-wrap" style="display: inline-block; padding: 32px 48px 32px 48px; border: 1px solid #EEE;">
    <section>
      <h1>マイページ 初回登録完了</h1>
      <p>メールアドレス、パスワードの変更が完了しました。<br/>設定したパスワードでログインしてください。</p>
      <a href="{{ route('login') }}" class="collapsible-header waves-effect waves-amber"  tabindex="0"><i class="material-icons left">eject</i>ログイン画面へ</a>
    </section>
  </div>
  <!-- /メッセージ -- >

</div>

<div class="section"></div>
</main>
@include('layout.t_bodyfooter')
@yield('body_footer')-->
@include('layout.t_copyright2')
@yield('copyright2')
@endsection

@section('pageJs')

<script src="{{asset('js/entry.js') }}"></script>
<script src="{{asset('js/reminder.js') }}"></script>
@endsection

@include('layout.t_footer')
