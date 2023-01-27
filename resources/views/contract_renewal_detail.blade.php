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
            <h2>契約更新のお知らせ<div class=" h2-border"></div></h2>
            <div class="input-field">
                @if(!empty($data))
                    お客様の契約更新情報は以下となります。<br>
                    <br>
                    契約プラン名：{{ $data['plan'] }}<br>
                    <br>
                    契約年月日：{{ $data['switching_scheduled_date'] }}<br>
                    次回契約期間：{{ $data['next_contract_start'] }}～{{ $data['next_contract_end'] }}<br>
                    供給地点特定番号：{{ $data['supplypoint_code'] }}<br>
                    <br>
                    @switch($data['pps_business_number'])
                        @case('A0476')
                            小売電気事業者については<a href="https://grandata-service.jp/legal#gd_electricity" class="border_link">こちら</a>からご確認ください。<br>
                            @break
                        @case('A0172')
                            小売電気事業者については<a href="https://grandata-service.jp/legal#htb_electricity" class="border_link">こちら</a>からご確認ください。<br>
                            @break
                        @case('A0058')
                            小売ガス事業者については<a href="https://grandata-service.jp/legal#fnj_gus" class="border_link">こちら</a>からご確認ください。<br>
                            @break
                        @case('A0023')
                            小売ガス事業者については<a href="https://grandata-service.jp/legal#saisan_gus" class="border_link">こちら</a>からご確認ください。<br>
                            @break
                        @case('A0087')
                            小売ガス事業者については<a href="https://grandata-service.jp/legal#gd_gus" class="border_link">こちら</a>からご確認ください。<br>
                            @break
                        @default
                        <br>
                    @endswitch
                    <br>
                    今後ともよろしくお願いいたします。<br>
                @else
                    契約情報の詳細が取得できませんでした。
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
