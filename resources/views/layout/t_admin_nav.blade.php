  <!-- dropdown1 -->
  <ul id="dropdown1" class="dropdown-content">
    <li><a href="#!" class="submenu_password">パスワード変更</a></li>
    <li><a href="#!" class="submenu_personal">個人設定</a></li>
    @if ($users['user_type'] == 1 or $users['user_type'] == 9)
    <li><a href="#!" class="submenu_user">ユーザ</a></li>
    @endif
    <!--<li class="divider"></li>-->
  </ul>

  <!-- nav -->
  <nav>
    <div class="nav-wrapper">
      <a href="#!" class="brand-logo"></a>
      <a href="#!" data-target="mobile-nav" class="sidenav-trigger"><i class="material-icons">menu</i></a>
      <ul class="right hide-on-med-and-down">
        @if ($users['user_type'] == 1 or $users['user_type'] == 9)
        <li><a href="/admin/organizations">主催者</a></li>
        @endif
        <li><a href="/admin/tours">申込</a></li>
        @if ($users['user_type'] == 1 or $users['user_type'] == 9)
        <li><a href="/admin/sites">テンプレート</a></li>
        @endif
        <li><a href="#!" class="menu_logout">ログアウト</a></li>
        <li><a class="dropdown-trigger" href="#!" data-target="dropdown1" style="width: 200px;"><i class="material-icons right">more_vert</i></a></li>
      </ul>
    </div>
  </nav>

  <!-- sidenav -->
  <ul class="sidenav" id="mobile-nav">
    @if ($users['user_type'] == 1 or $users['user_type'] == 9)
    <li><a href="/admin/organizations">主催者</a></li>
    @endif
    <li><a href="/admin/tours">申込</a></li>
    @if ($users['user_type'] == 1 or $users['user_type'] == 9)
    <li><a href="/admin/sites">テンプレート</a></li>
    @endif
    <li class="divider"></li>
    <li><a href="#!" class="submenu_password">パスワード変更</a></li>
    <li><a href="#!" class="submenu_personal">個人設定</a></li>
    @if ($users['user_type'] == 1 or $users['user_type'] == 9)
    <li><a href="#!" class="submenu_user">ユーザ</a></li>
    @endif
    <li class="divider"></li>
    <li><a href="#!" class="menu_logout">ログアウト</a></li>
  </ul>
