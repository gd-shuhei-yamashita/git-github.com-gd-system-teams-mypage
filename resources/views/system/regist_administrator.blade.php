{{-- 管理者／テストユーザー登録画面 --}}
@extends('layout.t_common')

@section('title','管理者/Tユーザー登録')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<!--<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">-->

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 2)
@section("cate2", 4)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')

        <div class="l-main">
            <h2>管理者/T ユーザー登録<div class="h2-admin-border"></div>
            </h2>

            <div class="input-field-text">
                <a>新規の管理者／テストユーザー(一般側に表示されない)を登録します。システム管理者のみ実施可能です。</a>
            </div>
@if ( config('const.DBType') == 'multi_slave' )
<div class="error red-text" style="float: left;">サービス設定が multi_slave のため親サービスから登録を行ってください</div>
@else

            <div class="form-area info">
                <form id="submit_section" action="{{ route('regist_administrator_store') }}" method="post">
				{{ csrf_field() }}
@php
    $role_loop = [
        ['key' => ''  , 'value' => 'ユーザ種別を選んでください'] ,
        ['key' => '9' , 'value' => 'テストユーザー'] ,
        ['key' => '2' , 'value' => '管理者'] ,
    ];
@endphp
                    <div class="input-area user">
                        <div class="input-field input-field1">
                            <label>ユーザー種別</label>
                            <div class="pul">
                                <select name="role" id="role" class="pul">
@foreach ($role_loop as $roles)
            <option value="{{ $roles['key'] }}"{{ (($roles['key'] == '') ? ' disabled' : '') }}{{ (($roles['key'] == old('role')) ? ' selected' : '') }}>{{ $roles['value'] }}</option>
@endforeach
                                </select>
                            </div>
							<div id="role_err" class="error red-text" style="float: left;">{{$errors->first('role')}}</div>
                        </div>
                        <div class="input-field">
                            <label>名前（必須）</label>
                            <input placeholder="例：蔵野 出太" id="name" name="name" type="text" class="validate" value="{{ old('name') }}">
							<div id="name_err" class="error red-text" style="float: left;">{{$errors->first('name')}}</div>
                        </div>
                        <div class="input-field input-field1">
                            <label>マイページ ID（10文字）</label>
                            <input placeholder="例：TS00000000" id="customer_code" name="customer_code" type="text" class="validate" value="{{ old('customer_code') }}">
							<div id="customer_code_err" class="error red-text" style="float: left;">{{$errors->first('customer_code')}}</div>
                        </div>
                        <div class="input-field">
                            <label>メールアドレス（管理者のみ必須）</label>
                            <input placeholder="例：test@exsample.com" id="email" name="email" type="text" class="validate" value="{{ old('email') }}">
							<div id="email_err" class="error red-text" style="float: left;">{{$errors->first('email')}}</div>
                        </div>
                        <div class="input-field input-field1">
                            <label>パスワード（英字の大/ 小文字、数字全て使用）</label>
                            <input placeholder="例：Aaaa123456" id="password" name="password" type="text" class="validate" value="{{ old('password', 'Aaaa123456') }}">
							<div id="password_err" class="error red-text" style="float: left;">{{$errors->first('password')}}</div>
                        </div>
                    </div>

                    <div class="input-field register">
                        <button id="btn_entry" type="submit" name="">登録する<img src="/img/arrow_right_black.svg"></button>
                    </div>
                </form>
            </div>
@endif


        </div>




<!--
<main>
<div class="container">
  <div class="col s12">
  <!-- 管理者登録 -- >
    <div class="section">新規の管理者／テストユーザー(一般側に表示されない)を登録する。システム管理者のみ実施可能です。</div>
    <div class="row content header">
@if ( config('const.DBType') == 'multi_slave' )
<div class="error red-text" style="float: left;">サービス設定が multi_slave のため親サービスから登録を行ってください</div>
@else

      <form id="submit_section" action="{{ route('regist_administrator_store') }}" method="post">
      {{ csrf_field() }}
        <div class="row">

          <!-- Disabled Switch -- >
          <div class="col s12">
            <div class="input-field col s12 m8">
@php
    $role_loop = [
        ['key' => ''  , 'value' => 'ユーザ種別を選んでください'] ,
        ['key' => '9' , 'value' => 'テストユーザー'] ,
        ['key' => '2' , 'value' => '管理者'] ,
    ];
@endphp
          <select name="role" id="role">
@foreach ($role_loop as $roles)
            <option value="{{ $roles['key'] }}"{{ (($roles['key'] == '') ? ' disabled' : '') }}{{ (($roles['key'] == old('role')) ? ' selected' : '') }}>{{ $roles['value'] }}</option>
@endforeach
          </select>

              <label for="role">ユーザ種別</label>
              <div id="role_err" class="error red-text" style="float: left;">{{$errors->first('role')}}</div>
            </div>
          </div>

          <div class="col s12">
            <div class="input-field col s12 m8">
              <input placeholder="名前を入れてください" id="name" name="name" type="text" class="validate" value="{{ old('name') }}">
              <label for="name">名前(必須）</label>
              <div id="name_err" class="error red-text" style="float: left;">{{$errors->first('name')}}</div>
            </div>
          </div>

          <div class="col s12">
            <div class="input-field col s12 m8">
              <input placeholder="マイページIDを入れてください" id="customer_code" name="customer_code" type="text" class="validate" value="{{ old('customer_code') }}">
              <label for="customer_code">マイページID (10文字 例:TS00000000)</label>
              <div id="customer_code_err" class="error red-text" style="float: left;">{{$errors->first('customer_code')}}</div>
            </div>
          </div>
          
          <div class="col s12">
            <div class="input-field col s12 m8">
              <input placeholder="ここに管理者に登録したい メールアドレスを入れてください" id="email" name="email" type="text" class="validate" value="{{ old('email') }}">
              <label for="email">メールアドレス（管理者のみ必須）</label>
              <div id="email_err" class="error red-text" style="float: left;">{{$errors->first('email')}}</div>
            </div>
          </div>

          <div class="col s12">
            <div class="input-field col s12 m8">
              <input placeholder="パスワードをここに入力（半角英数字10桁以上）" id="password" name="password" type="text" class="validate" value="{{ old('password', 'Aaaa123456') }}">
              <label for="password">パスワード　※英字の大/小文字、数字全て使用</label>
              <div id="password_err" class="error red-text" style="float: left;">{{$errors->first('password')}}</div>
            </div>
          </div>

        </div>

        <button id="btn_entry" type="submit" class="waves-effect waves-light btn col s8 m4 btn-large orange darken-1 tooltipped" data-html="true" data-position="right" data-tooltip="管理者/テストユーザを新規登録します。">
          <i class="material-icons right">save</i>登録
        </button>
      </form>
@endif

    </div>  
  </div>

</div>
</main>-->
@include('layout.t_copyright2')
@yield('copyright2')
@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="{{asset('js/admin_regist.js') }}"></script>
<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
