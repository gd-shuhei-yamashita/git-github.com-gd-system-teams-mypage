{{-- ログイン画面 --}}
@extends('layout.t_common')

@section('title','ログイン')
@section('description','グランデータご契約者さま専用マイページへのログインはこちらからご利用いただけます。')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/modal.css') }}" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css" />

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}

{{-- body_contents --}}
@section('content')

<?php
  session(['cid' => $cid]);
?>
@if (session('status'))
<script>
window.onload = function() {
  M.toast({html: '{{ session('status') }}'});
  console.log('Windows onloaded');
}
</script>
@endif
    <div class="main">
        <!-- 　<header> -->
            <!-- <button type="button" class="burger js-btn">
                <span class="btn-line"></span>
            </button>
            <nav>
                <ul class="menu">
                    <li class="menu-list"><img src="img/home_black.svg">ホーム</li>
                    <li class="menu-list"><img src="img/yen_black.svg">使用量・請求金額</li>
                    <li class="menu-list"><img src="img/perm_identity_black.svg">契約情報</li>
                    <li class="menu-list"><img src="img/https_black.svg">パスワード変更</li>
                    <li class="menu-list"><img src="img/alternate_email_black.svg">メールアドレス変更</li>
                    <li class="menu-list logout"><img src="img/logout_black.svg">ログアウト</li>
                </ul>
            </nav>
            <p>契約者名　様</p> -->
        <!-- </header> -->
        <!-- モーダル追加認証 -->
        <div id="layer_board_area">
            <div class="layer_board_bg"></div>
            <div class="layer_board m_login">
                <div class="bggray"></div>

                <div class="tp">
                    <div class="check_ani">
                        <img src="img/mitouroku.png">
                    </div>
                    <div class="tp_txt">未登録のメールアドレスです。</div>
                    <p class="inpt_add">
                        ご入力いただいたメールアドレス：<br class="nopc"><span id="email"></span>
                    </p>
                    <input type="hidden" name="email" value="">
                </div>

                <div class="msg">
                    <img src="img/i_icon.png" class="i_icon">
                    <p class="i_txt">
                        ご入力のメールアドレスに誤りがないか、ご確認ください。
                        <br><br>
                        誤りがない場合、まだメールアドレスのご登録をいただけいない可能性がございます。
                        <strong>以下のご本人様確認情報をご入力いただき、仮パスワードを発行してください。</strong>
                    </p>

                    <form method="post" class="pass_form">
                        <p>携帯電話番号</p>
                        <input type="tel" name="phone_num" placeholder="例）090XXXXXXXX (※ハイフン無し)">

                        <p>ご契約者様の生年月日</p>
                        <div class="nen">
                            <select name="year">
                                <option value="">--</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </select>
                            <label>年</label>
                            <select name="month">
                                <option value="">--</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                            <label>月</label>
                            <select name="day">
                                <option value="">--</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                            <label>日</label>
                        </div>

                        <button type="button" id="email_regist">メールアドレスを登録して<br class="nopc">仮パスワードを発行する</button>
                    </form>

                </div>
                <div id="addtional_auth_err" class="error" style="float: left;color: #ff0000;font-weight: bolder;"></div>

                <a href="#" class="btn_close">閉じる</a>
            </div>
        </div>
        <!-- モーダル -->
        <!-- モーダル仮パス送信完了 -->
        <div id="layer_board_area_complete">
            <div class="layer_board_bg"></div>
            <div class="layer_board m_login">
                <div class="bggray"></div>

                <div class="tp">
                    <div class="check_ani">
                        <img src="img/sms_icon.gif">
                    </div>
                    <div class="tp_txt soushin">認証SMSを送信しました。</S></div>
                </div>

                <div class="complete">
                    <img src="img/f_icon.png" class="memo">
                    <p>
                        入力された携帯電話番号宛に、IDとパスワードの確認に必要な認証用のSMSを送信しました。<br>
                        SMSをご確認いただき、マイページのログイン情報をご確認ください。
                    </p>
                </div>

                <a href="#" class="btn_close">閉じる</a>
            </div>
        </div>
        <!-- モーダル仮パス送信完了 -->
        <!-- モーダル複数契約 -->
        <div id="layer_board_area_multiple">
            <div class="layer_board_bg"></div>
            <div class="layer_board m_login">
                <div class="bggray"></div>

                <div class="tp">
                    <div class="check_ani">
                        <img src="img/toiawase.png">
                    </div>
                    <div class="tp_txt">カスタマーセンターまでお問い合わせください。</div>
                </div>

                <div class="msg">
                    <img src="img/i_icon.png" class="i_icon">
                    <p class="i_txt">
                        ご入力いただいた「携帯電話番号」と「ご契約者様の生年月日」で照会をしたところ、
                        対象の契約を絞り込めませんでした。
                        <br><br>
                        オペレーターによる対応が必要な状況ですので、大変お手数をおかけしますが
                        <strong>カスタマーセンターまでお電話をいただきますようお願い申し上げます。</strong>
                    </p>
                    <div class="customer_num">
                        <div class="heiretu">
                            <img src="img/navi.png">
                            <div>
                                <a class="num" href="tel:0570-070-336">0570-070-336</a>
                                <p class="nosp">受付時間：10:00~18:00 年末年始を除く</p>
                            </div>
                        </div>
                        <p class="nopc time">受付時間：10:00~18:00 年末年始を除く</p>
                    </div>
                </div>

                <a href="#" class="btn_close">閉じる</a>
            </div>
        </div>
        <!-- モーダル複数契約 -->
        <div class="l-main login-box">
            <div class="login">
                <img src="img/logo.svg">
                <h1>マイページログイン</h1>
            </div>

            <div class="form-area">
                <form id="login" action="{{ route('login') }}" method="post">
                    <input type="hidden" name="cid" id="cid" value="{{ session('cid') }}" />
                    <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                    <div class="input-field login-input">
                        <label>
                            <p>ID</p>
                            @if(Cookie::get('memoried_id') <> '')
                            <input type="text" name='customer_code' id='customer_code' value="{{Cookie::get('memoried_id')}}">
                            @else
                            <input type="text" name='customer_code' id='customer_code' value="{{old('customer_code')}}">
                            @endif
                        </label>
                    </div>
                    <div class="input-field login-input">
                        <label>
                            <p>パスワード</p>
                            <input type="password" name='password' id='password' value="{{old('password')}}">
                        </label>
                    </div>
                    @if(Cookie::get('memoried_id') <> '')
                    <a><input type="checkbox" name="memory_id" value="1" checked>次回からIDの入力を省略する</a>
                    @else
                    <a><input type="checkbox" name="memory_id" value="1">次回からIDの入力を省略する</a>
                    @endif
                    {{-- バリデーションエラーを返す --}}
                    @if($errors->any())
                    <div>
                        <ul>
                            @foreach($errors->all() as $error)
                            <li class="help-block red-text" style="font-weight: bolder;">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="input-field">
                        <button type="submit" name="btn-login">ログイン<img src="img/exit_to_app_black.svg"></button>
                    </div>
                    <div class="input-field input-field-link reminder">
                        <p>
                            <a name="btn_password_remind" id="btn_password_remind" href="#!" class='submenu_reminder pink-text'>
                                ▶︎ID・パスワードを忘れた方はこちら
                            </a>
                        </p>
                    </div>
                </form>
            </div>
            <div class="input-field input-field-link faq">
                <p>
                    <a href="https://grandata-service.jp/faq/#0501" target="_blank">ログインに関するよくある質問</a>
                </p>
            </div>
        </div>

@include('layout.t_copyright2')
@yield('copyright2')
    </div>

<div class="section"></div>
</main>

@endsection

@section('pageJs')

<script src="{{asset('js/entry.js') }}"></script>
<script src="{{asset('js/reminder.js') }}"></script>
<script src="{{asset('js/style.js') }}"></script>
<script src="{{asset('js/vendor/jquery.layerBoard.js') }}"></script>
<script src="{{asset('js/modal.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>

@endsection

@include('layout.t_footer')
