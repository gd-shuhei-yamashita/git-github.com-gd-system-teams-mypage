<footer>
    {{-- コピーライト --}}
    <div>
        <p>Copyright Grandata 2021 All rights reserved</p>
        <a class="pagetop_btn js-pagetop" href="#"></a>
        @include('renewal.layout.chatbot')
    </div>
    {{-- javascript --}}
    <script src="{{asset('js/renewal/vendor.js') }}"></script>
    @yield('pageJs')
</footer>