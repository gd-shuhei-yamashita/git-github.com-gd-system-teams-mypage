@extends('errors.layouts.base')

@section('title', '401 Unauthorized')

@section('message', '認証に失敗しました。')
{{-- 認証に失敗しました。 --}}
{{-- certification failed. --}}

@section('detail', 'リクエストされたリソースを得るために認証が必要です。')
{{-- リクエストされたリソースを得るために認証が必要です。 --}}
{{-- Authentication is required to obtain the requested resource. --}}
