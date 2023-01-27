/** リマインダー、パスワード変更 */
$(function() {
  $('.sidenav').sidenav();
  $('.dropdown-trigger').dropdown();
  // リマインダーメール
  // $('#btn_password_remind').on('click', function() {
  //   $('#layer_board_area').layerBoard({
  //     delayTime: 100, //表示までの待ち時間
  //     fadeTime: 300, //表示開始から表示しきるまでの時間
  //     alpha: 0.8, //背景レイヤーの透明度
  //     limitMin: 0, //何分経過後に再度表示するか/分（0で再表示なし）
  //     easing: 'linear', //イージング
  //     limitCookie: 0, //cookie保存期間/日（0で開くたび毎回表示される）
  //     countCookie: 1000 //何回目のアクセスまで適用するか(cookie保存期間でリセット)
  //   });
  //   $(".nen select").empty();
  //   var now = new Date();
  //   var stack_year = "<option value='0'>選択してください</option>";
  //   for(var i = 1900; i <= now.getFullYear(); i++) {
  //     stack_year += '<option value="' + i + '">' + i + '</option>';
  //   }
  //   $('[name=year]').append(stack_year);
  //   var stack_month = "<option value='0'>選択してください</option>";
  //   for(var i = 1; i <= 12; i++) {
  //     stack_month += '<option value="' + i + '">' + i + '</option>';
  //   }
  //   $('[name=month]').append(stack_month);
  //   var stack_day = "<option value='0'>選択してください</option>";
  //   for(var i = 1; i <= 31; i++) {
  //     stack_day += '<option value="' + i + '">' + i + '</option>';
  //   }
  //   $('[name=day]').append(stack_day);
  // });

  $('#btn_password_remind').on('click', function() {
    event.preventDefault();
    $.confirm({
      title: 'IDと初期化したパスワードをSMSで送信します。',
      columnClass: 'small',
      content: '' +
        '<div class="confirm-area"><form id="password_edit" action="">' +
        '<div class="row">' +
          '<div class="input-field2">' +
            '<label>携帯電話番号 <span style="color: #696969; font-size: 70%;">※グランデータに登録中の電話番号をご入力ください。</span><input type="text" name="phone_text" id="phone_text" placeholder="ハイフン無し"></label>' +
          '</div>' +
        '</div>' +
        '<div class="row">' +
          '<div class="input-field2">' +
            '<label>生年月日' +
            '<div class="nen">' +
            '<select name="year_text">' +
            '</select>' +
            '年 ' +
            '<select name="month_text">' +
            '</select>' +
            '月 ' +
            '<select name="day_text">' +
            '</select>' +
            '日 ' +
          '</label></div>' +
        '</div>' +
        '<div class="error" style="color: #ff0000;font-weight: bolder; margin-top: 10px">' +
          $('#addtional_auth_err').html() +
        '</div>' +
        '<a class="contact" href="https://grandata-service.jp/contact/" target="_blank" rel="noopener noreferrer">SMSが届かない場合は、こちらからお問い合わせください。</a>' +
        '<div class="row">' +
          '<div class="input-field2">' +
            '<div id="remind_email_err" class="error" style="float: left;color: #ff0000;font-weight: bolder;"></div>' +
            '<div id="remind_re_email_err" class="error" style="float: left;color: #ff0000;font-weight: bolder;"></div>' +
          '</div>' +
        '</div>' +
        '</form></div>',
      buttons: {
        formSubmit: {
          text: '送信',
          btnClass: 'confirm',
          action: function () {
            $('#addtional_auth_err').html('');
            var token = $('#token').val();
            var phone_num = $('input[name="phone_text"]').val();
            var year = $('[name=year_text]').val();
            var month = $('[name=month_text]').val();
            var day = $('[name=day_text]').val();
            $.ajax({
              url: application_url + 'password_reminder_addtional_auth',
              type: 'POST',
              dataType : 'json',
              data: {
                _token: token,
                "phone_num": phone_num,
                "year": year,
                "month": month,
                "day": day,
              },
            })
            .done(function(response){
              if (response.multiple_contract_flg) {
                $('.btn_close').click();
                $('#layer_board_area_multiple').layerBoard({
                    delayTime: 100, //表示までの待ち時間
                    fadeTime: 300, //表示開始から表示しきるまでの時間
                    alpha: 0.8, //背景レイヤーの透明度
                    limitMin: 0, //何分経過後に再度表示するか/分（0で再表示なし）
                    easing: 'linear', //イージング
                    limitCookie: 0, //cookie保存期間/日（0で開くたび毎回表示される）
                    countCookie: 1000 //何回目のアクセスまで適用するか(cookie保存期間でリセット)
                });
              } else if(response.error_msg){
                  $('#addtional_auth_err').html(response.error_msg);
                  $('#btn_password_remind').trigger("click");
              } else {
                  $('.btn_close').click();
                  $('#layer_board_area_complete').layerBoard({
                      delayTime: 100, //表示までの待ち時間
                      fadeTime: 300, //表示開始から表示しきるまでの時間
                      alpha: 0.8, //背景レイヤーの透明度
                      limitMin: 0, //何分経過後に再度表示するか/分（0で再表示なし）
                      easing: 'linear', //イージング
                      limitCookie: 0, //cookie保存期間/日（0で開くたび毎回表示される）
                      countCookie: 1000 //何回目のアクセスまで適用するか(cookie保存期間でリセット)
                  });
              }
            })
            .fail(function(logs){
              console.dir(JSON.stringify(logs));
                $.alert("SMS送信失敗しました。");
            });
          }
        },
        キャンセル: function () {}
      },
      onContentReady: function () {
        var jc = this;
        this.$content.find('#password_edit').on('submit', function (e) {
          e.preventDefault();
          jc.$$formSubmit.trigger('click');
        });
        $(".nen select").empty();
        var now = new Date();
        var stack_year = "<option value='0'>選択してください</option>";
        for(var i = 1900; i <= now.getFullYear(); i++) {
          stack_year += '<option value="' + i + '">' + i + '</option>';
        }
        $('[name=year_text]').append(stack_year);
        var stack_month = "<option value='0'>選択してください</option>";
        for(var i = 1; i <= 12; i++) {
          stack_month += '<option value="' + i + '">' + i + '</option>';
        }
        $('[name=month_text]').append(stack_month);
        var stack_day = "<option value='0'>選択してください</option>";
        for(var i = 1; i <= 31; i++) {
          stack_day += '<option value="' + i + '">' + i + '</option>';
        }
        $('[name=day_text]').append(stack_day);
      }
    });
  });

  /** メールでのパスワードリマインダー */
  // $('#abtn_password_remind').on('click', function() {
  //   event.preventDefault();
  //   $.confirm({
  //     title: '・パスワードリマインダー',
  //     columnClass: 'small',
  //     content: '' +
  //       '<div class="confirm-area"><form id="password_edit" action="">' +
  //       '<div class="row">' +
  //         '<div class="input-field2">' +
  //           '<label>メールアドレス<input type="email" name="remind_email" id="remind_email" placeholder="メールアドレス"></label>' +
  //         '</div>' +
  //       '</div>' +
  //       '<div class="row">' +
  //         '<div class="input-field2">' +
  //           '<label>メールアドレス(再入力)<input type="email" name="remind_re_email" id="remind_re_email" placeholder="メールアドレス(再入力)"></label>' +
  //         '</div>' +
  //       '</div>' +
  //       '<div class="row">' +
  //         '<div class="input-field2">' +
  //           '<div id="remind_email_err" class="error" style="float: left;color: #ff0000;font-weight: bolder;"></div>' +
  //           '<div id="remind_re_email_err" class="error" style="float: left;color: #ff0000;font-weight: bolder;"></div>' +
  //         '</div>' +
  //       '</div>' +
  //       '</form></div>',
  //     buttons: {
  //       formSubmit: {
  //         text: '送信',
  //         btnClass: 'confirm',
  //         action: function () {
  //           var token = $('#token').val();
  //           // var token = "";
  //           console.log(token);
  //           var error_message = '';
  //           var remind_email = this.$content.find('#remind_email').val();
  //           var remind_re_email = this.$content.find('#remind_re_email').val();

  //           $('#password_edit .error').html('');

  //           if(!remind_email) {
  //             error_message = 'メールアドレスは必須です。';
  //             $('#remind_email_err').html(error_message);
  //           } else {
  //             if(!remind_email.match(/^([a-zA-Z0-9])+([a-zA-Z0-9\+\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)*$/)){
  //               error_message = 'メールアドレス形式で入力してください。';
  //               $('#remind_email_err').html(error_message);
  //             }
  //           }

  //           if(!remind_re_email) {
  //             error_message = 'メールアドレスは必須です。';
  //             $('#remind_email_err').html(error_message);
  //           } else {
  //             if(remind_email != remind_re_email) {
  //               error_message = 'メールアドレスが一致しません。';
  //               $('#remind_email_err').html(error_message);
  //             }
  //           }

  //           if (error_message) {
  //             return false;
  //           } else {
  //             console.log("password_reminder ajax start");
  //             $.ajax({
  //               url: application_url + 'password_reminder',
  //               type: 'POST',
  //               dataType : 'json',
  //               data: {
  //                 _token: token,
  //                 "email": remind_email
  //               },
  //             })
  //             .done(function(response){
  //               if (response.addtional_auth_flag) {
  //                 $('input[name="email"]').val(response.email);
  //                 $('#email').text(response.email);
  //                 $('#layer_board_area').layerBoard({
  //                   delayTime: 100, //表示までの待ち時間
  //                   fadeTime: 300, //表示開始から表示しきるまでの時間
  //                   alpha: 0.8, //背景レイヤーの透明度
  //                   limitMin: 0, //何分経過後に再度表示するか/分（0で再表示なし）
  //                   easing: 'linear', //イージング
  //                   limitCookie: 0, //cookie保存期間/日（0で開くたび毎回表示される）
  //                   countCookie: 1000 //何回目のアクセスまで適用するか(cookie保存期間でリセット)
  //                 });
  //                 $(".nen select").empty();
  //                 var now = new Date();
  //                 var stack_year = "<option value='0'>選択してください</option>";
  //                 for(var i = 1900; i <= now.getFullYear(); i++) {
  //                   stack_year += '<option value="' + i + '">' + i + '</option>';
  //                 }
  //                 $('[name=year]').append(stack_year);
  //                 var stack_month = "<option value='0'>選択してください</option>";
  //                 for(var i = 1; i <= 12; i++) {
  //                   stack_month += '<option value="' + i + '">' + i + '</option>';
  //                 }
  //                 $('[name=month]').append(stack_month);
  //                 var stack_day = "<option value='0'>選択してください</option>";
  //                 for(var i = 1; i <= 31; i++) {
  //                   stack_day += '<option value="' + i + '">' + i + '</option>';
  //                 }
  //                 $('[name=day]').append(stack_day);
  //               } else if (response.temporary_password_flag) {
  //                 $('#layer_board_area_complete').layerBoard({
  //                   delayTime: 100, //表示までの待ち時間
  //                   fadeTime: 300, //表示開始から表示しきるまでの時間
  //                   alpha: 0.8, //背景レイヤーの透明度
  //                   limitMin: 0, //何分経過後に再度表示するか/分（0で再表示なし）
  //                   easing: 'linear', //イージング
  //                   limitCookie: 0, //cookie保存期間/日（0で開くたび毎回表示される）
  //                   countCookie: 1000 //何回目のアクセスまで適用するか(cookie保存期間でリセット)
  //                 });
  //               } else {
  //                 $.alert("メールを送信しました。<br/>※メールが届かない場合、メールアドレスをご確認の上再度お試しください。");
  //               }
  //             })
  //             .fail(function(logs){
  //               console.dir(JSON.stringify(logs));
  //               $.alert('メール送信失敗しました。');
  //             });
  //             console.log("fin.");
  //           }
  //         }
  //       },
  //       キャンセル: function () {}
  //     },
  //     onContentReady: function () {
  //       var jc = this;
  //       this.$content.find('#password_edit').on('submit', function (e) {
  //         e.preventDefault();
  //         jc.$$formSubmit.trigger('click');
  //       });
  //     }
  //   });
  // });

  // パスワード変更
  $('#submenu_password_init').submit(function() {
    console.log("submenu_password_init check start");

    var error_message = '';
    var password_new = $('#password_new').val();
    var password_new_confirmation = $('#password_new_confirmation').val();

    $('#password_edit .error').html('');

    if (password_new != password_new_confirmation) {
      error_message = 'パスワードが一致しません。';
      $('#password_new_err').html(error_message);

    } else if (!password_new) {
      error_message = 'パスワードは必須です。';
      $('#password_new_err').html(error_message);
    } else {
      var pattern = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!-/:-@[-`{-~]{10,20}$/;
      //var pattern = /^(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,20}$/i;
      //var pattern = /^(?=.*?[a-z])(?=.*?[A-Z])(?=.*?\d)[a-zA-Z\d]{8,20}$/;
      //var pattern = /^(?=.*?[a-z])(?=.*?\d)(?=.*?[!-\/:-@[-`{-~])[!-~]{8,20}$/i;

      var complexity = '半角英数字10文字以上20文字以下';
      //var complexity = '半角英数字をそれぞれ1種類以上含む8文字以上20文字以下';
      //var complexity = '半角英小文字大文字数字をそれぞれ1種類以上含む8文字以上20文字以下';
      //var complexity = '半角英数字記号をそれぞれ1種類以上含む8文字以上20文字以下';

      if (!password_new.match(pattern)){
        error_message = complexity + 'で入力してください。';
        $('#password_new_err').html(error_message);
      } else {
        $('#password_new_err').html("");
      }
    }

    if (!password_new_confirmation) {
      error_message = 'パスワードは必須です。';
      $('#password_new_confirmation_err').html(error_message);
    } else {
      if (password_new != password_new_confirmation) {
        error_message = 'パスワードが一致しません。';
        $('#password_new_confirmation_err').html(error_message);
      } else {
        $('#password_new_confirmation_err').html("");
      }
    }

    // エラーメッセージがあるなら戻す
    if (error_message) {
      return false;
    }

    // 正常
    return true;
  });

});