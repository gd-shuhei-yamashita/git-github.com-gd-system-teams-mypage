{{-- ユーザ管理メニュー --}}
<div class="js-admin_user_menu">
    <div class="layer_board_bg"></div>
    <div class="layer_board">
        {{-- ここに内容を記載 --}}
        <div class="userManagement">
            <h3 class="userManagement__title">ユーザ管理メニュー</h3>
            <section class="userManagement__infomations">
                <span class="text-bold">ID:</span> <span class="js-edit-userid">0</span>
                /
                <span class="text-bold">お客様名:</span> <span class="js-edit-username">名前</span>
            </section>
            <section class="userManagement__edit">
                <div>
                    <span class="text-bold">削除フラグ</span>
                    <div class="switch">
                        <label>
                            Off
                            <input type="checkbox" name="deleted_at" class="js-deleted_at">
                            <span class="lever"></span>
                            On
                        </label>
                        <span class="js-edit-deleted_at default-value"></span>
                    </div>
                </div>
                <div>
                    <span class="text-bold">初回認証</span>
                    <div class="switch">
                        <label>
                            Off
                            <input type="checkbox" name="email_verified_at" class="js-email_verified_at">
                            <span class="lever"></span>
                            On
                        </label>
                        <span class="js-edit-email_verified_at default-value"></span>
                    </div>
                </div>
            </section>
        </div>
        <div class="">
            <button type="button" class="js-user-submit userManagement__submit">反　映</button>
            <button type="button" class="js-user-cancel userManagement__cancel btn_close">キャンセル</button>
        </div>

        {{-- <a href="#" class="btn_close">閉じる</a> --}}
    </div>
</div>