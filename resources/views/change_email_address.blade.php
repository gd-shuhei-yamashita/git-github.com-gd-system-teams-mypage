{{-- メールアドレス変更画面 --}}
@extends('layout.t_common')

@section('title','メールアドレス変更')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 0)
@section("cate2", 2)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')
<main>
    <div class="main">
@if (session('user_login.role') < 5)
  <div class="section red-text">【注意】管理者権限でユーザー画面を閲覧しております</div>
@endif

        <div class="l-main">
            <h2>メールアドレス変更<div class="h2-border"></div>
            </h2>
            <div class="input-field-text">
                <a>メールアドレスを変更する際は、以下に新しいメールアドレスを入力して、「変更する」ボタンをクリックしてください。<br>
                    ※半角英数字でご入力ください。
                </a>
            </div>
          <span class="help-block red-text" style="font-weight: bolder;">{{$errors->first('msg')}}</span>

            <div class="form-area mail-form-area">
                <form id="submit_section" action="{{ route('change_email_address_post') }}" method="post">    
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

                    <div class="input-field">
                        <label>新しいメールアドレス<input value="{{old('mail_address')}}" id="mail_address" name="mail_address" type="text" name="newmail"></label>
                    </div>
                    <div class="input-field">
                        <label>新しいメールアドレスの確認<input value="{{old('re_mail_address')}}" id="re_mail_address" type="text" name="re_mail_address"></label>
                    </div>
                    <div class="input-field">
                        <button type="submit" name="mail-change">変更する<img
                                src="/img/published_with_changes_black.svg"></button>
                    </div>
                </form>
            </div>

        </div>
@include('layout.t_copyright2')
@yield('copyright2')
    </div>

</main>
@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
