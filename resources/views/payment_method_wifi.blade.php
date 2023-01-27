{{-- 支払い方法変更画面 --}}
@extends('layout.t_common')

@section('title','支払い方法の変更')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">
<script src="https://kit.fontawesome.com/d6027630b2.js" crossorigin="anonymous"></script>
@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')

<div class="l-main">
    <h2>
        <p><i class="fa-solid fa-wifi"></i>［WiMAX］</p>支払い方法の変更<div class=" h2-border"></div>
        <div class=" h2-border"></div>
    </h2>
    <div class="payment_method">
        @if(empty($data))
        支払い方法の情報が取得できませんでした。
        @else
        <div class="title">
            現在の支払い方法：クレジットカード
        </div>
        <br>
        <div class="link-btn">
            <button type="button" onclick="window.open('{{ $data['modify_url'] }}', '_blank', 'noreferrer')">変更する</button>
        </div>
        @endif
    </div>

@include('layout.t_copyright2')
@yield('copyright2')
</div>





@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>

<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
