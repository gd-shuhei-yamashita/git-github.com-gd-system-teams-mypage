/**
 * HOME js
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
        Notices.init();
        Billding.init();
      },
    }
  })();

  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   * 各種お知らせ
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const Notices = (function() {
    /* define */
    const $CONTENTS_PC = $('#notices_pc');
    const $CONTENTS_SP = $('#notices_sp');
    const AJAX_URL = application_url + 'home/notices';

    let _this;

    /* method */
    return {
      /**
       * initialize
       */
      init : function() {
        _this = this;
        this.show();
      },


      /**
       * 各種お知らせの情報を非同期通信で取得して表示する
       */
      show : function() {
        const options = {
          url: AJAX_URL,
          type: 'post',
          data : null
        };
        GdMypage.ajax.run(options, function(response) {
          $CONTENTS_PC.html(response.pc);
          $CONTENTS_SP.html(response.sp);
        });

      }
    }
  })();


  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   * 請求エリア
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const Billding = (function() {
    /* define */
    const $CONTENTS = $('#billing_informations');
    const AJAX_URL = application_url + 'home/billing_informations';
    const PREV_BUTTON = '#billing_prev';
    const NEXT_BUTTON = '#billing_next';
    let _this;
    let _billingDate = {
      max: null,
      min: null,
      current: null,
    };

    /* method */
    return {
      /**
       * initialize
       */
      init : function() {
        _this = this;
        this.show();
      },


      /**
       * 現在の請求年月にセット
       */
      setCurrnetYearMonth: function(year, month) {
        _billingDate.current = year + month.toString().padStart(2, '0');
      },

      /**
       * 請求年月データを保存
       */
      setBillingDate: function(response) {
        if (response.date) _billingDate.current = response.date;
        if (response.max_date) _billingDate.max = response.max_date;
        if (response.min_date) _billingDate.min = response.min_date;
      },

      /**
       * リクエスト用の請求の年月を取得
       */
      getBilldingYearMonth: function() {
        return _billingDate.current;
      },


      /**
       * 請求データを非同期通信で取得して表示する
       */
      show: function() {
        const options = {
          url: AJAX_URL,
          type: 'post',
          data : {
            'billing_date': _this.getBilldingYearMonth()
          }
        };
        GdMypage.ajax.run(options, function(response) {
          $CONTENTS.html(response.contents);
          _this.setBillingDate(response);
          if ( _billingDate.current < _billingDate.max) {
            _this.setEventNextButton();
          }
          if ( _billingDate.current > _billingDate.min) {
            _this.setEventPrevButton();
          }
        });
      },

      /**
       * 「先月」ボタンを押したときのイベント登録
       */
      setEventPrevButton: function() {
        $(PREV_BUTTON).addClass('active').on('click', function(e) {
          e.preventDefault();
          let year = parseInt(_billingDate.current.toString().slice(0, 4));
          let month = parseInt(_billingDate.current.toString().slice(4, 6));
          if (month === 1) {
            year -= 1;
            month = 12;
          } else {
            month -= 1;
          }
          _this.setCurrnetYearMonth(year, month);
          _this.show();
        });
      },

      /**
       * 「翌月」ボタンを押したときのイベント登録
       */
      setEventNextButton: function() {
        $(NEXT_BUTTON).addClass('active').on('click', function(e) {
          e.preventDefault();
          let year = parseInt(_billingDate.current.toString().slice(0, 4));
          let month = parseInt(_billingDate.current.toString().slice(4, 6));
          if (month === 12) {
            year += 1;
            month = 1;
          } else {
            month += 1;
          }
          _this.setCurrnetYearMonth(year, month);
          _this.show();
        });
      }
    }

  })();


  Controller.init();
});
