<head>
    <meta charset="utf-8">
    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>
    <meta name="description" content="@yield('description')">
    <meta name="author" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon -->
    <link rel="icon" href="{{asset('img/icon/favicon_32_32.png') }}" sizes="32x32">
    <link rel="icon" href="{{asset('img/icon/favicon_192_192.png') }}" sizes="192x192">
    <link rel="stylesheet" href="{{asset('css/renewal/vendor.css') }}">
    @yield('pageCss')
    <link href="{{asset('css/renewal/env/'.config('app.env').'.css') }}" rel="stylesheet">
    @include('renewal.layout.google_site_tag')
    @yield('head')
</head>
