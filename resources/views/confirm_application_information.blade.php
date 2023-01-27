{{-- 契約情報の確認画面 --}}
@extends('layout.t_common')

@section('title','契約情報の確認')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 3)
@section("cate2", 1)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')


  <!-- 契約情報の確認 -->
 
  <div class="l-main" name="#">
@if (session('user_login.role') < 5)
  <div class="section red-text">【注意】管理者権限でユーザー画面を閲覧しております</div>
@endif    

<!-- ユーザ情報 -->
        <h2>契約情報<div class=" h2-border"></div></h2>
            <div class="info-area">
                <p>ユーザー情報</p>
                <dl>
                    <dt>お客様名</dt>
                    <dd>{{ session('user_now.name') }}</dd>
                    <dt>メールアドレス</dt>
                    <dd>{{ session('user_now.email') }}</dd>
                    <dt>ご連絡先電話番号</dt>
                    <dd>{{ session('user_now.phone') }}</dd>
                    {{-- HMWR-2528対応 --}}
                    @if(session('user_now.customer_code') == 'MC01075465')
                    <dt>契約・請求書送付先住所</dt>
                    <dd>東京都渋谷区恵比寿１丁目１３番地６号　第２伊藤ビル５０３</dd>
                    @endif
                </dl>
            </div>

<!-- /ユーザ情報 -->

<!-- 申込情報一覧 -->
@php
  $contracts_count = 0;
@endphp
@forelse ($contracts as $contract)
<?php $contracts_count++; ?>
    <div class="h2-border info-border1"></div>
    <div class="info-area info1">
        <p>契約情報 {{ $contracts_count }}</p>
        <dl>
            <dt>契約名義</dt>
            <dd>{{ $contract['contract_name'] }}</dd>
            <dt>住所</dt>
            <dd>{{ $contract['address'] }}</dd>
            <dt>契約プラン名</dt>
            <dd>{{ $contract['plan'] }}</dd>
            @if(!empty($contract['option']))
            @php
                $option_count = 0;
            @endphp
            @foreach($contract['option'] as $option)
            @if($option_count == 0)
            <dt>オプション名</dt>
            @else
            <dt></dt>
            @endif
            <dd>
            {{ $option->name }}
            @if($option->status == 3)
                <button class="option-disable-btn" type="button" disable>解約済み</button>
            @else
                <button class="option-close-btn" type="button" onclick="location.href='{{ route('option_close', ['option_contract_id' => $option->id]) }}'">解約する</button>
            @endif
            </dd>
            @php
            $option_count++;
            @endphp
            @endforeach
            @endif
            @if($contract['thankyou_letter'])
            <dt></dt>
            <dd><a href="{{ route('thankyou_letter', ['supplypoint_code' => $contract['supplypoint_code'] == '' ? 'none' : $contract['supplypoint_code'] ]) }}" class="border_link">契約のお知らせ（契約締結後書面）</a></dd>
            @if($contract['futai_premiumu'])
            <dt></dt>
            <dd><a href="pdf/ABEMAでんき_クーポンコード入力案内_211025.pdf" class="border_link">ABEMAプレミアム会員 クーポンコードの入力案内</a></dd>
            @endif
            @if($contract['futai_basic'])
            <dt></dt>
            <dd><a href="pdf/【S01-02】smartcinema_A3_2ori_210903.pdf" class="border_link">スマートシネマのご案内</a></dd>
            @endif
            @if($contract['futai_entame'])
            <dt></dt>
            <dd><a href="pdf/【S01-02】smartcinema_A3_2ori_210903.pdf" class="border_link">スマートシネマのご案内</a></dd>
            @endif
            @if($contract['futai_digital'])
            <dt></dt>
            <dd><a href="pdf/【S02-02】smartcinema_A3_2ori_210903.pdf" class="border_link">スマートシネマのご案内</a></dd>
            @endif
            @if($contract['futai_douga'])
            <dt></dt>
            <dd><a href="pdf/【S02-02】smartcinema_A3_2ori_210903.pdf" class="border_link">スマートシネマのご案内</a></dd>
            @endif
            @endif
            <!-- 表示内容検討のため一時非表示 -->
            <!-- <dt></dt> -->
            <!-- <dd><a href="{{ route('contract_renewal_detail', ['supplypoint_code' => $contract['supplypoint_code'] == '' ? 'none' : $contract['supplypoint_code'] ]) }}" class="border_link">契約締結書面</a></dd> -->
        </dl>
    </div>


@empty
    <p>申込情報なし</p>
@endforelse
<!-- /申込情報一覧 -->
@include('layout.t_copyright2')
@yield('copyright2')


      </div>

@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="{{asset('js/style.js') }}"></script>

@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
