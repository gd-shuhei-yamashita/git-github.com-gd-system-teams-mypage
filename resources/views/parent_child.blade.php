{{-- 関連アカウント画面 --}}
@extends('layout.t_common')

@section('title','関連アカウント')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 0)
@section("cate2", 3)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')

        <div class="l-main" name="#">
            <h2>関連アカウント<div class=" h2-border"></div>
            </h2>

            <div class="info-area">
                <p>親子関係の顧客アカウント一覧を示します。子の顧客アカウントは、こちらの一覧からすべての操作が可能です。</p>
                <dl>
                  @php
                    $user_count = 0;
                  @endphp
                  @forelse ($users as $user)
                  @php
                    $user_count++;
                  @endphp
                        <dt>契約 {{ $user_count }} </dt>
                          <dd><a href="{{route('parent_child_users_peek')}}?customer_code={{ $user['child_customer_code'] }}">{{ $user['child_customer_code'] }}</a>
                            ({{ $user['user_name'] }} 様)
                          </dd>
                  @empty
                      <dt>申込情報なし</dt>
                  @endforelse				

                </dl>
            </div>

        </div>





<!-- 
<main>
<div class="container">
  <div class="col s12">
  <!-- 関連アカウント -- >
    <div class="section">親子関係の顧客アカウント一覧を示します。子の顧客アカウントは、こちらの一覧からすべての操作が可能です。</div>
    <div class="row content header">

<!-- 申込情報一覧 -- >
@php
  $user_count = 0;
@endphp
@forelse ($users as $user)
<?php // $user_count++; ?>
      <div class="row">
        <div class="col m12 s12"><h5><i class="material-icons left">account_circle</i>契約 {{ $user_count }} </h5></div>
        <div class="col m12 s6">
          <a href="{{route('parent_child_users_peek')}}?customer_code={{ $user['child_customer_code'] }}">{{ $user['child_customer_code'] }}</a>
        </div>

        <div class="col m12 s6">
        {{ $user['user_name'] }}
        </div>

      </div>
@empty
    <p>申込情報なし</p>
@endforelse
<!-- /申込情報一覧 -- >

    </div>  
  </div>

</div>
</main>-->
@include('layout.t_copyright2')
@yield('copyright2')
@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<!-- <script src="{{asset('js/entry.js') }}"></script> -->
<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
