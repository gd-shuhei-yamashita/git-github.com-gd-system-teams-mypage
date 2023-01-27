<link rel="stylesheet" href="{{asset('/css/renewal/admin.css')}}">
<li><a class="menu-list" href="{{ route('password_change') }}"><i class="fa-solid fa-unlock-keyhole"></i>パスワード変更</a></li>
<li class="nohover">
    <a class="menu-list"><i class="fa-regular fa-folder"></i>データ連携</a>
</li>
<ul>
    <li class="list-child"><a href="{{ route('capture_application_information') }}">申込情報取込（顧客）</a></li>
    <li class="list-child"><a href="{{ route('capture_application_information2') }}">申込情報取込（契約）</a></li>
    <li class="list-child"><a href="{{ route('capture_usagedata') }}">使用量データ取込</a></li>
    <li class="list-child"><a href="{{ route('capture_billingdata') }}">請求データ取込</a></li>
    <li class="list-child"><a href="{{ route('capture_items') }}">内訳データ取込</a></li>
</ul>
<li class="nohover">
    <a class="menu-list"><i class="fa-solid fa-user-gear"></i>管理</a>
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
@if (config('app.env') === 'local')
<li class="nohover">
    <a class="menu-list"><i class="fa-solid fa-gear"></i>開発</a>
</li>
<ul>
    <li class="list-child"><a href="/develop/entry">Mallieデータ登録</a></li>
</ul>
@endif
<li class="menu-list line"></li>


