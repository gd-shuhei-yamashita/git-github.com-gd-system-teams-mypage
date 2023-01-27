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
    <h2>お支払い方法の登録・変更<div class=" h2-border"></div></h2>

    <div class="payment_top">
        <a href="{{ route('payment_method_electric_gas') }}">
            <p><i class="fa-regular fa-lightbulb"></i><i class="fa-solid fa-fire"></i></p>
            電気・ガスの支払い方法を登録・変更する
        </a>
        @if($wifi_flag)
        {{-- <a href="{{ route('payment_method_wifi') }}">
            <p><i class="fa-solid fa-wifi"></i></p>
            WiMAXの支払い方法を変更する
        </a> --}}
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