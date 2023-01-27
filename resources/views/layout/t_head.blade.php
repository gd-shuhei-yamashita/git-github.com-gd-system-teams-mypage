@section('head')
<meta charset="utf-8">
<title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>
<meta name="description" content="@yield('description')">
<meta name="author" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- favicon -->
<link rel="icon" href="{{asset('img/icon/favicon_32_32.png') }}" sizes="32x32" />
<link rel="icon" href="{{asset('img/icon/favicon_192_192.png') }}" sizes="192x192" />
<script src="https://kit.fontawesome.com/d6027630b2.js" crossorigin="anonymous"></script>

<!--Import Google Icon Font-->
<!--<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">-->
<!-- Compiled and minified CSS -->
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">-->

<!-- <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/ui-lightness/jquery-ui.css"> -->
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/12.0.0/nouislider.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css">-->
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/angular-css/1.0.8/angular-css.min.js">-->
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/multiple-select/1.2.2/multiple-select.min.css">-->

@yield('pageCss')
@endsection