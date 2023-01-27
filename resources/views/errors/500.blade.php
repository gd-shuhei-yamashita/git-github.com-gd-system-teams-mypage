@extends('errors.layouts.base')

@section('title', '500 サーバ内部エラー')
{{-- サーバ内部エラー --}}
{{-- 500 Internal Server Error --}}

@section('message', 'サーバー内部でエラーが発生しました。')
{{-- サーバー内部でエラーが発生しました。 --}}
{{-- An error occurred inside the server. --}}

@section('detail', "プログラムに文法エラーがあったり、設定に誤りがあった場合などに返されます。\n管理者へ連絡してください。")
{{-- プログラムに文法エラーがあったり、設定に誤りがあった場合などに返されます。管理者へ連絡してください。 --}}
{{-- It will be returned when there is a syntax error in the program, or there is an error in the setting. Please contact the administrator. --}}
