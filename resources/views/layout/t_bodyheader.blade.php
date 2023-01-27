@section('body_header')
@php
  $now_place = \Route::current() -> getName();
  //echo print_r($phase,1);
  
  // コントローラーに合わせて開閉
  $cate1 = $__env->yieldContent('cate1');
  $cate2 = $__env->yieldContent('cate2');
  
  // 管理者、ユーザによる権限別表示
  if (Session::get('user_now.role') < 5) {
    $usertype = 1; // 1:admin 0:user
    $isuser  = " hidden";
    $isadmin = "";
    if (Session::get('user_now.role') == 1) {
      $issystem = ""; // 表示を許す
    } else {
      $issystem = " hidden";
    }
  } else {
    $usertype = 0;
    $isuser  = "";
    $isadmin = " hidden";
    $issystem = " hidden";
  };

  $haschild = "";
  // 一般ユーザ(role=9>5) で、親子関係1以上あれば表示する
  if (Session::get('user_now.role') < 5 || count( Session::get('user_now_parent_child', []) ) == 0){
    $haschild = " hidden"; // 隠す
  }
  // タブを開いている場所をアクティブにする
  $c0 = ( $cate1 == 0 ) ? " active" : "";
  $c1 = ( $cate1 == 1 ) ? " active" : "";
  $c2 = ( $cate1 == 2 ) ? " active" : "";
  $c3 = ( $cate1 == 3 ) ? " active" : "";

  $cc00 = ( $cate1 == 0 && $cate2 == 0) ? ' active' : "";
  $cc01 = ( $cate1 == 0 && $cate2 == 1) ? ' active' : "";
  $cc02 = ( $cate1 == 0 && $cate2 == 2) ? ' active' : "";
  $cc03 = ( $cate1 == 0 && $cate2 == 3) ? ' active' : "";

  $cc10 = ( $cate1 == 1 && $cate2 == 0) ? ' active' : "";
  $cc11 = ( $cate1 == 1 && $cate2 == 1) ? ' active' : "";
  $cc12 = ( $cate1 == 1 && $cate2 == 2) ? ' active' : "";
  $cc13 = ( $cate1 == 1 && $cate2 == 3) ? ' active' : "";
  $cc14 = ( $cate1 == 1 && $cate2 == 4) ? ' active' : "";

  $cc20 = ( $cate1 == 2 && $cate2 == 0) ? ' active' : "";
  $cc21 = ( $cate1 == 2 && $cate2 == 1) ? ' active' : "";
  $cc22 = ( $cate1 == 2 && $cate2 == 2) ? ' active' : "";
  $cc23 = ( $cate1 == 2 && $cate2 == 3) ? ' active' : "";
  $cc24 = ( $cate1 == 2 && $cate2 == 4) ? ' active' : "";
  $cc25 = ( $cate1 == 2 && $cate2 == 5) ? ' active' : "";
  $cc26 = ( $cate1 == 2 && $cate2 == 6) ? ' active' : "";
  $cc27 = ( $cate1 == 2 && $cate2 == 7) ? ' active' : "";
  $cc28 = ( $cate1 == 2 && $cate2 == 8) ? ' active' : "";

  $cc30 = ( $cate1 == 3 && $cate2 == 0) ? ' active' : "";
  $cc31 = ( $cate1 == 3 && $cate2 == 1) ? ' active' : "";
  $cc32 = ( $cate1 == 3 && $cate2 == 2) ? ' active' : "";

  
@endphp
  <header>
    <!-- navbar -->
    <nav class="navbar ">
      <div class="nav-wrapper white text-darken-4">
@if (session()->get('db_accesspoint_now', '0') == 2)
        <a href="#!" class="brand-logo orange-text"><h5 class="resp">&nbsp; @yield('title') .</h5></a>
@else
        <a href="#!" class="brand-logo grey-text"><h5 class="resp">&nbsp; @yield('title') </h5></a>
@endif
        <!-- <a href="#" data-target="mobile-side" class="btn-floating btn-large sidenav-trigger red"><i class="material-icons">menu</i></a> -->
        <a href="#" data-target="mobile-side" class="btn sidenav-trigger orange darken-1"><i class="material-icons">menu</i></a>
        <ul id="nav-mobile" class="right hide-on-med-and-down">
<!--
          <li class=""><a href="#"><i class="material-icons left grey-text">notifications</i></a></li>
          <li class=""><a href="#"><i class="material-icons left grey-text">help</i></a></li>
-->
        </ul>
      </div>
    </nav>
    <!-- /navbar -->

    <!-- sidenav -->
    <ul class="sidenav sidenav-fixed" id="mobile-side"> <!--  amber lighten-5 -->
<!-- titlelogo -->

      <li style="height:60px;"><a href="{{ route('home') }}" class="logo-container">
@if (session()->get('db_accesspoint_now', '0') == 2)
<img src="{{asset( config('const.TitleLogo2') ) }}" alt="Logo" title="{{ config('const.ServiceName2') }}" style="width:100%">
@else
<img src="{{asset( config('const.TitleLogo') ) }}" alt="Logo" title="{{ config('const.ServiceName') }}" style="width:100%">
@endif
      </a></li>

<!-- titlelogo -->
      <li class=""{{$isuser}}>
      <!-- ログアウト/覗き見解除 -->
        <a href="{{ route('confirm_application_information') }}" class="chip light-green lighten-4"><i class="material-icons left">person</i>{{ mb_strimwidth(Session::get('user_now.name') , 0, 12, "…", 'UTF-8') }} 様
@if( Session::get('parent_user.role')  )
        <i class="material-icons right brown-text tooltipped" data-html="true" data-position="down" data-tooltip="親から子を確認しています">group</i>
@else
@if( Session::get('user_login.role') != Session::get('user_now.role') )
        <i class="material-icons right red-text tooltipped" data-html="true" data-position="down" data-tooltip="覗き見モードです">visibility</i>
@endif
@endif
        </a>
      </li>
      <li class=""{{$isadmin}}>
@if (Session::get('user_now.role') == 1) 
        <a href="{{ route('confirm_application_information') }}" class="chip pulse"><i class="material-icons left">star</i>{{ Session::get('user_now.name') }} [system] </a>
@else
        <a href="{{ route('confirm_application_information') }}" class="chip amber"><i class="material-icons left">security</i>{{ Session::get('user_now.name') }} [管理者] </a>
@endif
{{-- DB_PLACEMENT が マルチタイプ かつ 管理者のみにメニュー表示 --}}
@if (config('const.DBPlacement') == 'multi_master'  && $usertype == 1)
          <li class=""><a href="{{ route('toggle_db') }}" id="change_db"><i class="material-icons left orange-text" title="データベースの切替">switch_camera</i>
<span class="grey-text">{{( session()->get('db_accesspoint_now', '0') == 2) ? "個人へ切替" : "企業へ切替" }}</span>
          </a></li>
@endif
      </li>

      <li class="no-padding  orange darken-1">
        <ul class="collapsible collapsible-accordion">
          <li class="{{ $cc00 }}"><a class="collapsible-header waves-effect waves-light" href="{{ route('home') }}"><i class="material-icons left">home</i>ホーム画面</a></li>
          <!-- EU -->
          <li class="bold{{ $c3 }}"{{$isuser}}><a class="collapsible-header waves-effect waves-light" tabindex="0"><i class="material-icons left">timeline</i>契約内容・請求確認<i class="material-icons chevron right">chevron_left</i></a>
            <div class="collapsible-body">
              <ul>
                <li class="{{ $cc30 }}"><a href="{{ route('confirm_usagedata') }}"><i class="material-icons left">payment</i>請求金額・使用量</a></li>
                <li class="{{ $cc31 }}"><a href="{{ route('confirm_application_information') }}"><i class="material-icons left">assignment</i>契約情報</a></li>
              </ul>
            </div>
          </li>
          <!-- /EU -->

          <!-- 共通 -->
          <li class="bold{{ $c0 }}"><a class="collapsible-header waves-effect waves-light" tabindex="0"><i class="material-icons left">settings</i>アカウント<i class="material-icons chevron right">chevron_left</i></a>
            <div class="collapsible-body">
              <ul>
                <li class="{{ $cc01 }}"><a href="{{ route('password_change') }}"><i class="material-icons left">vpn_key</i>パスワード変更</a></a></li>
                <li class="{{ $cc02 }}"{{$isuser}}><a href="{{ route('change_email_address') }}"><i class="material-icons left">alternate_email</i>メールアドレス変更</a></a></li>
                <li class="{{ $cc03 }}"{{$haschild}}><a href="{{ route('parent_child') }}"><i class="material-icons left">group</i>複数契約情報</a></a></li>
              </ul>
            </div>
          </li>
          <!-- /共通 -->
          
{{-- 管理者ユーザのみにタグを表示する --}}
@if ($usertype == 1)
          <!-- データ連携 -->
          <li class="bold{{ $c1 }}"{{$isadmin}}><a class="collapsible-header waves-effect waves-light" tabindex="0"><i class="material-icons left">import_contacts</i>データ連携<i class="material-icons chevron right">chevron_left</i></a>
            <div class="collapsible-body">
              <ul>
                <li class="{{ $cc13 }}"><a href="{{ route('capture_application_information') }}"><i class="material-icons left">people</i>申込情報取込(顧客)</a></li>
                <li class="{{ $cc14 }}"><a href="{{ route('capture_application_information2') }}"><i class="material-icons left">add_location</i>申込情報取込(契約)</a></li>

                <li class="{{ $cc10 }}"><a href="{{ route('capture_usagedata') }}"><i class="material-icons left">data_usage</i>使用量データ取込</a></li>
                <li class="{{ $cc11 }}"><a href="{{ route('capture_billingdata') }}"><i class="material-icons left">payment</i>請求データ取込</a></li>
                <li class="{{ $cc12 }}"><a href="{{ route('capture_items') }}"><i class="material-icons left">assignment</i>内訳データ取込</a></li>

              </ul>
            </div>
          </li>
          <!-- /データ連携 -->

          <!-- 管理 -->
          <li class="bold{{ $c2 }}"{{$isadmin}}><a class="collapsible-header waves-effect waves-light" tabindex="0"><i class="material-icons left">domain</i>管理<i class="material-icons chevron right">chevron_left</i></a>
            <div class="collapsible-body">
              <ul>
                <li class="{{ $cc28 }}"><a href="{{ route('regist_notice') }}"><i class="material-icons left">message</i>お知らせ登録</a></li>
                <li class="{{ $cc24 }}"{{$issystem}}><a href="{{ route('regist_administrator') }}"><i class="material-icons left">how_to_reg</i>管理者/Tユーザ登録</a></li>
        
                <!-- <li class="{{ $cc25 }}"><span class="badge green lighten-4">mock</span><a href="{{ route('change_application_infomarion') }}"><i class="material-icons left">perm_identity</i>申込情報変更</a></li> -->
                <li class="{{ $cc26 }}"><a href="{{ route('search_application_information') }}"><i class="material-icons left">person_pin</i>申込情報検索</a></li>
                <li class="{{ $cc27 }}"><a href="{{ route('search_operation_history') }}"><i class="material-icons left">search</i>操作履歴検索</a></li>

                <li class="{{ $cc22 }}"><a href="{{ route('change_transfer') }}"><i class="material-icons left">transfer_within_a_station</i>譲渡変更</a></li>
                <li class="{{ $cc23 }}"><a href="{{ route('integration_customer_id') }}"><i class="material-icons left">call_merge</i>マイページID統合</a></li>
                <li class="{{ $cc20 }}"><a href="{{ route('change_customer_id_linkage') }}"><i class="material-icons left">call_missed_outgoing</i>マイページID紐付変更</a></li>
                <li class="{{ $cc21 }}"><a href="{{ route('change_supplypoint_linkage') }}"><i class="material-icons left">call_missed_outgoing</i>供特紐付変更</a></li>

              </ul>
            </div>
          </li>
          <!-- /管理 -->
@endif
{{-- /管理者ユーザのみにタグを表示する --}}
        </ul>
      </li>
      <!-- ログアウト/覗き見解除 -->

      <!-- route('parent_child_peek_logout') -->
@if( Session::get('parent_user.role')  )
      <li><a href="{{ route('parent_child_peek_logout') }}?customer_code={{Session::get('user_now.customer_code')}}" class="collapsible-header waves-effect waves-amber menu_logout"  tabindex="0"><i class="material-icons left">eject</i>ログアウト(関連アカウント)</a></li>
@else
@if( Session::get('user_login.role') != Session::get('user_now.role') )
      <li><a href="{{ route('search_application_information_peek_logout') }}?customer_code={{Session::get('user_now.customer_code')}}" class="collapsible-header waves-effect waves-amber menu_logout"  tabindex="0"><i class="material-icons left">eject</i>ログアウト(覗き見解除)</a></li>
@else
      <li><a href="{{ route('logout') }}" class="collapsible-header waves-effect waves-amber menu_logout"  tabindex="0"><i class="material-icons left">eject</i>ログアウト</a></li>
@endif
@endif
    </ul>
    <!-- /sidenav -->
  </header>
@endsection
