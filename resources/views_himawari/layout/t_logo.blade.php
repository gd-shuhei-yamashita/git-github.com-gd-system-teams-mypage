{{-- ロゴ画像 --}}
@section('logo')
<img src="{{asset( config('const.TitleLogo') ) }}" alt="Logo" title="{{ config('const.TitleName') }}" >
@endsection
