/**
 * マイページ共通 js
 */
$(function() {
  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   * Controller
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const Controller = (function() {
    return {
      /**
       * initialize
       */
      init : function() {
        GoToTop.init();
        Menu.init();
        Toast.init();
        Ajax.init();
        Csv.init();
        this.setGlobalVars();
        this.setGlobalObject();
      },

      /**
       * グローバル変数にセットして他のjsから呼び出せるようにする
       */
      setGlobalVars : function() {
        window.application_url = $('#application_url').val();
        $('#application_url').remove();
      },

      /**
       * グローバル変数にセットして他のjsから呼び出せるようにする
       */
      setGlobalObject : function() {
        window.GdMypage = {
          'ajax': Ajax,
          'toast': Toast,
          'csv': Csv,
          'utility': Utility
        }
      },
    }
  })();


  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   * ページトップへ移動
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const GoToTop = (function() {

    const $BUTTON = $('.js-pagetop');

    return {
      /**
       * initialize
       */
      init : function() {
        this.setScrollEvent();
        this.setEvent();
      },

      /**
       * スクロールが500に達したらボタン表示
       */
      setScrollEvent : function() {
        $(window).scroll(function () {
            if ($(this).scrollTop() > 50) {
                $BUTTON.fadeIn();
            } else {
                $BUTTON.fadeOut();
            }
        });
      },

      /**
       * ページトップボタンを押したとき
       */
      setEvent : function() {
          $BUTTON.on('click', function(e) {
            e.preventDefault();
            $('body,html').animate({
                scrollTop: 0
            }, 400);
          });
      }
    }
  })();


  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   * ハンバーガーメニュー
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const Menu = (function() {

    const BUTTON_SELECTOR = '.js-btn';
    const TARGET_SELECTOR = '.navi, .burger, .btn-line';
    const OPEN_STATE_CLASS = 'open';
    const $BODY = $('body');

    let _this;
    let openState = false;
    let scrollPosition = 0;


    return {
      /**
       * initialize
       */
      init : function() {
        _this = this;
        this.setEvent();
      },

      /**
       * メニューボタンを押したとき
       */
      setEvent : function() {
          $(BUTTON_SELECTOR).on('click', function(e) {
            e.preventDefault();
            openState ? _this.close() : _this.open();
          });
      },

      /**
       * メニューを開く時の処理
       */
      open : function() {
        openState = true;
        $(TARGET_SELECTOR).addClass(OPEN_STATE_CLASS);
        scrollPosition = $(window).scrollTop();
        $BODY.addClass('fixed').css({'top': -scrollPosition});
      },

      /**
       * メニューを閉じる時の処理
       */
      close : function() {
        openState = false;
        $(TARGET_SELECTOR).removeClass(OPEN_STATE_CLASS);
        $BODY.removeClass('fixed').css({'top': 0});
        $(window).scrollTop(scrollPosition);
      }
    }
  })();


  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   *  Javascriptライブラリ「Toastr」
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const Toast = (function() {

    const DEFAULT_OPTIONS = {
        closeButton: true,
        debug: false,
        newestOnTop: false,
        progressBar: false,
        positionClass: 'toast-top-right',
        preventDuplicates: false,
        onclick: null,
        showDuration: 400,
        hideDuration: 400,
        timeOut: 5000,
        extendedTimeOut: false,
        showEasing: 'swing',
        hideEasing: 'linear',
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut'
    };

    let _this;

    return {
      /**
       * initialize
       */
      init : function() {
        _this = this;
        const status = $('#status').val() || '';
        $('#status').remove();
        if (status) {
          _this.info(status);
        }
      },

      /**
       * window幅によって表示位置を変える
       * @return {string}
       */
      getPositionClass : function() {
        if(window.matchMedia('(max-width: 767px)').matches){
          // sp版
          return 'toast-bottom-center';
        } else {
          // pc版
          return 'toast-top-right';
        }

      },

      /**
       * トースターjsの機能が存在するか確認
       * @return {boolean}
       */
      isLibrary : function() {
        if (window.toastr === void 0) {
          console.error('toastr.min.jsが読み込まれていません。');
          console.error('message:' + message);
          return false;
        }
        return true;
      },

      /**
       * トースターのオプションを設定する
       * @param {object} options
       */
      setOption : function(options) {
        toastr.options = Object.assign(
          DEFAULT_OPTIONS,
          { positionClass: _this.getPositionClass() },
          options || {}
        );
      },

      /**
       * メッセージを表示 success
       * @param {string} message
       * @param {object} options
       */
      success : function(message, options) {
        if (_this.isLibrary()) {
          _this.setOption(options);
          toastr.success(message);
        } else {
          _this.defaultAlert(message);
        }
      },

      /**
       * メッセージを表示 info
       * @param {string} message
       * @param {object} options
       */
      info : function(message, options) {
        if (_this.isLibrary()) {
          _this.setOption(options);
          toastr.info(message);
        } else {
          _this.defaultAlert(message);
        }
      },

      /**
       * メッセージを表示 warning
       * @param {string} message
       * @param {object} options
       */
      warning : function(message, options) {
        if (_this.isLibrary()) {
          _this.setOption(options);
          toastr.warning(message);
        } else {
          _this.defaultAlert(message);
        }
      },

      /**
       * メッセージを表示 error
       * @param {string} message
       * @param {object} options
       */
      error : function(message, options) {
        if (_this.isLibrary()) {
          _this.setOption(options);
          toastr.error(message);
        } else {
          _this.defaultAlert(message);
        }
      },

      /**
       * 標準アラート
       * @param {string} message
       */
      defaultAlert : function(message) {
        alert(message);
      },
    }
  })();


  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   * Ajax
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const Ajax = (function() {
    /* define */

    let _this;
    let _token;
    let _tokenError = {};
    let _loginError = false;

    /* method */
    return {
      /**
       * initialize
       */
      init : function() {
        _this = this;
        this.setToken($('#token').val());
        $('#token').remove();
      },


      /**
       * tokenをセット
       */
      setToken : function(token) {
        _token = token;
      },

      /**
       * tokenを取得
       */
      getToken : function() {
        return _token;
      },

      /**
       * tokenエラー回数をリセット
       */
      resetTokenErrorCount : function(url) {
        _tokenError[url] = 0;
      },

      /**
       * tokenエラー回数を加算して返す
       */
      addTokenErrorCount : function(url) {
        if (_tokenError.hasOwnProperty(url)) {
          _tokenError[url]++;
        } else {
          _tokenError[url] = 1;
        }
        return _tokenError[url];
      },

      /**
       * 実行
       */
      run : function(options, callback, ngcallback) {
        $.ajax({
          url: options.url,
          type: options.type || 'get',
          dataType: 'json',
          headers: {
            'X-CSRF-TOKEN': _this.getToken(),
          },
          data : options.data || {},
        }).done(function(response) {
          if (response.message === 'SUCCESS') {
            _this.resetTokenErrorCount(options.url)
            _this.setToken(response.token);
            callback !== void 0 && callback(response.data);
          } else if (response.message === 'NG') {
            ngcallback !== void 0 && ngcallback(response.data);
          }
        }).fail(function(error){
          console.error(error);
          if ((error.responseJSON?.message || '') === 'CSRF token mismatch.') {
            if (_this.addTokenErrorCount(options.url) === 1) {
              return _this.reissueToken(options, callback, ngcallback);
            }
          }
          ngcallback !== void 0 && ngcallback(error.data || error);
        });
      },

      /**
       * トークン発行して再度ajax通信を試みる
       */
      reissueToken : function(options, callback, ngcallback) {
        $.ajax({
          url: application_url + 'reissue_token',
          type: 'get',
          dataType: 'json'
        }).done(function(response) {
          if (response.message === 'SUCCESS') {
            _this.setToken(response.token);
            _this.run(options, callback, ngcallback);
          }
        }).fail(function(error){
          if (!_loginError && error.responseJSON.message === 'Unauthenticated.') {
            _loginError = true;
            window.alert('認証の有効期限切れもしくは無効となったので再度ログインを行ってください。');
            return window.location.href = '/';
          }
          console.error(error);
        });
      }
    }
  })();


  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   * Csv
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const Csv = (function() {
    /* define */
    const DOWNLOAD_BOX_ID = 'js-csv-download-resourse';

    let _this;
    let _count = 0;

    /* method */
    return {
      /**
       * initialize
       */
      init : function() {
        _this = this;
        this.setHtmlTagForDownload();
      },


      /**
       * ダウンロードデータ保管場所
       */
      setHtmlTagForDownload : function(token) {
        $('body').append('<div id="' +DOWNLOAD_BOX_ID + '" style="display:none;"></div>');
      },

      /**
       * csvファイルを動的にダウンロード
       * @param {string} fileName ファイル名
       * @param {string} base64 csvデータをbase64エンコードしたもの
       */
      createLink : function(fileName) {
        _count++;
        $('#' + DOWNLOAD_BOX_ID).append('<a id="'+DOWNLOAD_BOX_ID+'-'+_count+'" download="'+fileName+'" href="" download ></a>');
      },

      /**
       * csvファイルを動的にダウンロード
       * @param {string} fileName ファイル名
       * @param {string} base64 csvデータをbase64エンコードしたもの
       */
      download : function(fileName, base64) {
        _this.createLink(fileName);
        const blob = _this.toBlob(base64, 'text/csv');
        if (window.navigator.msSaveBlob) {
          // IEやEdgeの場合、Blob URL Schemeへと変換しなくともダウンロードできる
          window.navigator.msSaveOrOpenBlob(blob, 'file.csv');
        } else {
            // BlobをBlob URL Schemeへ変換してリンクタグへ埋め込む
            const TARGET = DOWNLOAD_BOX_ID+'-' + _count;
            $('#' + TARGET).prop('href', window.URL.createObjectURL(blob));
            // リンクをクリックする
            document.getElementById(TARGET).click();
        }
      },


      /**
       * Base64とMIMEコンテンツタイプからBlobオブジェクトを作成する。日本語対応。
       * @param {string} base64 base64エンコードしたデータ
       * @param {string} mime_ctype MIMEコンテンツタイプ
       */
      toBlob : function(base64, mime_ctype) {
        // 日本語の文字化けに対処するためBOMを作成する。
        var bom = new Uint8Array([0xEF, 0xBB, 0xBF]);

        var bin = atob(base64.replace(/^.*,/, ''));
        var buffer = new Uint8Array(bin.length);
        for (var i = 0; i < bin.length; i++) {
            buffer[i] = bin.charCodeAt(i);
        }
        // Blobを作成
        try {
            var blob = new Blob([bom, buffer.buffer], {
                type: mime_ctype,
            });
        } catch (e) {
            return false;
        }
        return blob;
      },
    }
  })();


  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   * ユーティリティメソッド
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const Utility = {
    /**
     * 3桁ずつカンマ区切りにする
     * @param {number} number 数字、小数点ありでもOK
     * @return {string}
     */
    number_format: function(number) {
      const s = String(number).split('.');
      const ret = String(s[0]).replace( /(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');
      if (s.length > 1) {
          ret += '.' + s[1];
      }
      return ret;
    },

    /**
     * カンマ区切りを戻す
     * @param {string}
     * @return {number}
     */
    number_unformat: function(string) {
      const removecomma = string.replace(/,/g, '');
      const s = String(removecomma).split('.');
      const ret = (s.length > 1) ? parseInt(removecomma) : parseFloat(removecomma);
      return ret;
    },

    /**
     * 
     * @param {string}
     * @return {number}
     */
    getYearFromYYYYMM: function(yyyymm) {
      return parseInt(yyyymm.toString().slice(0, 4));
    },

    /**
     * 
     * @param {string}
     * @return {number}
     */
    getMonthFromYYYYMM: function(yyyymm) {
      return parseInt(yyyymm.toString().slice(4, 6));
    },

    /**
     * 0埋め
     * @param {number}
     * @param {number} digit 何桁
     * @return {string}
     */
    getZeroPad: function(number, digit) {
      return number.toString().padStart(digit, '0');
    },

  };

  Controller.init();
});
