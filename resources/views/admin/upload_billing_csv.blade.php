{{-- 請求データCSVアップロード画面 --}}
@extends('layout.t_common')

@section('title','請求データCSVアップロード')

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
  <!-- 請求データ取込 -->
    <!-- <div class="section">内訳データ.csv</div> -->
    <div class="row content header">

      <form method="POST" action="{{ route('store_billingdata')}}" enctype="multipart/form-data" style="display:inline-block;">
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        <input type="file" id="file" name="file[]" class="form-control" multiple required>
        <button type="submit">アップロード</button>
      </form>

      <button id="execute-billing" onclick="location.href='/admin/batch/billingdata'">取込実行</button>

      <div>
      @foreach ($files as $file)
        <p>{{ $file }} </p>
      @endforeach
      </div>
    </div>
  </div>

</div>
</main>
@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<!-- <script src="{{asset('js/admin_capture_items.js') }}"></script>
<script src="{{asset('js/entry.js') }}"></script> -->
<script src="{{asset('js/batch.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
