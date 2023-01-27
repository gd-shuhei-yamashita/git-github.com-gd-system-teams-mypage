{{-- パスワード設定画面（パスワード忘れ） --}}
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
        　<header>
            <!-- <button type="button" class="burger js-btn">
                <span class="btn-line"></span>
            </button>
            <nav>
                <ul class="menu">
                    <li class="menu-list"><img src="img/home_black.svg">ホーム</li>
                    <li class="menu-list"><img src="img/yen_black.svg">使用量・請求金額</li>
                    <li class="menu-list"><img src="img/perm_identity_black.svg">契約情報</li>
                    <li class="menu-list"><img src="img/https_black.svg">パスワード変更</li>
                    <li class="menu-list"><img src="img/alternate_email_black.svg">メールアドレス変更</li>
                    <li class="menu-list logout"><img src="img/logout_black.svg">ログアウト</li>
                </ul>
            </nav>
            <p>契約者名　様</p> -->
        </header>
        <div class="l-main">
            <h2>パスワードリマインダー<div class="h2-border"></div>
            </h2>

            <div class="input-field-text">
                <a>お客様 ( {{ $email }} ) の新しいパスワードをご入力下さい。<br/>
				下記の項目を入力して、【登録】ボタンをクリックしてください。<br/>
                ※パスワードは英字の大文字/小文字、数字を全て使用し10桁以上で登録してください。
                </a>
            </div>


            <div class="form-area mail-form-area">
                <form id="submenu_password_init" class="col s12" action="{{ ($route_name == 'password_init2') ?  route('password_init2_change') : route('password_init_change') }}" method="post">
					<input type="hidden" name="email" id="email" value="{{ $email }}" />
					<input type="hidden" name="remember_token" id="remember_token" value="{{ $remember_token }}" />
					{{ csrf_field() }}
					<div class="row">
					  <span id="password_new_err" class="help-block red-text" style="font-weight: bolder;">{{$errors->first('password_new')}}</span>
					</div>
                    <div class="input-field">
                        <label>新しいパスワード<input type='password' value="{{old('password_new')}}" name='password_new' id='password_new'></label>
                    </div>
                    <div class="input-field">
                        <label>新しいパスワード(確認)<input type='password' value="{{old('password_new_confirmation')}}" name='password_new_confirmation' id='password_new_confirmation'></label>
                    </div>
                    <div class="input-field">
                        <button id="btn_entry" type="submit" >登録<img
                                src="/img/published_with_changes_black.svg"></button>
                    </div>
                </form>
            </div>

        </div>

@include('layout.t_copyright2')
@yield('copyright2')


</div>




<!--<nav>
  <div class="nav-wrapper white text-darken-4 center-align">
    <a href="#" class="brand-logo"><a href="/" class="logo-container">
@include('layout.t_logo')
@yield('logo')
    </a></a>
    <!-- a href="#" class="brand-logo">Logo</a -- >
  </div>
</nav>
  <div class="section"></div>
  <div class="container center">

      <form id="submenu_password_init" class="col s12" action="{{ ($route_name == 'password_init2') ?  route('password_init2_change') : route('password_init_change') }}" method="post">
        <input type="hidden" name="email" id="email" value="{{ $email }}" />
        <input type="hidden" name="remember_token" id="remember_token" value="{{ $remember_token }}" />
        {{ csrf_field() }}
        <div class='row'>
          <div class="col s12 m8 offset-m2 left-align">
          <h5> <i class="material-icons left">vpn_key</i>パスワードリマインダー</h5>
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

  <div class="section"></div>-->
@endsection

@section('pageJs')
<script src="{{asset('js/entry.js') }}"></script>
<script src="{{asset('js/reminder.js') }}"></script>
@endsection
@include('layout.t_footer')