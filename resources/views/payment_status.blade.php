{{-- お支払い状況画面 --}}
@extends('layout.t_common')

@section('title','お支払い状況')

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
    @if(session('supplypoint_code_undefined_flg'))
        <div class="section data_request">【データ取得中】反映まで今暫くおまちください。</div>
    @endif
    <div class="status-area">
        <p>ご請求履歴一覧</p>
        <div class="status-plan">
            <div class="use1">
                <a>年選択</a>
                <div class="cp_ipselect cp_sl02" id="billing_year">
                    <select name="billing_year" class="">
                        <option value="">---------</option>
                        <option value="2022年">2022年</option>
                        <option value="2021年">2021年</option>
                        <option value="2020年">2020年</option>
                    </select>
                </div>
            </div>
            <div class="use1">
                <a>月選択</a>
                <div class="cp_ipselect cp_sl02" id="billing_month">
                    <select name="billing_month" class="">
                        <option value="0">-</option>
                        @for($i = 12; $i > 0; $i--)
                        <option value="{{sprintf('%02d', $i)}}">{{$i}}月</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
        <p class="comment">※お支払い状況のステータスは反映までお時間がかかります。</p>
    </div>
    <div id="status_list">
    </div>
@include('layout.t_copyright2')
@yield('copyright2')
</div>


@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>

<script src="{{asset('js/style.js') }}"></script>
<script src="{{asset('js/payment_status.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
