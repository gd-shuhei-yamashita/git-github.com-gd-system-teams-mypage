{{--　ログイン画面 | ID・パスワードを忘れた方はこちら | 契約はあるがマイページの登録がまだ？ --}}
<div class="js-create_mypage_account" id="layer_board_area_multiple">
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