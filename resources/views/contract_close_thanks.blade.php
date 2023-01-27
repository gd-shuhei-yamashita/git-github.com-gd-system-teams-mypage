{{-- 契約解約申し込み完了画面 --}}
@extends('layout.t_common')

@section('title','解約手続き受付完了')

@section('pageCss')
<link href="{{asset('css/form.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')

<div class="main">
<div class="l-main" id="contract_close">
            <h2>解約・引っ越し手続き<div class="h2-border"></div>
            </h2>

            <div class="center">
                <h3>解約・引っ越し手続き<br class="nopc">受付完了</h3>

                <p>
                    解約手続きの受付が完了いたしました。<br>
                    ご登録のE-mailアドレス宛にメールを送信いたしましたのでご確認ください。<br>
                    お手続き内容に確認事項がございましたら5営業日以内にカスタマーセンターよりお電話させて頂きます。
                </p>

            </div>

            <div class="caution_red">
                <p class="txt_red">最終ご請求月は、<br class="nopc">解約月の最大2ヶ月後になります。</p>
                <p>他社サービスをお申し込みいただくと自動的に解約されます。</p>
                <div class="kome">
                    <p>※</p>
                    <p>引っ越し先でもご利用中のサービスを継続する場合は、お引っ越し（移転）のお手続きをさせていただきます。</p>
                </div>
                <div class="kome">
                    <p>※</p>
                    <p>最終ご利用日が本日より5営業日以内をご希望の場合、カスタマーセンターへご連絡の上ご解約手続きをお願いいたします。</p>
                </div>
            </div>

            <div class="center">
                <h3>ガスのみ・電気＋ガス<br class="nopc">のご解約・引っ越しの方</h3>
                <p class="txt_red red_thin">
                    ガスの解約には原則立ち合いが必要となります。<br>
                    お立ち合いの日程を調整させていただくため、<span>本手続きのみでは完了いたしません</span>のでご注意ください。<br>
                    本手続き完了後、カスタマーセンターより<span>5営業日以内</span>に折り返しご連絡いたします。
                </p>
                <p>
                    ※ガスの解約にはご契約者様又は代理人様にて現地のお立ち合いと日程等を確認させていただきます。
                </p>
            </div>

            <div class="caution">
                <h3 id="caution">注意事項</h3>
                <p>・メールの受信が確認できない場合、迷惑フォルダやメール受信設定などご確認くださいますようお願いいたします。</p>
                <p>・万が一、メールの受信が確認できない場合、恐れ入りますが専用窓口までご連絡ください。</p>
            </div>

            <div class="link-btn">
                <button type="button" onclick="location.href='{{ route('home') }}'" id="top">
                    マイページTopへ
                </button>
            </div>

            <div class="madoguichi">
                <div class="mado_left">
                    カスタマーセンター
                    <p>受付時間　10:00~18:00</p>
                </div>
                <div class="navi">
                    <div>ナビダイヤル<a href="tel:0570-070-366"><img src="img/phone.png">0570-070-366</a></div>
                    <p>お問い合わせが集中しており、大変お電話が繋がりにくくなっております。</p>
                </div>
            </div>

        </div>
        @include('layout.t_copyright2')
        @yield('copyright2')
    </div>
</div>

@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>

<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
