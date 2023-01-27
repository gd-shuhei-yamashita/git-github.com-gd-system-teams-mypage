{{-- 請求系CSVアップロードメニュー画面 --}}
@extends('layout.t_common')

@section('title','請求系CSVアップロードメニュー')

@section('pageCss')
<link href="{{asset('css/common.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/mypage.css') }}" rel="stylesheet" type="text/css">

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 1)
@section("cate2", 2)
@include('layout.t_bodyheader')

{{-- body_contents --}}
@section('content')
<main>
<div class="container">
  <div class="col s12">
  <!-- 内訳データ取込 -->
    <!-- <div class="section">内訳データ.csv</div> -->
    <div class="row">
      <a href="/admin/upload/usagedata">使用量データ</a>
      <!-- <button onclick="location.href='/admin/upload/billingdata'">取込実行</button>
      <button onclick="location.href='/admin/upload/meisaidata'">取込実行</button> -->
    </div>
    <div class="row">
      <a href="/admin/upload/billingdata">請求データ</a>
    </div>
    <div class="row">
      <a href="/admin/upload/meisaidata">内訳データ</a>
    </div>  
  </div>

</div>
</main>
@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<!-- <script src="{{asset('js/admin_capture_items.js') }}"></script>
<script src="{{asset('js/entry.js') }}"></script> -->
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
