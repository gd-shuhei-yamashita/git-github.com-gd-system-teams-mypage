{{-- お支払い状況詳細画面 --}}
@extends('layout.t_common')

@section('title','お支払い状況詳細')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')

<div class="l-main home">

    <h2>
        お支払い状況<div class="h2-border h2-border-home"></div>
    </h2>

    <div class="status-area">
        <p>ご請求状況</p>
    </div>
    <p class="comment">※お支払い状況のステータスは反映までお時間がかかります。</p>
    <table class="payment_status">
        <tr>
            <td>ご請求年月日</td>
            <td><div class="t_flex">{{ $data['billig_data']['payment_date'] }}</div></td>
        </tr>
        <tr>
            <td>ご請求金額<br class="nopc">(税込)</td>
            <td><div class="t_flex">{{ number_format($data['billig_data']['billing_amount_total']) }}円</div></td>
        </tr>
        <tr>
            <td>お支払い方法</td>
            @php
                $local_payment_type=["(-0-)","口座振替","クレジットカード","コンビニ払い","(-4-)","銀行窓口","(-6-)","(-7-)"];
            @endphp
            <td><div class="t_flex">{{ $local_payment_type[ $data['billig_data']['payment_type'] ] }}</div></td>
        </tr>
        <tr>
            <td>お支払い状況</td>
            @if(empty($data['payment_check']['payment_amount']))
            @if($data['payment_check']['today'] - $data['payment_check']['billing_date'] >= 2)
            <td><div class="t_flex"><p class="status_flag"><span class="unpaid">未払い</span></p></div></td>
            @else
            <td><div class="t_flex"><p class="status_flag"><span class="confirm">確認中</span></p></div></td>
            @endif
            @elseif($data['payment_check']['payment_amount'] >= $data['payment_check']['billing_amount'])
            <td><div class="t_flex"><p class="status_flag"><span class="paid">支払済</span></p></div></td>
            @else
            <td></td>
            @endif
        </tr>
    </table>

    <div class="status-area">
        <p>ご契約中のサービス</p>
    </div>
    @foreach($data['detail_list'] as $detail)
    <table class="payment_status">
        <tr>
            <td>契約プラン</td>
            <td class="contract">{{ $detail['plan'] }}
                @if($detail['pps_type'] == 1 || $detail['pps_type'] == 5)
                <p class="service_flag"><i class="fa-regular fa-lightbulb"></i>電気</p>
                @elseif($detail['pps_type'] == 2 || $detail['pps_type'] == 3 || $detail['pps_type'] == 4)
                <p class="service_flag"><i class="fa-solid fa-fire"></i>ガス</p>
                @elseif(substr($detail['supplypoint_code'], 0, 2) == 'GP')
                <p class="service_flag"><i class="fa-solid fa-wifi"></i>WiMAX</p>
                @endif
            </td>
        </tr>
        <tr>
            <td>ご契約住所</td>
            <td class="contract">{{ $detail['address'] }}</td>
        </tr>
        <tr>
            <td>使用期間</td>
            <td class="contract">{{ $detail['start_date'] }}～<br class="nopc">{{ $detail['end_date'] }}</td>
        </tr>
        @if($detail['pps_type'] == 1 || $detail['pps_type'] == 5)
        <tr>
            <td>使用量</td>
            <td><div class="t_flex">{{ $detail['usage'] }}kWh</div></td>
        </tr>
        <tr>
            <td>検針日</td>
            <td class="contract">{{ $detail['metering_date'] }}</td>
        </tr>
        <tr>
            <td>次回検針予定日</td>
            <td class="contract">{{ $detail['next_metering_date'] }}</td>
        </tr>
        <tr>
            <td>ご請求金額</td>
            <td class="contract">{{ number_format($detail['billing_amount']) }}円</td>
        </tr>
        @elseif(substr($detail['supplypoint_code'], 0, 2) == 'GP')
        <tr>
            <td>ご請求金額</td>
            <td class="contract">{{ number_format($detail['billing_amount']) }}円</td>
        </tr>
        @elseif($detail['pps_type'] == 2 || $detail['pps_type'] == 3 || $detail['pps_type'] == 4)
        <tr>
            <td>使用量</td>
            <td><div class="t_flex">{{ $detail['usage'] }}</div></td>
        </tr>
        <tr>
            <td>検針日</td>
            <td class="contract">{{ $detail['metering_date'] }}</td>
        </tr>
        <tr>
            <td>次回検針予定日</td>
            <td class="contract">{{ $detail['next_metering_date'] }}</td>
        </tr>
        <tr>
            <td>ご請求金額</td>
            <td class="contract">{{ number_format($detail['billing_amount']) }}円</td>
        </tr>
        @endif
    </table>
    @endforeach

@include('layout.t_copyright2')
@yield('copyright2')
</div>


@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
<script src="https://kit.fontawesome.com/d6027630b2.js" crossorigin="anonymous"></script>

<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
