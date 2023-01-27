{{-- 契約更新のお知らせ --}}
@extends('layout.t_common')

@section('title','契約更新のお知らせ')

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
<main>
    <div class="main">
        <div class="l-main">
            <h2>契約更新のお知らせ（契約締結後書面）<div class=" h2-border"></div></h2>
            <div class="input-field">
                @if(!empty($data))
                    <div class="text_r">公開日：{{$data['delivery_date']}}</div><br>
                    <br>
                    この度はお申込みいただき、誠にありがとうございます｡<br>
                    ご契約をさせていただきますので、契約情報ページより申込内容をご確認いただきますようお願い申し上げます。<br>
                    現在のご契約につきまして更新時期となりましたのでお知らせいたします。<br>
                    <br>
                    解約・ご契約内容の変更・現在のご契約内容詳細のご確認につきましては、<br>
                    下記、カスタマーセンターまでご連絡いただきますようお願いいたします。<br>
                    解約の意思表示がない場合、同一条件で1年間継続するものといたします。<br>
                    <br>
                    ご契約内容の詳細につきましては<a href="{{ route('confirm_application_information') }}" class="border_link">契約情報</a>にてご確認ください。<br>
                    <br><br>
                    〈カスタマーセンター〉<br>
                    0570-070-336(受付時間10：00～18：00)<br>
                @else
                    お知らせはありません
                @endif
            </div>
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
