{{-- 申込情報変更画面 --}}
@extends('layout.t_common')

@section('title','申込情報変更')

@section('pageCss')
<link href="{{asset('css/common.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/mypage.css') }}" rel="stylesheet" type="text/css">
@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 2)
@section("cate2", 5)
@include('layout.t_bodyheader')

{{-- body_contents --}}
@section('content')
<main>
<div class="container">
  <div class="col s12">
  <!-- 契約情報の確認 -->
    <div class="section">・申込情報を表示する<br/>
    ・表示する情報は申込情報として日次で登録された情報<br/>
    ・情報変更はマリーの改修が発生するため今回スコープ外<br/>
    </div>
    <div class="row content header">
      <form id="submit_section" action="https://s-okawa.knt.bod-develop.com/admin/organizations/section" method="post">    
        <div class="row">
          <div class="col s12">
            <div class="input-field col s8">
              <input placeholder="ここにひもづけをしたい 譲渡元顧客IDを入れてください" id="first_name" type="text" class="validate">
              <label for="first_name">譲渡元顧客ID</label>
            </div>
          </div>

          <div class="col s12">
            <div class="input-field col s8">
              <input placeholder="ここにひもづけをしたい 譲渡先顧客IDを入れてください" id="first_name" type="text" class="validate">
              <label for="first_name">譲渡先顧客ID</label>
            </div>
          </div>

        </div>
        <button id="btn_entry" type="submit" class="waves-effect waves-light btn col s4 btn-large orange darken-1 tooltipped" data-html="true" data-position="right" data-tooltip="アップロードしたデータで上書きします。">
          <i class="material-icons right">save</i>登録
        </button>
      </form>

    </div>  
  </div>

</div>
</main>
@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="{{asset('js/entry.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
