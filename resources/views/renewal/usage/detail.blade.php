{{-- 請求金額・使用量状況 詳細 の確認画面 --}}
@extends('renewal.layout.app')

@section('title','請求金額・使用量 詳細')

@section('pageCss')
<link href="{{asset('css/renewal/common.css') }}" rel="stylesheet">
<link href="{{asset('css/renewal/detail.css') }}" rel="stylesheet">
<link href="{{asset('css/renewal/print.css') }}" rel="stylesheet" type="text/css" media="print">
@endsection

{{-- body_header --}}
@include('renewal.layout.bodyheader')

{{-- body_contents --}}
@section('content')
<div class="l-main">
    <h1 class="title">
        <a href="{{ route('confirm_usagedata') }}?date={{ $_GET['date'] }}&supplypoint_code={{ $_GET['supplypoint_code'] }}">
            <i class="fa-solid fa-angle-left"></i>
        </a>
        請求金額詳細
    </h1>
    <div class="detail">
        <div class="detail-area detail-area-1">
            <div class="border"></div>
            <p>請求内訳</p>
            <dl>
                <dt>利用者名</dt>
                <dd>{{ $billing['contract_name'] }}</dd>
                <dt>住所</dt>
                <dd>{{ $billing['address'] }}</dd>
                @if($service != 'wifi')
                <dt>供給地点番号</dt>
                <dd>{{ $billing['supplypoint_code'] }}</dd>
                @endif
                @forelse ($billing_itemize as $billing_itemize_tmp)
                <dt>{{ $billing_itemize_tmp['itemize_name'] }}</dt>
                <dd class="right-align">{{ number_format($billing_itemize_tmp['itemize_bill'],2) }} 円
                    @if ($billing_itemize_tmp['note'])
                        <span class="note">{{ $billing_itemize_tmp['note'] }}</span>
                    @endif
                </dd>
                @empty
                <dt></dt>
                <dd>申込情報なし</dd>
                @endforelse
                <dt>合計</dt>
                <dd>{{ number_format($billing['billing_amount']) }} 円</dd>
            </dl>
        </div>

        <div class="detail-area detail-area-2">
            <div class="border"></div>
            <p>請求情報詳細</p>
            <dl>
                <dt>請求年月</dt>
                <dd>{{ substr($billing['billing_date'], 0, 4) }}年{{ substr($billing['billing_date'], 4, 2) }}月</dd>
                <dt>請求額</dt>
                <dd>{{ number_format($billing['billing_amount']) }} 円</dd>
                <dt>内消費税相当額</dt>
                <dd>{{ number_format($billing['tax']) }} 円</dd>
                <dt>契約プラン</dt>
                <dd>{{ $billing['plan'] }}</dd>
                <dt>利用期間</dt>
                <dd>{{ $billing['start_date'] }}　～　<br class="br-pc">{{ $billing['end_date'] }}</dd>
                @if($service != 'wifi')
                <dt>使用量</dt>
                <dd>{{ $billing['usage'] }} {!! $billing['usage_unit_html'] !!}</dd>
                <dt>検針月日</dt>
                <dd>{{ $billing['metering_date'] }}</dd>
                <dt>次回検針予定日</dt>
                <dd>{{ $billing['next_metering_date'] }}</dd>
                <dt>当月指示数</dt>
                <dd>{{ $billing['main_indicator'] }}</dd>
                <dt>前月指示数</dt>
                <dd>{{ $billing['main_indicator_last_month'] }}</dd>
                <dt>差引き</dt>
                <dd>{{ $billing['difference'] }}</dd>
                <dt>計器乗率</dt>
                <dd>{{ $billing['meter_multiply'] }}</dd>
                @endif
                <dt>当月お支払い予定日</dt>
                <dd>{{ $billing['payment_date'] }}</dd>
                @php
                    $local_payment_type=['(-0-)','口座振替','クレジットカード','コンビニ払い','(-4-)','銀行窓口','(-6-)','(-7-)'];
                @endphp
                <dt>支払い方法{{$billing['payment_type']}}</dt>
                <dd>{{ $local_payment_type[ $billing['payment_type'] ] }}</dd>
            </dl>
        </div>
    </div>

    {{-- <div class="link-btn link-btn-detail btn_display">
        <a href="{{ route('receipt_pdf', ['supplypoint_code' => $billing['supplypoint_code'], 'date' => $billing['usage_date']]) }}" download="">
            <button type="button">
                領収書出力
                <img src="/img/file_download_black.svg">
            </button>
        </a>
    </div> --}}
    {{-- @if (isset($downloadable) && $downloadable)
        <div class="link-btn link-btn-detail btn_display">
            <a href="{{ route('specification_pdf', ['supplypoint_code' => $billing['supplypoint_code'], 'date' => $billing['usage_date']]) }}" download="">
                <button type="button">
                    明細をダウンロードする
                    <img src="/img/file_download_black.svg">
                </button>
            </a>
        </div>
    @endif --}}
    {{-- <div class="link-btn link-btn-detail btn_display">
        <button type="button" onclick="window.print();">
            ページを印刷する<img src="/img/file_download_black.svg">
        </button>
    </div> --}}
</div>

@endsection

{{-- load js --}}
@section('pageJs')
<script src="{{asset('js/renewal/common.js') }}"></script>
@endsection

{{-- footer --}}
@section('footer')
@include('renewal.layout.footer_login')
@endsection
