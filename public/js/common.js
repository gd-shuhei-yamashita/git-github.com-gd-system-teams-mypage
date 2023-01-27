/**
 *
 * 各画面にて非同期処理を終了した時点で
 * main_deferred.resolve()
 * を呼べばthen()に入れる。ローディングが消えなければresolve()を疑うこと。
 *
 * main_deferred.promise()
 * .then(処理1)
 * .then(処理2)
 * .then(処理n);
 * と処理をつないで非同期処理を終えてから残りの処理に入る。
 *
 *
 */

/**
 * @param{Object} 主プロセスをコントロールするDeferred
 */
var main_deferred = new $.Deferred;

/**
 * @param{Object} 個別プロセスをコントロールするDeferred
 */
var each_deferred = new $.Deferred;
var quill_deferred = new $.Deferred;
var dfd_auth_common = new $.Deferred;
var dfd_gdpr = new $.Deferred;

/**
 * Quill
 */
var quill;

/**
 * 初期処理
 */
$(function() {

  // 共通処理完了後に実行
  main_deferred.promise()
  .then(function() { // Step1

//    // ナビ（フィールド）
//    var nav_fields = get_filelist(PATH_JSON_FIELDS);
//    nav_fields.deferred.done(function() {
//      $.each(nav_fields.data, function(i, v) {
////        $('#nav_fields').append(
////          '<li><a href="' + CONTENT_090301 + '.php?id=' + v.name + '&' + get_timestamp() + '">' + v.display + '</li>'
////        );
//        $('#nav_fields').append(
//          '<option value="' + CONTENT_090301 + '.php?id=' + v.name + '&' + get_timestamp() + '">' + v.display + '</option>'
//        );
//      });
//    });

//    // ナビ（サイト）
//    var nav_pages = get_filelist(PATH_JSON_SITES);
//    nav_pages.deferred.done(function() {
//      $.each(nav_pages.data, function(i, v) {
//        $('#nav_pages').append(
//          '<li><a href="' + CONTENT_090302 + '.php?id=' + v.name + '&' + get_timestamp() + '">' + v.display + '</li>'
//        );
//      });
//    });

  })
  .then(function() { // Step2

    //set_height();

    //////////////////////////////////////////////////
    // イベント：Quill
    //////////////////////////////////////////////////
    $('.editor').on('click', function() {
      var $edit_target = $(this).nextAll('.edit-target').eq(0);
      var $html_target = $(this).nextAll('.html-target').eq(0);
      var $disp_target = $(this).nextAll('.disp-target').eq(0);

      if ($edit_target.val()) {
        var val = JSON.parse($edit_target.val());
      } else {
        var val = {};
      }

      $.confirm({
        title: '',
        content: 'url:/admin/quill',
        onContentReady: function () {
          quill.setContents(val);
        },
        buttons: {
          '設定': function () {
            var contents = quill.getContents();
            //var delta = JSON.stringify(contents, null, 2); // 第三引数で整形
            var delta = JSON.stringify(contents, null);
            var html = $('.ql-editor').html();
            var _html = unescapeHTML(html);
            $edit_target.val(delta);
            $html_target.val(quill.container.firstChild.innerHTML);
            $disp_target.html(_html);
          },
          'キャンセル': function () {
          }
        }
      });
    });

    // Materialize
    M.AutoInit();

    // デートピッカー
    $('.datepicker').datepicker({
      selectMonths: true,
      clear: 'Clear',
      close: 'OK',
      autoClose: true,
      format: 'yyyy/mm/dd',
      i18n:{
        months: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12']
      },
      yearRange: 50
    });

    // タイムピッカー
    $('.timepicker').timepicker({
      twelveHour: false,
      autoClose: true,
    });

    // 共通処理の終了⇒個別処理の開始へ
    each_deferred.resolve();
  })
  .then(function() { // Step3

    // ローディング
    $('.loading').hide();
    $('.content').css({opacity: '0'}).animate({opacity: '1'}, 500);
  });

});