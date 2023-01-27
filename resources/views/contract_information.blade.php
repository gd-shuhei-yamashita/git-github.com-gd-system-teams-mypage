{{-- 約款情報 --}}
@extends('layout.t_common')

@section('title','約款情報')

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
            <h2>約款情報<div class=" h2-border"></div></h2>
            @php
            $contracts_count = 0;
            @endphp
            @foreach($url_data as $url)
            @php
            $contracts_count++;
            @endphp
            @if(!empty($url['denki_jusetu']))
            <div class="input-field txtlink">
                <p class="title">【契約情報 {{ $contracts_count }}】</p>
                <label class="service">でんき</label><br>
                @if(!empty($url['denki_name']))
                <a href={{ $url['denki_yakkan'] }} target="_blank" class="link-list">{{ $url['denki_name'] }} 利用約款</a><br>
                <a href={{ $url['denki_jusetu'] }} target="_blank" class="link-list">{{ $url['denki_name'] }} 重要事項説明</a><br>
                @if(!empty($url['denki_shubetsu']))
                <a href={{ $url['denki_shubetsu'] }} target="_blank" class="link-list">{{ $url['denki_name'] }} 契約種別説明書</a><br>
                @endif
                @else
                <a href={{ $url['denki_yakkan'] }} target="_blank" class="link-list">利用約款</a><br>
                <a href={{ $url['denki_jusetu'] }} target="_blank" class="link-list">重要事項説明</a><br>
                @if(!empty($url['denki_shubetsu']))
                <a href={{ $url['denki_shubetsu'] }} target="_blank" class="link-list">契約種別説明書</a><br>
                @endif
                @endif
            </div><br>
            @elseif(!empty($url['gas_jusetu']))
            <div class="input-field txtlink">
                <p class="title">【契約情報 {{ $contracts_count }}】</p>
                <label class="service">ガス</label><br>
                <a href={{ $url['gas_yakkan'] }} target="_blank" class="link-list">都市ガス 利用規約</a><br>
                <a href={{ $url['gas_jusetu'] }} target="_blank" class="link-list">都市ガス 重要事項説明</a><br>
                @if(!empty($url['gas_sasshi']))
                <a href={{ $url['gas_sasshi'] }} target="_blank" class="link-list">都市ガス 冊子</a><br>
                @endif
            </div><br>
            @endif
            @endforeach
        </div>
    </div>


    <footer>
        <p>Copyright Grandata 2021 All rights reserved</p>
        <a class="pagetop_btn js-pagetop"></a>
    </footer>

</main>
@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
