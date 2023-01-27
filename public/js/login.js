/**
 * パスワードリマインダー、メールアドレス入力
 * 
 */
$(function() {

//    $('#login').validate({
//      rules: {
//        email: {
//          required: true,
//          email: true
//        },
//        password: "required",
//      },
//      errorPlacement: function(error, element) {
//        error.appendTo($('#'+ element.attr('name') + '_err'));
//      }
//    });

  $('#btn_password_remind').on('click', function() {

    $.confirm({
      title: '<i class="material-icons left">contact_mail</i>パスワードリマインダー',
      columnClass: 'small',
      content: '' +
        '<form id="password_remind" action="">' +
        '<div class="row">' +
          '<div class="col s12">' +
            '<input type="email" name="remind_email" id="remind_email" placeholder="メールアドレス">' +
            '<div id="remind_email_err" class="error" style="float: left;"></div>' +
          '</div>' +
        '</div>' +
        '<div class="row">' +
          '<div class="col s12">' +
            '<input type="email" name="remind_re_email" id="remind_re_email" placeholder="メールアドレス(再入力)">' +
            '<div id="remind_re_email_err" class="error" style="float: left;"></div>' +
          '</div>' +
        '</div>' +
        '</form>',
      buttons: {
        formSubmit: {
          text: '送信',
          btnClass: 'btn waves-effect blue',
          action: function () {
            var error_message = '';
            var remind_email = this.$content.find('#remind_email').val();
            var remind_re_email = this.$content.find('#remind_re_email').val();

            $('#password_remind .error').html('');

            if(!remind_email) {
              error_message = 'メールアドレスは必須です。';
              $('#remind_email_err').html(error_message);
            } else {
              if(!remind_email.match(/^([a-zA-Z0-9])+([a-zA-Z0-9\+\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)*$/)){
                error_message = 'メールアドレス形式で入力してください。';
                $('#remind_email_err').html(error_message);
              }
            }

            if(!remind_re_email) {
              error_message = 'メールアドレスは必須です。';
              $('#remind_re_email_err').html(error_message);
            } else {
              if(remind_email != remind_re_email) {
                error_message = 'メールアドレスが一致しません。';
                $('#remind_re_email_err').html(error_message);
              }
            }

            if (error_message) {
              return false;
            } else {
              $.alert('メールを送信しました。');
            }
          }
        },
        キャンセル: function () {}
      },
      onContentReady: function () {
        var jc = this;
        this.$content.find('#password_remind').on('submit', function (e) {
          e.preventDefault();
          jc.$$formSubmit.trigger('click');
        });
      }
    });

//    $('#password_remind').validate({
//      rules: {
//        remind_email: {
//          required: true,
//          email: true
//        },
//        remind_re_email: "required",
//      },
//      errorPlacement: function(error, element) {
//        error.appendTo($('#'+ element.attr('name') + '_err'));
//      }
//    });

  });
});
