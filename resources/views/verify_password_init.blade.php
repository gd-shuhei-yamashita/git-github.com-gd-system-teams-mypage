{{-- パスワード設定画面（初回ログイン） --}}
@extends('layout.t_common')

@section('title','パスワード設定')

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
          初回パスワード設定
          <div class="h2-border"></div>
        </h2>
        <div class="input-field-text">
          <a>
            お客様( {{ $email }} ) の新しいパスワードをご入力下さい。下記の項目を入力して、「登録する」ボタンをクリックして下さい。<br />
            ※パスワードは英字の大文字 /
            小文字、数字を全て使用し10桁以上で登録してください。
          </a>
        </div>
        <div class="form-area">
          <form id="submenu_password_init" action="{{ ($route_name == 'verification.email2_reminder') ?  route('verification.email2_reminder.update') : route('verification.email_reminder.update') }}" method="post">
		<input type="hidden" name="email" id="email" value="{{ $email }}" />
		<input type="hidden" name="remember_token" id="remember_token" value="{{ $remember_token }}" />
        {{ csrf_field() }}
            <div class="row">
              <span id="password_new_err" class="help-block red-text" style="font-weight: bolder;">{{$errors->first('password_new')}}</span>
            </div>
            <div class="input-field">
              <label>新しいパスワード※<input type='password' value="{{old('password_new')}}" name='password_new' id='password_new'></label>
            </div>
            <div class="input-field">
              <label
                >新しいパスワード(確認)※<input type='password' value="{{old('password_new_confirmation')}}" name='password_new_confirmation' id='password_new_confirmation'></label>
            </div>

            <div class="input-field">
              <button id="btn_entry" type="submit" >
                登録する<img src="/img/arrow_right_black.svg" />
              </button>
            </div>
          </form>
        </div>
      </div>



<!--<main style="padding-left: 0px;">
<nav>
  <div class="nav-wrapper white text-darken-4 center-align">
    <a href="#" class="brand-logo"><a href="/" class="logo-container">

    </a></a>
  </div>
</nav>
  <div class="section"></div>
  <div class="container center row">
  <!-- step2 ->
  <div class="col s12 m8 offset-m2 row" style="">
    <div class="col s4 m3 btn-large green" >1.メールアドレス</div>
    <div class="col s4 offset-m1 m3 btn-large green" >2.パスワード</div>
    <div class="col s4 offset-m1 m3 btn-large grey lighten-2" >3.登録完了</div>
  </div>
  <!-- /step2 ->
      <form id="submenu_password_init" class="col s12" action="{{ ($route_name == 'verification.email2_reminder') ?  route('verification.email2_reminder.update') : route('verification.email_reminder.update') }}" method="post">
        <input type="hidden" name="email" id="email" value="{{ $email }}" />
        <input type="hidden" name="remember_token" id="remember_token" value="{{ $remember_token }}" />
        {{ csrf_field() }}
        <div class='row'>
          <div class="col s12 m8 offset-m2 left-align">
          <h5> <i class="material-icons left">vpn_key</i>  パスワード設定</h5>
          </div>
          <div class="col s12 m8 offset-m2 left-align">
          お客様 ( {{ $email }} ) の<br/>新しいパスワードをご入力下さい。<br/>
          下記の項目を入力して、【登録】ボタンをクリックしてください。<br/>
          ※パスワードは英字の大文字/小文字、数字を全て使用し10桁以上で登録してください。
          </div>
        </div>
        <div class='row'>
          <div class="col s12 m8 offset-m2">
            <span id="password_new_err" class="error red-text" style="font-weight: bolder;float: left;">{{$errors->first('password_new')}}</span>
          </div>
        </div>
        <div class='row'>
          <div class="input-field col s12 m8 offset-m2">
            <input type='password' placeholder="パスワードをご入力ください" value="{{old('password_new')}}" name='password_new' id='password_new'><i class="material-icons toggle-password small">visibility_off</i>
            <label for='password_new'>新しいパスワード</label>
          </div>
        </div>
        <div class='row'>
          <div class="input-field col s12 m8 offset-m2">
            <input type='password' placeholder="確認のためパスワードを再度ご入力ください" value="{{old('password_new_confirmation')}}" name='password_new_confirmation' id='password_new_confirmation'><i class="material-icons toggle-password small">visibility_off</i>
            <label for='password_new_confirmation'>新しいパスワード(確認)</label>
          </div>
        </div>

        <div class='row'>
          <button id="btn_entry" type="submit" class="col s12 m8 offset-m2 btn waves-effect waves-light orange">登録</button>
        </div>
      </form>

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