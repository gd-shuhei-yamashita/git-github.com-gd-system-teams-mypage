{{-- ホーム画面 --}}
@extends('layout.t_common')

@section('title','ホーム')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">
<script src="https://kit.fontawesome.com/d6027630b2.js" crossorigin="anonymous"></script>

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 0)
@section("cate2", 0)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')
<div class="main">
    <div class="news-pc">
        <div class="news-area">
            <p>各種お知らせ</p>
            <ul id="result1_list">
                <li><span class="date">----/--/--</span><a href="#">読み込み中...</a></li>
                <li><span class="date">----/--/--</span><a href="#">読み込み中...</a></li>
                <li><span class="date">----/--/--</span><a href="#">読み込み中...</a></li>
            </ul>
            <div class="news-link">
                <a href="https://grandata-service.jp/news/" target="_blank" rel="noopener noreferrer">全て表示</a>
                <img src="img/open_in_new_black.svg">
            </div>
            <input type="hidden"name="display_number" value="3">
        </div>
    </div>

    <div class="l-main home">
        <h2>
            ホーム<div class="h2-border h2-border-home"></div>
        </h2>
        @if(session('supplypoint_code_undefined_flg'))
        <div class="section data_request">【データ取得中】反映まで今暫くおまちください。</div>
        @endif
        @if (session('user_login.role') == 1)
        <div class="section red-text">【注意】オペレーション操作者が不明になる恐れがあるので、システム管理者権限を常用して運用保守は行わないでください。</div>
		@endif

        @include('renewal.home.delivery_wifi')

        <input type="hidden" id="bill_month" value="">
        <input type="hidden" id="month" value="">
        <div class="home-use">
            <input type="hidden" id="first_billing_date" value="">
            <input type="hidden" id="latest_billing_date" value="">
            <a id="claim">----年--月請求分</a>
            <div class="other-month" id="other-month">
                <a href="#" id="bill_last_month">先月</a>
                <img src="img/code_black.svg">
                <a href="#" id="bill_next_month">翌月</a>
            </div>
        </div>

        <table class="home_total">
            <tr>
                <th>契約サービス<br>合計請求金額（税込）</th>
                <th class="amount">---円</th>
            </tr>
        </table>
        <div class="home-use">
            <a id="claim">契約中のサービス(請求分内訳)</a>
        </div>
        <table class="service">
            <tr>
                <td><i class="fa-regular fa-lightbulb"></i>---</td>
                <td>
                    <div class="t_flex">
                        ---円
                        <div class="link-btn">
                            <button type="button" onclick="location.href='detail.html'" disabled>
                                詳細<img src="img/arrow_right_black.svg">
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td><i class="fa-solid fa-fire"></i>---</td>
                <td>
                    <div class="t_flex">
                        ---円
                        <div class="link-btn">
                            <button type="button" onclick="location.href='detail.html'" disabled>
                                詳細<img src="img/arrow_right_black.svg">
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td><i class="fa-solid fa-wifi"></i>---</td>
                <td>
                    <div class="t_flex">
                        ---円
                        <div class="link-btn">
                            <button type="button" onclick="location.href='detail.html'" disabled>
                                詳細<img src="img/arrow_right_black.svg">
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td><i class="fa-solid fa-gear"></i>---</td>
                <td>
                    <div class="t_flex">
                        ---円
                        <div class="link-btn">
                            <button type="button" onclick="location.href='detail.html'" disabled>
                                詳細<img src="img/arrow_right_black.svg">
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <h2 class="goannai">
            ご案内<div class="h2-border h2-border-home"></div>
        </h2>

        <div class="homecp">
            <p>
                電気料金のお支払いに使えるクレジットカードが発行できます。
                発行は最短5分で完了！
                新規入会時のお得な特典もありますのでぜひご利用ください。
            </p>
            <div class="banner">
                <div class="bnbox">
                    <a href="https://www.saisoncard.co.jp/creditcard/extended/scdvm/?cd=14V1&sd=251&mi=1000060">VISA、mastetcard を発行する
                        <img src="img/banner/S-2105-025-VM-2_700x350_2000.png" class="nopc">
                        <img src="img/banner/S-2105-025-VM-2_700x350_2000.png" class="nosp">
                    </a>
                </div>
                <div class="bnbox">
                    <a href="https://www.saisoncard.co.jp/amextop/pearl-pro0-14v3-2000cb/?cd=14V3&sd=080&mi=1000200">AMERICAN EXPRESS CARD を発行する
                        <img src="img/banner/v3_700x350_2000.jpg" class="nopc">
                        <img src="img/banner/v3_700x350_2000.jpg" class="nosp">
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="news-sp">
        <div class="h2-border-home2"></div>
        <div class="news-area">
            <div class="news-area-ttl">
                <p>各種お知らせ</p>
                <div class="news-link">
                    <a href="https://grandata-service.jp/news/" target="_blank"
                        rel="noopener noreferrer">全て表示</a><img src="img/open_in_new_black.svg">
                </div>
            </div>
            <ul id="result1_list">
                <li><span class="date">----/--/--</span><br><a href="#">読み込み中...</a></li>
                <li><span class="date">----/--/--</span><br><a href="#">読み込み中...</a></li>
                <li><span class="date">----/--/--</span><br><a href="#">読み込み中...</a></li>
            </ul>
        </div>
    </div>
@include('layout.t_copyright2')
@yield('copyright2')
</div>




@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')


<script src="{{asset('js/style.js') }}"></script>
<script src="{{asset('js/home.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
