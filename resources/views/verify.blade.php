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

    <div class="main">
      <div class="l-main login">
        <h2>
          初回ログイン
          <div class="h2-border"></div>
        </h2>
        <div class="input-field-text">
          <a>
            マイページ用パスワードの初期設定を行います。<br />
            下記の項目を入力して、「確認メールを送信する」ボタンをクリックしてください。<br />
            メールの受信設定をされている方は「@grandata-grp.co.jp」のドメインを受信可能なように設定ください。<br />
            ※は必須項目です。
          </a>
        </div>
        <div class="form-area">
          <form id="login" action="{{ route('verification.notice.update') }}" method="post">
          <input type="hidden" name="cid" id="cid" value="{{ session('cid') }}" />
          <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

          <div class="input-field short login-input">
            <label>マイページID※<input type='text' name='customer_code' id='customer_code' value="{{ session('user_now.customer_code') }}" disabled></label>
          </div>
          <div class="input-field short2 login-input">
            <label>お客様名※<input type='text' name='customer_code' id='customer_code' value="{{ session('user_now.name') }}" disabled></label>
          </div>
          <div class="input-field login-input">
            <label>メールアドレス※<input type='text' name='email' id='email' value="{{old('email')}}"></label>
          </div>
          <div class="input-field login-input">
            <label>メールアドレス(確認)※<input type='text' name='email_confirmation' id='email_confirmation' value="{{old('email_confirmation')}}"></label>
          </div>

          <div class="input-field flogin">
            <button type='submit' name='btn_login' >
              確認メールを送信する<img src="/img/email_black_24dp.svg" />
            </button>
          </div>
        </form>
      </div>
    </div>

<!--<main style="padding-left: 0px;">
<nav>
  <div class="nav-wrapper white text-darken-4 center-align">
    <a href="#" class="brand-logo"><a href="#" class="logo-container">
    </a></a>
  </div>
</nav>
<div class="section"></div>
<div class="section"></div>

<div class="container center row">
  <!-- step1 ->
  <div class="col s12 m8 offset-m2 row" style="">
    <div class="col s4 m3 btn-large green" >1.メールアドレス</div>
    <div class="col s4 offset-m1 m3 btn-large grey lighten-2" >2.パスワード</div>
    <div class="col s4 offset-m1 m3 btn-large grey lighten-2" >3.登録完了</div>
  </div>
  <!-- /step1 ->
  <div class="col s12 m8 offset-m2 z-depth-1 grey lighten-4 row" style="display: inline-block; border: 1px solid #EEE;">

    <!-- 初回入力フォーム ->
    <form id="login" action="{{ route('verification.notice.update') }}" method="post">
      <input type="hidden" name="cid" id="cid" value="{{ session('cid') }}" />
      <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

      <!-- メッセージ ->
      <div class='row' style="margin-bottom: 0;">
        <div class="col s12">
          <h4>初回ログインの方はこちら</h4>
          <div class="section left-align">
            マイページ用パスワードの初期登録を行います。<br/>
            下記の項目を入力して、確認メール送信ボタンをクリックしてください。<br/>
          </div>
        </div>
      </div>
      <!-- /メッセージ ->

      <div class='row' style="margin-bottom: 0;">
        <div class='input-field col s12'>
          <input type='text' name='customer_code' id='customer_code' value="{{ session('user_now.customer_code') }}" disabled>
          <label for='customer_code'>マイページID </label>
          <div id="customer_code_err" style="float: left;"></div>
        </div>
        <div class='input-field col s12'>
          <input type='text' name='customer_code' id='customer_code' value="{{ session('user_now.name') }}" disabled>
          <label for='customer_code'>お客様名 </label>
          <div id="customer_code_err" style="float: left;"></div>
        </div>
      </div>

      <div class='row' style="margin-bottom: 0;">
        <div class="col s12  left-align">
          <h5><i class="material-icons left">email</i>メールアドレス</h5>
        </div>
      </div>

      {{-- バリデーションエラーを返す https://readouble.com/laravel/5.5/ja/validation.html --}}
      @if ($errors->any())
      <div class='row col s12'>
        <ul>
        @foreach ($errors->all() as $error)
        <span class="error red-text" style="font-weight: bolder;float: left;">{{$error}}</span>
        @endforeach
        </ul>
      </div>
      @endif
      
      <div class='row' style="margin-bottom: 0;">
        <div class='input-field col s12'>
          <input type='text' name='email' id='email' value="{{old('email')}}">
          <label for='email'>メールアドレス </label>
          <!-- <div id="email_err" style="float: left;"></div> ->
        </div>
      </div>
      
      <div class='row' style="margin-bottom: 0;">
        <div class='input-field col s12'>
          <input type='text' name='email_confirmation' id='email_confirmation' value="{{old('email_confirmation')}}">
          <label for='email_confirmation'>メールアドレス(確認)  </label>
          <!-- <div id="email_confirmation_err" style="float: left;"></div> ->
        </div>
      </div>

      <div class='row'>
        <div class='input-field col s12'>
          <button type='submit' name='btn_login' class='col s12 btn btn-large waves-effect orange darken-1'><i class="material-icons left">play_circle_outline</i> 確認メールを送信する</button>
        </div>
      </div>

    </form>
    <!-- /初回入力フォーム ->
    <div class='row' style="margin-bottom: 0;">
      <a href="{{ route('logout') }}" class="collapsible-header waves-effect waves-amber menu_logout"  tabindex="0"><i class="material-icons left">eject</i>ログアウト</a>
    </div>
  </div>

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
