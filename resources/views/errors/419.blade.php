@extends('errors.layouts.base')

@section('title', '419 CSRF error')

@section('message', 'リクエストにエラーがあります。')
{{-- リクエストにエラーがあります。 --}}
{{-- Invalid request. --}}

@section('detail', "このレスポンスは、構文が無効であるため\nサーバーがリクエストを理解できないことを示します。\n画面を戻ってブラウザをリロードされてください。")
{{-- このレスポンスは、構文が無効であるためサーバーがリクエストを理解できないことを示します。 --}}
{{-- This response indicates that the server can not understand the request because the syntax is invalid. --}}

@section('link')
  <p><a href="{{ config('app.url', '/') }}">ホーム &gt;&gt;</a></p>
@endsection
