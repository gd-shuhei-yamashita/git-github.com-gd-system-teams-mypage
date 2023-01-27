@extends('errors.layouts.base')

@section('title', 'サービス メンテナンス中')
{{-- サービス利用不可 --}}
{{-- 503 Service Unavailable --}}

@section('message', 'このページへは事情によりアクセスできません。')
{{-- このページへは事情によりアクセスできません。 --}}
{{-- You can not access this page due to circumstances. --}}

@section('detail', 'サービスが一時的に過負荷やメンテナンスで使用不可能な状態です。')
{{-- サービスが一時的に過負荷やメンテナンスで使用不可能な状態です。 --}}
{{-- Service is temporarily unusable due to overload or maintenance. --}}

@section('link')
<p><a href="{{ config('app.url', '/') }}">ホーム &gt;&gt;</a></p>
@endsection
