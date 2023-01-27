{{-- ログイン画面 --}}
@extends('renewal.layout.app')

{{-- page informations --}}
@section('title', 'ログイン')
@section('description', 'グランデータご契約者さま専用マイページへのログインはこちらからご利用いただけます。')

{{-- css --}}
@section('pageCss')
<link href="{{asset('css/renewal/common.css') }}" rel="stylesheet">
<link href="{{asset('css/renewal/login.css') }}" rel="stylesheet">
@endsection

{{-- body_header --}}
{{-- @include('renewal.layout.bodyheader') --}}

{{-- body_contents --}}
@section('content')

<div class="l-main login-box">
    <div class="login">
        <img class="logo" src="img/logo.svg" alt="granData">
        <h1>マイページログイン</h1>
    </div>

    <div class="form-area">
        <form id="login" action="{{ route('authenticate') }}" method="post">
            <input type="hidden" name="cid" id="cid" value="{{ session('cid') }}">
            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
            @php
            $password = old('password');
            if(Cookie::get('memoried_id') <> '') {
                $csutomerCode = Cookie::get('memoried_id');
                $memoryIdCheck = 'checked';
            } else {
                $csutomerCode = old('customer_code');
                $memoryIdCheck = '';
            }
            @endphp
            <div class="input-field login-input">
                <label>
                    <p>ID</p>
                    <input type="text" name='customer_code' id='customer_code' value="{{ $csutomerCode }}">
                </label>
            </div>
            <div class="input-field login-input">
                <label>
                    <p>パスワード</p>
                    <input type="password" name='password' id='password' value="{{ $password }}">
                </label>
            </div>
            <a>
                <input type="checkbox" name="memory_id" value="1" {{ $memoryIdCheck }}>次回からIDの入力を省略する
            </a>
            {{-- エラー START --}}
            @if($errors->any())
            <div>
                <ul>
                    @foreach($errors->all() as $error)
                    <li class="help-block text-red text-bold">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            {{-- エラー END --}}
            <div class="input-field">
                <button type="submit" name="btn-login">
                    ログイン<img src="img/exit_to_app_black.svg">
                </button>
            </div>
            <div class="input-field input-field-link reminder">
                <button type="button" class="js-reminder-button">
                    ▶︎ID・パスワードを忘れた方はこちら
                </button>
            </div>
        </form>
    </div>
    <div class="input-field input-field-link faq">
        <p>
            <a href="https://grandata-service.jp/faq/#0501" target="_blank">
                ログインに関するよくある質問
            </a>
        </p>
    </div>
</div>

@endsection


{{-- load js --}}
@section('pageJs')
<script src="{{asset('js/renewal/common.js') }}"></script>
<script src="{{asset('js/renewal/login.js') }}"></script>
@endsection

{{-- load modal --}}
@section('modal')
    @include('renewal.modal.j_confirm.password_reminder')
    @include('renewal.modal.layer_board.exist_multiple_contracts')
    {{-- @include('renewal.modal.layer_board.create_mypage_account') --}}
    {{-- @include('renewal.modal.layer_board.password_reminder_complete') --}}
@endsection

{{-- footer --}}
@section('footer')
@include('renewal.layout.footer_login')
@endsection

