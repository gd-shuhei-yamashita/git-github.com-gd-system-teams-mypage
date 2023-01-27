{{-- ログイン画面 --}}
@extends('layout.t_common')

{{-- @section('title','ログイン') --}}

@section('pageCss')
<link href="{{asset('css/common.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/mypage.css') }}" rel="stylesheet" type="text/css">
@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}

{{-- body_contents --}}
@section('content')

@if (session('status'))
<script>
window.onload = function() {
  M.toast({html: '{{ session('status') }}'});
  console.log('Windows onloaded');
}
</script>
@endif
<main style="padding-left: 0px;">
<nav>
  <div class="nav-wrapper white text-darken-4 center-align">
    <a href="#" class="brand-logo"><a href="#" class="logo-container">
@include('layout.t_logo')
@yield('logo')
    </a>
  </div>
</nav>
<div class="section"></div>
<div class="section"></div>
<div class="container center">
  <!-- エラーメッセージ -->
  <div class="z-depth-1 grey lighten-4 row  error-wrap" style="display: inline-block; padding: 32px 48px 32px 48px; border: 1px solid #EEE;">
    <section>
      <h1>@yield('title')</h1>
      <p class="error-message">@yield('message')</p>
      <p class="error-detail"><?php echo nl2br($__env->yieldContent('detail')); ?></p>
      @yield('link')
    </section>
  </div>
  <!-- /エラーメッセージ -->
  <br/>
  <br/>
  <br/>
  <br/>

  <!-- メッセージ -->
  <div class="row">
  </div>
  <!-- /メッセージ -->

</div>

<div class="section"></div>
</main>

@include('layout.t_bodyfooter')
@yield('body_footer')
@endsection

@section('pageJs')
<script src="{{asset('js/entry.js') }}"></script>
<script src="{{asset('js/reminder.js') }}"></script>
@endsection

@include('layout.t_footer')
