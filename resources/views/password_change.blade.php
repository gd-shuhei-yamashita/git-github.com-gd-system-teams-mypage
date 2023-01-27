{{-- パスワード変更画面 --}}
@extends('layout.t_common')

@section('title','パスワード変更')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 0)
@section("cate2", 1)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')
<main>
    <div class="main">
		
        <div class="l-main">
            <h2>パスワード変更<div class="h2-border"></div>
            </h2>

            <div class="input-field-text">
                <a>パスワードを変更する際は、以下に新しいパスワードを入力して、「変更する」ボタンをクリックしてください。<br>
                    ※パスワードは英字の大文字/小文字、数字を全て使用し10桁以上で登録してください。
                </a>
            </div>


            <div class="form-area mail-form-area">
                <form id="submit_section" action="{{ route('password_change') }}" method="post">
					<input type="hidden" name="cid" id="cid" value="{{ session('cid') }}" />
					<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
					<div class="row">
					  <span class="help-block red-text" style="font-weight: bolder;">{{$errors->first('password_new')}}</span>
					</div>
                    <div class="input-field">
                        <label>新しいパスワード<input value="{{old('password_new')}}" name="password_new" id="password_new" type="password" class="validate"></label>
                    </div>
                    <div class="input-field">
                        <label>新しいパスワード確認<input value="{{old('password_new_confirmation')}}" name="password_new_confirmation" id="password_new_confirmation" type="password" class="validate"></label>
                    </div>
                    <div class="input-field">
                        <button id="btn_entry" type="submit" name="pass-change">変更する<img
                                src="img/published_with_changes_black.svg"></button>
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
