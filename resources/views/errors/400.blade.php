@extends('errors.layouts.base')

@section('title', '400 Bad Request')

@section('message', 'リクエストにエラーがあります。')
{{-- リクエストにエラーがあります。 --}}
{{-- There is an error in the request. --}}

@section('detail', "このレスポンスは、構文が無効であるため\nサーバーがリクエストを理解できないことを示します。")
{{-- このレスポンスは、構文が無効であるためサーバーがリクエストを理解できないことを示します。 --}}
{{-- This response indicates that the server can not understand the request because the syntax is invalid. --}}
