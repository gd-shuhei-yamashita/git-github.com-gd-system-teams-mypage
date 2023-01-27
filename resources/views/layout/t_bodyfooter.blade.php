@section('body_footer')
<footer class="page-footer " style="background-color: #8e773c; padding-left: 0px;">
  <div class="container">
    <div class=" row" style="width: 100%;">
      <div class="col s6 m6 left-align">
        <a class="grey-text text-lighten-4 right" href="https://grandata-service.jp/" target="_blank">サービスサイト</a>
      </div>
      <div class="col s6 m6 left-align">
        <a class="grey-text text-lighten-4 left" href="https://grandata-service.jp/contact/" target="_blank">お問い合わせ</a>
      </div>
    </div>
  </div>
@include('layout.t_copyright')
@yield('copyright')
</footer>
@endsection
