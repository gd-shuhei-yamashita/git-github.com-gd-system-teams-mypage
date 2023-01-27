
{{--　ログイン画面 | ID・パスワードを忘れた方はこちら --}}
<div class="jconfirm-contents js-password-reminder">
    <div class="confirm-area">
        <form id="password_reminder_form" action="">
            <div class="input-field wide">
                <label>
                    携帯電話番号 <span class="attention">※グランデータに登録中の電話番号をご入力ください。</span>
                    <input type="text" class="js-reminder-modal-phone" name="phone_text" placeholder="ハイフン無し">
                </label>
            </div>
            <div class="input-field">
                <label>
                    生年月日
                    <div>
                        <select class="js-reminder-modal-y" name="year_text"><option>選択してください</option></select><span class="birthdayUnit">年</span><br class="br-sp">
                        <select class="js-reminder-modal-m" name="month_text"><option>選択してください</option></select><span class="birthdayUnit">月</span><br class="br-sp">
                        <select class="js-reminder-modal-d" name="day_text"><option>選択してください</option></select><span class="birthdayUnit">日</span><br class="br-sp">
                    </div>
                </label>
            </div>
        </form>
    </div>
    <div class="confirm-area-message disabled">
        <i class="confirm-area-message-icon"></i>
        <span class="js-reminder-modal-message"></span>
    </div>
    <a class="confirm-area-contact disabled" href="https://grandata-service.jp/contact/" target="_blank" rel="noopener noreferrer">
        SMSが届かない場合は、<br class="br-sp">こちらからお問い合わせください。
    </a>
    <div class="confirm-area-buttons">
        <button type="button" class="btn confirm-area-buttons-submit">SMSを送信する</button>
        <button type="button" class="btn confirm-area-buttons-cancel">キャンセル</button>
    </div>
</div>
