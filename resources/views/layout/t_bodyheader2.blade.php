@section('body_header')
<header>
@php
  $haschild = "";
  // 一般ユーザ(role=9>5) で、親子関係1以上あれば表示する
  if (count( Session::get('user_now_parent_child', []) ) == 0 || Session::get('user_now.role') < 5 ){
    $haschild = " style=display:none";
  }
@endphp
            <button type="button" class="burger js-btn">
                <span class="btn-line"></span>
            </button>
            <nav>
                <ul class="menu">
                    <li class="menu-list"><a href="{{ route('home') }}"><i class="fa-solid fa-house"></i>ホーム</a></li>
@if (Session::get('user_now.role') < 5)
					<link rel="stylesheet" href="/css/admin.css">
                    <li class="menu-list"><a href="{{ route('password_change') }}"><img src="/img/https_black.svg">パスワード変更</a></li>
                    <li class="menu-list nohover">
                        <a><img src="/img/folder_black_24dp.svg">データ連携</a>
                    </li>
                        <ul>
                            <li class="list-child"><a href="{{ route('capture_application_information') }}">申込情報取込（顧客）</a></li>
                            <li class="list-child"><a href="{{ route('capture_application_information2') }}">申込情報取込（契約）</a></li>
                            <li class="list-child"><a href="{{ route('capture_usagedata') }}">使用量データ取込</a></li>
                            <li class="list-child"><a href="{{ route('capture_billingdata') }}">請求データ取込</a></li>
                            <li class="list-child"><a href="{{ route('capture_items') }}">内訳データ取込</a></li>
                        </ul>
                    <li class="menu-list nohover">
                        <a><img src="/img/manage_accounts_black_24dp.svg">管理</a>
                    </li>
                        <ul>
@if (Session::get('user_now.role') == 1)
                            <li class="list-child"><a href="{{ route('regist_notice') }}">お知らせ登録</a></li>
                            <li class="list-child"><a href="{{ route('regist_administrator') }}">管理者/T ユーザー登録</a></li>
@endif
                            <li class="list-child"><a href="{{ route('search_application_information') }}">申込情報検索</a></li>
                            <li class="list-child"><a href="{{ route('search_operation_history') }}">操作履歴検索</a></li>
                            <li class="list-child"><a href="{{ route('change_transfer') }}">譲渡変更</a></li>
                            <li class="list-child"><a href="{{ route('integration_customer_id') }}">マイページID 統合</a></li>
                            <li class="list-child"><a href="{{ route('change_customer_id_linkage') }}">マイページID 紐付変更</a></li>
                            <li class="list-child"><a href="{{ route('change_supplypoint_linkage') }}">供特紐付変更</a></li>
                        </ul>
                    <li class="menu-list"><a href="{{ route('contract_information') }}">約款情報</a>
@else
                    <li class="menu-list"><a href="{{ route('confirm_usagedata') }}"><i class="fa-solid fa-yen-sign"></i>使用量・請求金額</a></li>
                    {{-- <li class="menu-list"><a href="{{ route('payment_status') }}"><i class="fa-solid fa-coins"></i>お支払い状況</a></li> --}}
                    <li class="menu-list"><a href="{{ route('confirm_application_information') }}"><i class="fa-solid fa-user"></i>契約情報</a></li>
                    <li class="menu-list"><a href="{{ route('payment_method') }}"><i class="fa-regular fa-credit-card"></i>お支払い方法の登録・変更</a></li>
                    @if (Session::get('wifi_delivery_date') && strtotime(Session::get('wifi_delivery_date')) > strtotime(date('Y-m-d 23:59:59')) )
                    <li class="menu-list"><a href="{{ route('delivery_wifi') }}"><i class="fa-solid fa-truck"></i>WiMAXの配達日変更</a></li>
                    @endif
                    <li class="menu-list"><a href="{{ route('password_change') }}"><i class="fa-solid fa-unlock-keyhole"></i>パスワード変更</a></li>
                    <li class="menu-list"><a href="{{ route('change_email_address') }}"><i class="fa-solid fa-at"></i>メールアドレス登録・変更</a>
                    {{-- <li class="menu-list"><a href="https://grandata-service.jp/info-change/" target="_blank" rel="noopener noreferrer"><img src="/img/email_black.svg">契約内容の変更</a> --}}
                    <li class="menu-list"><a href="https://grandata-service.jp/faq/" target="_blank" rel="noopener noreferrer"><i class="fa-regular fa-circle-question"></i>よくある質問</a>
                    <li class="menu-list"><a href="{{ route('contract_information') }}"><i class="fa-solid fa-circle-info"></i>約款情報</a>
                    </li>
                    <li class="menu-list"{{$haschild}}><a href="{{ route('parent_child') }}"><i class="fa-solid fa-users"></i>複数契約情報</a></li>
                    <li class="menu-list line"></li>
                    <li class="menu-list"><a href="{{ route('contract_close') }}"><i class="fa-regular fa-file-lines"></i>解約・引っ越しの手続き</a></li>
@endif
@if( Session::get('parent_user.role')  )
					<li class="menu-list logout"><a href="{{ route('parent_child_peek_logout') }}?customer_code={{Session::get('user_now.customer_code')}}"><i class="fa-solid fa-right-from-bracket"></i>ログアウト(関連アカウント)</a></li>
@else
@if( Session::get('user_login.role') != Session::get('user_now.role') )
					<li class="menu-list logout"><a href="{{ route('search_application_information_peek_logout') }}?customer_code={{Session::get('user_now.customer_code')}}"><i class="fa-solid fa-right-from-bracket"></i>ログアウト(覗き見解除)</a></li>
@else
                    <li class="menu-list logout"><a href="{{ route('logout') }}"><i class="fa-solid fa-right-from-bracket"></i>ログアウト</a>
                    </li>
@endif
@endif
                </ul>
            </nav>
            <p>{{ Session::get('user_now.name') }}　様
@if( Session::get('parent_user.role')  )
        (親から子を確認中)
@else
@if( Session::get('user_login.role') != Session::get('user_now.role') )
        (覗き見モード)
@endif
@endif
@if (Session::get('user_now.role') < 5)
	@if (Session::get('user_now.role') == 1)
		[system]
	@else
		[管理者]
	@endif
@endif

			</p>
			<div id="download_temp_area"></div>
        </header>
@endsection
