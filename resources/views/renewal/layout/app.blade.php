<!DOCTYPE HTML>

@include('renewal.layout.head')

<body>
  <input id="application_url" type="hidden" name="app_url" value="{{ preg_replace("/^https?\:\/\/(.*?)\//", "/", asset('') ) }}">
  <input id="token" type="hidden" name="_token" value="{{ csrf_token() }}">
  <input id="status" type="hidden" name="_status" value="{{ session('status') }}">

  @yield('body_header')
  <main class="main">
    @if (session('user_login') && session('user_login.role') < 5)
      <div class="main-warning" title="管理者権限でユーザー画面を閲覧しております">
        <i class="fa-solid fa-triangle-exclamation"></i>覗き見モード
      </div>
    @endif
    @yield('content')
  </main>
  <div id="modal_resource">
    @yield('modal')
  </div>
  @yield('footer')
</body>
</html>