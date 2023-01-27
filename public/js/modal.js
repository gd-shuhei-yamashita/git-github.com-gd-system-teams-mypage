$(function () {
    //「閉じる」がクリックされた場合
    $('.layer_board_bg').on('click', function () {
        $('body').css('overflow-y', 'auto');
    });
    $('.btn_close').on('click', function () {
        $('body').css('overflow-y', 'auto');
    });
    $('.layer_board_open a').on('click', function () {
        $('body').css('overflow-y', 'hidden');
    });
});

$(function () {
    $(".show_more").click(function () {
        var show_text = $(this)
            .parent(".text_wrapper")
            .find(".text");
        var small_height = 90; //This is initial height.
        var original_height = show_text.css({
            height: "auto"
        }).height();

        if (show_text.hasClass("open")) {
            /*CLOSE*/
            show_text.height(original_height).animate({
                height: small_height
            }, 300);
            show_text.removeClass("open");
            $(this)
                .text("本文を表示する")
                .removeClass("active");
        } else {
            /*OPEN*/
            show_text
                .height(small_height)
                .animate({
                    height: original_height
                }, 300, function () {
                    show_text.height("auto");
                });
            show_text.addClass("open");
            $(this)
                .text("閉じる")
                .addClass("active");
        }
    });
});
$(function () {
    $('#btn1').on('click', function () {
        // 要素の位置を取得して変数に格納
        var isPosition = $('#high').position();
        console.log(isPosition.top);
        $(".layer_board").animate({
            scrollTop: isPosition.top - 30
        }, 300);
    });
    $('#btn2').on('click', function () {
        // 要素の位置を取得して変数に格納
        var isPosition = $('#arrive').position();
        console.log(isPosition.top);
        $(".layer_board").animate({
            scrollTop: isPosition.top - 30
        }, 300);
    });
});

$(function () {
    $('#email_regist').on('click', function () {
        $('#addtional_auth_err').html('');
        var token = $('#token').val();
        var phone_num = $('input[name="phone_num"]').val();
        var year = $('[name=year]').val();
        var month = $('[name=month]').val();
        var day = $('[name=day]').val();
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
    });
});
