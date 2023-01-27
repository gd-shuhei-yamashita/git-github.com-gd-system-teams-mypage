{{-- ホーム画面 --}}
@extends('renewal.layout.app')

@section('title', '［WiMAX］配達日時の変更')

{{-- load css --}}
@section('pageCss')
<link href="{{asset('css/renewal/common.css') }}" rel="stylesheet">
<link href="{{asset('css/renewal/delivery.css') }}" rel="stylesheet">
@endsection

{{-- body_header --}}
@include('renewal.layout.bodyheader')

{{-- body_contents --}}
@section('content')

<div class="l-main">
    <h2>［WiMAX］配達日時の変更</h2>

    <div class="payment_method">
        <div class="title">
            現在の配達予定日時：{{ $date }}
            <p>{{ $time }}</p>
        </div>
        <br>
        <div class="link-btn">
        @if (!Session::get('wifi_delivery_date_change_url') || strtotime(Session::get('wifi_delivery_date')) < strtotime('+7 day'))
        <button type="button" disabled>配達日時を変更する</button>
        @else
        <button type="button"
            onclick="window.open('{{ Session::get('wifi_delivery_date_change_url') }}', '_blank', 'noreferrer')">配達日時を変更する</button>
        @endif
        </div>
    </div>
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

