{{-- 請求金額・使用量状況 詳細 の確認画面 --}}
@extends('layout.t_common')

@section('title','請求金額・使用量 詳細')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/print.css') }}" rel="stylesheet" type="text/css" media="print">

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 3)
@section("cate2", 0)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')
<div class="main">
    <div class="l-main" name="#">
        <h2 class="h2-detail">
            <a href="{{ route('confirm_usagedata') }}?date={{ $_GET['date'] }}">
                <img src="/img/chevron_upward_black.svg">
            </a>
            請求金額詳細
            <!--<div class="h2-border h2-border-detail"></div>-->
        </h2>
        @if (session('user_login.role') < 5)
        <div class="section red-text browser_margin">【注意】管理者権限でユーザー画面を閲覧しております</div>
        @endif
        <div class="detail">
            <div class="detail-area detail-area-1">
                <div class="h2-border detail-border1"></div>
                <p>請求内訳</p>
                <dl>
                    <dt>利用者名</dt>
                    <dd>{{ $billing["contract_name"] }}</dd>
                    <dt>住所</dt>
                    <dd>{{ $billing["address"] }}</dd>
                    @if($service != 'wifi')
                    <dt>供給地点番号</dt>
                    <dd>{{ $billing["supplypoint_code"] }}</dd>
                    @endif
                    @forelse ($billing_itemize as $billing_itemize_tmp)
                    <dt>{{ $billing_itemize_tmp["itemize_name"] }}</dt>
                    <dd class="right-align">{{ number_format($billing_itemize_tmp["itemize_bill"],2) }}円
                        @if ($billing_itemize_tmp["note"])
                        <span>{{ $billing_itemize_tmp["note"] }}</span>
                        @endif
                    </dd>
                    @empty
                    <dt></dt>
                    <dd>申込情報なし</dd>
                    @endforelse
                    <dt>合計</dt>
                    <dd>{{ number_format($billing["billing_amount"]) }} 円</dd>
                </dl>
            </div>

            <div class="detail-area detail-area-2">
                <div class="h2-border detail-border1"></div>
                <p>請求情報詳細</p>
                <dl>
                    <dt>請求年月</dt>
                    <dd>{{ $billing["billing_date"] }}{{-- 2019/03/01 --}}</dd>
                    <dt>請求額</dt>
                    <dd>{{ number_format($billing["billing_amount"]) }} 円 {{-- --円 --}}</dd>
                    <dt>内消費税相当額</dt>
                    <dd>{{ number_format($billing["tax"]) }} 円 {{-- --円 --}}</dd>
                    <dt>契約プラン</dt>
                    <dd>{{ $billing["plan"] }}{{-- - --}}</dd>
                    <dt>利用期間</dt>
                    <dd>{{ $billing["start_date"] }}　～　{{ $billing["end_date"] }}{{-- --　～　-- --}}</dd>
                    @if($service != 'wifi')
                    <dt>使用量</dt>
                    <dd>{{ $billing["usage"] }}{{-- 132.5 --}}</dd>
                    <dt>検針月日</dt>
                    <dd>{{ $billing["metering_date"] }}{{-- 2019/02/25 --}}</dd>
                    <dt>次回検針予定日</dt>
                    <dd>{{ $billing["next_metering_date"] }}{{-- 2019/03/26 --}}</dd>
                    <dt>当月指示数</dt>
                    <dd>{{ $billing["main_indicator"] }}{{-- 2349.8 --}}</dd>
                    <dt>前月指示数</dt>
                    <dd>{{ $billing["main_indicator_last_month"] }}{{-- 2217.3 --}}</dd>
                    <dt>差引き</dt>
                    <dd>{{ $billing["difference"] }}{{-- 132.5 --}}</dd>
                    <dt>計器乗率</dt>
                    <dd>{{ $billing["meter_multiply"] }}{{-- - --}}</dd>
                    @endif
                    <dt>当月お支払い予定日</dt>
                    <dd>{{ $billing["payment_date"] }}{{-- ご契約のクレジットカード会社に準拠 --}}</dd>
                    @php
                        $local_payment_type=["(-0-)","口座振替","クレジットカード","コンビニ払い","(-4-)","銀行窓口","(-6-)","(-7-)"];
                    @endphp
                    <dt>支払い方法</dt>
                    <dd>{{ $local_payment_type[ $billing["payment_type"] ] }}{{-- 未設定 --}}</dd>
                </dl>
            </div>


        </div>
            <div class="link-btn link-btn-detail">
                <a href="{{ route('receipt_pdf', ['supplypoint_code' => $billing['supplypoint_code'], 'date' => $billing['billing_date']]) }}"><button type="button">領収書出力<img
                        src="/img/file_download_black.svg"></button></a>
            </div>
            <div class="link-btn link-btn-detail">
                <a href="{{ route('specification_pdf', ['supplypoint_code' => $billing['supplypoint_code'], 'date' => $billing['billing_date']]) }}"><button type="button">明細出力<img
                        src="/img/file_download_black.svg"></button></a>
            </div>
        <div class="link-btn link-btn-detail btn_display ">
            <button type="button" onclick="$('iframe').css('display', 'none');window.print();$('iframe').css('display', 'block');">
                ページを印刷する<img src="/img/file_download_black.svg">
            </button>
        </div>
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
