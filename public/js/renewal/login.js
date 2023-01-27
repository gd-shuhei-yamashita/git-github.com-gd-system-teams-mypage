/**
 * ログイン js
 */
$(function(){
  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   * Controller
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const Controller = (function() {
    return {
      /**
       * initialize
       */
      init : function() {
        Reminder.init();
      },
    }
  })();


  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   * ID・パスワードを忘れた方はこちら
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const Reminder = (function() {
    /* define */
    const $BUTTON = $('.js-reminder-button');
    const $CONTENTS = $('.js-password-reminder');
    const MODAL_TITLE = 'IDと初期化したパスワードを<br class="br-sp">SMSで送信します。';
    const CONTENTS = $CONTENTS.html();
    const PHONE_NUMBER = '.js-reminder-modal-phone';
    const BIRTHDAY_YEAR = '.js-reminder-modal-y';
    const BIRTHDAY_MONTH = '.js-reminder-modal-m';
    const BIRTHDAY_DAY = '.js-reminder-modal-d';
    const SUBMIT_BUTTON = '.confirm-area-buttons-submit';
    const CANCEL_BUTTON = '.confirm-area-buttons-cancel';
    const ORIGINAL_CANCEL_BUTTON = 'js-reminder-modal-close';
    const BUTTONS_AREA = '.confirm-area-buttons';
    const CONTACT_AREA = '.confirm-area-contact';
    const MESSAGE_AREA = '.confirm-area-message';
    const MESSAGE = '.js-reminder-modal-message';
    $CONTENTS.remove();
    const LAYER_BOARD_OPTION = {
        delayTime: 100, // 表示までの待ち時間
        fadeTime: 300, // 表示開始から表示しきるまでの時間
        alpha: 0.8, // 背景レイヤーの透明度
        limitMin: 0, // 何分経過後に再度表示するか/分（0で再表示なし）
        easing: 'linear', // イージング
        limitCookie: 0, // cookie保存期間/日（0で開くたび毎回表示される）
        countCookie: 1000 // 何回目のアクセスまで適用するか(cookie保存期間でリセット)
    };

    let _this;
    let _jc;
    let _data = {
      phone_num: '',
      year: '',
      month: '',
      day: '',
    };

    /* method */
    return {
      /**
       * initialize
       */
      init : function() {
        _this = this;
        this.setEvent();
        // test↓
        // $('.js-exist_multiple_contracts').layerBoard(LAYER_BOARD_OPTION);
        // $('.js-password_reminder_complete').layerBoard(LAYER_BOARD_OPTION);
      },


      /**
       * クリックイベントをセット
       */
      setEvent : function() {
        $BUTTON.on('click', function(e) {
          e.preventDefault();
          _this.fireEvent();
        });
      },

      /**
       * モーダル表示
       */
      fireEvent : function() {
        _jc = $.confirm({
          title: MODAL_TITLE,
          type: 'password-remider',
          columnClass: 'small',
          content: CONTENTS,
          backgroundDismiss: true,
          buttons: {
            formSubmit: {
              text: '送信',
              btnClass: '',
              action: function () {}
            },
            cancel: {
              text: 'キャンセル',
              btnClass: ORIGINAL_CANCEL_BUTTON,
              action: function () {}
            },
          },
          onContentReady: function() { _this.afterModalDisplay() }
        });
      },

      /**
       * モーダルを表示した際の処理
       */
      afterModalDisplay : function() {
        // 送信ボタンクリックイベント
        $(SUBMIT_BUTTON).on('click', function (e) {
          e.preventDefault();
          const $button = $(this);
          $button.data('default-label', $(this).text());
          $button.text('送信中').prop('disabled', true);
          _this.sendAction();
        });
        // キャンセルボタンクリックイベント
        $(CANCEL_BUTTON + ', .jconfirm-bg').on('click', function (e) {
          e.preventDefault();
          _jc.close();
        });
        // 年月日の選択肢を生成
        const THIS_YEAR = new Date().getFullYear();
        const DEFAULT_OPTION = '<option value="0">選択してください</option>';
        let html_year_options  = DEFAULT_OPTION;
        let html_month_options = DEFAULT_OPTION;
        let html_day_options   = DEFAULT_OPTION;

        for (let i = THIS_YEAR - 110; i <= THIS_YEAR; i++) {
          html_year_options += '<option value="' + i + '">' + i + '</option>';
        }
        for (let i = 1; i <= 12; i++) {
          html_month_options += '<option value="' + i + '">' + i + '</option>';
        }
        for (let i = 1; i <= 31; i++) {
          html_day_options += '<option value="' + i + '">' + i + '</option>';
        }

        $(BIRTHDAY_YEAR).empty().append(html_year_options);
        $(BIRTHDAY_MONTH).empty().append(html_month_options);
        $(BIRTHDAY_DAY).empty().append(html_day_options);

        // 年月からその月の最終日を取得して再生成
        $(BIRTHDAY_YEAR + ',' + BIRTHDAY_MONTH).on('change', function() {
          const selected = parseInt($(BIRTHDAY_DAY).val() || 0);
          let year = parseInt($(BIRTHDAY_YEAR).val() || 0);
          let month = parseInt($(BIRTHDAY_MONTH).val() || 0);
          if (year && month) {
            month = month === 12 ? 0 : month;
            year = month === 12 ? year + 1 : year;
            const lastDay = new Date(year, month, 0).getDate();
            html_day_options   = DEFAULT_OPTION;
            for (let i = 1; i <= lastDay; i++) {
              let attr = i === selected ? ' selected' : '';
              html_day_options += '<option value="' + i + '"'+attr+'>' + i + '</option>';
            }
            $(BIRTHDAY_DAY).empty().append(html_day_options);
          }
          _this.checkDiffEvent();
        });
        $(PHONE_NUMBER + ',' + BIRTHDAY_DAY).on('change', function() {
          _this.checkDiffEvent();
        });
      },

      /**
       * モーダルのフォーム送信処理
       */
      sendAction : function() {
        _data = {
          'phone_num': $(PHONE_NUMBER).val() || '',
          'year' : $(BIRTHDAY_YEAR).val()  || '',
          'month': $(BIRTHDAY_MONTH).val() || '',
          'day'  : $(BIRTHDAY_DAY).val()   || '',
        };
        GdMypage.ajax.run({
          url: 'password_reminder_addtional_auth',
          type: 'POST',
          data: _data,
        }, function(response) {
          $(BUTTONS_AREA).addClass('disabled');
          switch (response.status) {
            case 'success':
              _this.showSuccess(response.message);
              break;
            case 'pending':
              _jc.close();
              $('.js-exist_multiple_contracts').layerBoard(LAYER_BOARD_OPTION);
              break;
            case 'not_found':
            case 'error':
            default:
              _this.showError(response.message);
              break;
          }
          $(SUBMIT_BUTTON).text($(SUBMIT_BUTTON).data('default-label') || '送信').prop('disabled', false);
        }, function(logs) {
          console.dir(JSON.stringify(logs));
          _jc.close();
          $.alert('SMSの送信に失敗しました。');
        });
      },

      /**
       * モーダルをリセットする
       */
      reset : function() {
        $(MESSAGE).html('');
        $(MESSAGE_AREA).removeClass('error success').addClass('disabled');
        $(CONTACT_AREA).addClass('disabled');
        $(BUTTONS_AREA).removeClass('disabled');
        $(SUBMIT_BUTTON).prop('disabled', false);
      },

      /**
       * エラーメッセージを表示する
       */
      showError : function(message) {
        $(CONTACT_AREA).removeClass('disabled');
        $(MESSAGE_AREA).removeClass('disabled success').addClass('error');
        $(MESSAGE).html(message ||  'SMS送信失敗しました。');
      },

      /**
       * 成功メッセージを表示する
       */
      showSuccess : function(message) {
        $(CONTACT_AREA).removeClass('disabled');
        $(MESSAGE_AREA).removeClass('disabled error').addClass('success');
        $(MESSAGE).html(message);
      },

      /**
       * 値に変更があればボタンを表示する
       */
      checkDiffEvent : function() {
        const p = $(PHONE_NUMBER).val() || '';
        const y = $(BIRTHDAY_YEAR).val()  || '';
        const m = $(BIRTHDAY_MONTH).val() || '';
        const d = $(BIRTHDAY_DAY).val()   || '';
        // console.log(p + ',' + y + ',' + m + ',' + d);
        // console.log(_data.phone_num + ',' + _data.year + ',' + _data.month + ',' + _data.day);
        if (
          p !== _data.phone_num ||
          y !== _data.year ||
          m !== _data.month ||
          d !== _data.day
        ) {
          $(CONTACT_AREA).addClass('disabled');
          $(MESSAGE_AREA).removeClass('error success').addClass('disabled');
          $(BUTTONS_AREA).removeClass('disabled');
        }
      },

    }
  })();


  Controller.init();
});
