{{-- ホーム画面 --}}
@extends('renewal.layout.app')

@section('title', 'ホーム')

{{-- load css --}}
@section('pageCss')
<link href="{{asset('css/renewal/common.css') }}" rel="stylesheet">
<link href="{{asset('css/renewal/home.css') }}" rel="stylesheet">
@endsection

{{-- body_header --}}
@include('renewal.layout.bodyheader')

{{-- body_contents --}}
@section('content')
<div>

    {{-- 各種お知らせPC版 --}}
    <section id="notices_pc">
        @include('renewal.home.notices_pc')
    </section>

    <div class="l-main home">
        <h2>ホーム</h2>
        @if(session('supplypoint_code_undefined_flg'))
        <div class="section data_request">【データ取得中】反映まで今暫くおまちください。</div>
        @endif
        @if (session('user_login.role') == 1)
        <div class="section data_request text-default">
            <b class="text-red text-bold">【注意】</b>オペレーション操作者が不明になる恐れがあるので、システム管理者権限を常用して運用保守は行わないでください。
        </div>
		@endif

        {{-- wifi配送情報 --}}
        @include('renewal.home.delivery_wifi')

        {{-- 請求情報 --}}
        <section id="billing_informations">
            @include('renewal.home.billing_informations', [
                'billingDate' => '------',
                'totalAmount' => '---',
                'contracts' => [
                    ['plan'=>'---', 'billing_message' => '---円', 'type' => 'electric'],
                    ['plan'=>'---', 'billing_message' => '---円', 'type' => 'gas'],
                    ['plan'=>'---', 'billing_message' => '---円', 'type' => 'mobile'],
                    ['plan'=>'---', 'billing_message' => '---円', 'type' => 'option']
                ]
            ])
        </section>

        {{-- ご案内 --}}
        <section class="invitation">
            <h2>ご案内</h2>

            <div class="invitation-contents">
                <p class="text-default">
                    トラノコとの提携を開始しました！グランデータから「トラノコ」を始めると投資資金2,000円分をプレゼントします。
                </p>
                <div class="bannerbox">
                    <div class="banner">
                        <a href="https://toranoko.com/go/grandata/?utm_source=Grandata&utm_medium=Mail&utm_campaign=TT_others_Grandata_Mail_Web_Both_221021_01_CID00915">
                            「トラノコ」でコツコツ投資を始める
                            <img src="img/banner/toranoko_221021_01.png" alt="">
                        </a>
                    </div>
                </div>
            </div>

            <div class="invitation-contents">
                <p class="text-default">
                    電気料金のお支払いに使えるクレジットカードが発行できます。
                    発行は最短5分で完了！
                    新規入会時のお得な特典もありますのでぜひご利用ください。
                </p>
                <div class="bannerbox">
                    <div class="banner">
                        <a href="https://www.saisoncard.co.jp/creditcard/extended/scdvm/?cd=14V1&sd=251&mi=1000060">
                            VISA、mastetcard を発行する
                            <img src="img/banner/S-2105-025-VM-2_700x350_2000.png" alt="">
                        </a>
                    </div>
                    <div class="banner">
                        <a href="https://www.saisoncard.co.jp/amextop/pearl-pro0-14v3-2000cb/?cd=14V3&sd=080&mi=1000200">
                            AMERICAN EXPRESS CARD を発行する
                            <img src="img/banner/v3_700x350_2000.jpg" alt="">
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- 各種お知らせSP版 --}}
    <section id="notices_sp">
        @include('renewal.home.notices_sp')
    </section>
</div>
@endsection

{{-- load js --}}
@section('pageJs')
<script src="{{asset('js/renewal/common.js') }}"></script>
<script src="{{asset('js/renewal/home.js') }}"></script>
@endsection

{{-- footer --}}
@section('footer')
@include('renewal.layout.footer_login')
@endsection

