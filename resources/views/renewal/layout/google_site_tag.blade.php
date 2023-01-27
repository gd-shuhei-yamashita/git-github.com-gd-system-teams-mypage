@if(config('app.env') == 'product' && Session::get('user_now.role') == 9)
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-546HPE1VN9"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-546HPE1VN9');
        gtag('config', 'UA-217463400-1');
    </script>
@endif