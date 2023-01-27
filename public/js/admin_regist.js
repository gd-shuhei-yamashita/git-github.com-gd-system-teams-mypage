/** リマインダー、パスワード変更 */
$(function() {
    /** 作業前にメールアドレスに記載がない場合、追記して処理を戻します */
    $('#btn_entry').on('click', function() {
        var role          = $('#role').val();
        var customer_code = $('#customer_code').val();
        var email         = $('#email').val();
        console.log("role:"          + role);
        console.log("customer_code:" + customer_code);
        console.log("email:"         + email);
        if (role == 9 && customer_code != "" && email == "") {
            email = customer_code + "@example.com";
            $('#email').val(email);
            console.log("emailchange:");
            M.toast({html: '電子メールアドレス ダミーに置き換えました'});
            // デバッグ
            event.preventDefault();
        }
    });
    
});

