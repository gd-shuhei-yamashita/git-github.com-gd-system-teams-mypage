<li><a class="menu-list" href="{{ route('confirm_usagedata') }}"><i class="fa-solid fa-yen-sign"></i>使用量・請求金額</a></li>
{{-- <li><a class="menu-list" href="{{ route('payment_status') }}"><i class="fa-solid fa-coins"></i>お支払い状況</a></li> --}}
<li><a class="menu-list" href="{{ route('confirm_application_information') }}"><i class="fa-solid fa-user"></i>契約情報</a></li>
<li><a class="menu-list" href="{{ route('payment_method') }}"><i class="fa-regular fa-credit-card"></i>お支払い方法の登録・変更</a></li>
@if (Session::get('wifi_delivery_date') && strtotime(Session::get('wifi_delivery_date')) > strtotime(date('Y-m-d 23:59:59')) )
<li><a class="menu-list" href="{{ route('delivery_wifi') }}"><i class="fa-solid fa-truck"></i>WiMAXの配達日変更</a></li>
@endif
<li><a class="menu-list" href="{{ route('password_change') }}"><i class="fa-solid fa-unlock-keyhole"></i>パスワード変更</a></li>
<li><a class="menu-list" href="{{ route('change_email_address') }}"><i class="fa-solid fa-at"></i>メールアドレス登録・変更</a>
{{-- <li><a class="menu-list" href="https://grandata-service.jp/info-change/" target="_blank" rel="noopener noreferrer"><img src="/img/email_black.svg">契約内容の変更</a> --}}
<li><a class="menu-list" href="https://grandata-service.jp/faq/" target="_blank" rel="noopener noreferrer"><i class="fa-regular fa-circle-question"></i>よくある質問</a>
<li><a class="menu-list" href="{{ route('contract_information') }}"><i class="fa-solid fa-circle-info"></i>約款情報</a></li>
<li {{$haschild}}><a class="menu-list" href="{{ route('parent_child') }}"><i class="fa-solid fa-users"></i>複数契約情報</a></li>
<li class="menu-list line"></li>
<li><a class="menu-list" href="{{ route('contract_close') }}"><i class="fa-regular fa-file-lines"></i>解約・引っ越しの手続き</a></li>
