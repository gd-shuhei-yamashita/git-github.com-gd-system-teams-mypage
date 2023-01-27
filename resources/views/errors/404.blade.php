@extends('errors.layouts.base')

@section('title', '404 Not Found')

@section('message', '該当アドレスのページを見つける事ができませんでした。')
{{-- 該当アドレスのページを見つける事ができませんでした。 --}}
{{-- The page of the corresponding address could not be found.--}}

@section('detail', "お客様がご利用中のメールシステムによってURLが途中で改行され、リンクが最後まで正常に選択されていない可能性があります。\nその場合、URLを最後までコピーいただいたうえで、ページ上部のアドレスバー（https://～のURLが表示されている部分）に貼り付けし、アクセスをお試しください。")
{{-- サーバーは要求されたリソースを見つけることができなかったことを示します。 URLのタイプミス、もしくはページが移動または削除された可能性があります。 トップページに戻るか、もう一度検索してください。 --}}
{{-- The server indicates that it could not find the requested resource.\n A typo in the URL, or the page may have been moved or deleted.\n Please go back to the top page or search again. --}}
@section('link')
  <p><a href="{{ config('app.url', '/') }}">ホーム &gt;&gt;</a></p>
@endsection