@section('body_header')
<header>
    @php
        $haschild = '';
        // 一般ユーザ(role=9>5) で、親子関係1以上あれば表示する
        if (count( Session::get('user_now_parent_child', []) ) == 0 || Session::get('user_now.role') < 5 ) {
            $haschild = ' style=display:none';
        }
    @endphp
    <button type="button" class="burger js-btn">
        <span class="btn-line"></span>
    </button>
    <nav class="navi">
        <ul class="menu">
            <li><a class="menu-list" href="{{ route('home') }}"><i class="fa-solid fa-house"></i>ホーム</a></li>
            @if (Session::get('user_now.role') < 5)
                @include('renewal.layout.bodyheader_menu_admin')
            @else
                @include('renewal.layout.bodyheader_menu')
            @endif
            <li class="logout">
                @if( Session::get('parent_user.role')  )
                    <a class="menu-list" href="{{ route('parent_child_peek_logout') }}?customer_code={{Session::get('user_now.customer_code')}}">
                        <i class="fa-solid fa-right-from-bracket"></i>ログアウト(関連アカウント)
                    </a>
                @elseif( Session::get('user_login.role') != Session::get('user_now.role') )
                    <a class="menu-list" href="{{ route('search_application_information_peek_logout') }}?customer_code={{Session::get('user_now.customer_code')}}">
                        <i class="fa-solid fa-right-from-bracket"></i>ログアウト(覗き見解除)
                    </a>
                @else
                    <a class="menu-list" href="{{ route('logout') }}">
                        <i class="fa-solid fa-right-from-bracket"></i>ログアウト
                    </a>
                @endif
            </li>
        </ul>
    </nav>
    <p>
        {{ Session::get('user_now.name') }}　様
        @if( Session::get('parent_user.role')  )
            (親から子を確認中)
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
