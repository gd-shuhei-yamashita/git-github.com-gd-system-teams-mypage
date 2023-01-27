function auth(){

  var dfd_f;

  // サイト定義
//  $.getJSON('/json/sites/' + cid + '.json', function(param) {
//    $.each(param, function(k, v) {
//      site[k] = v;
//    });

  $.ajax({
    url: "/application/definition/" + cid,
  }).done(function(param) {
    $.each(param, function(k, v) {
      site[k] = v;
    });

    // 現在の言語が不明な場合は言語リストの1つ目を設定
    if (!Object.keys(site.current).length) {
      site.current.lang = site.lang[0].value;
    }

    // TODO:ロゴテンプレート化
    var image_url = '/image/' + site.course_id + '.gif';
    var newImage = new Image();
    newImage.src = image_url;
    newImage.onload = function() {
      $.get(image_url)
      .done(function() { 
        $('.brand-logo')
          .html('')
          .append(
            $('<img/>')
            .css('height', '44px')
            .css('width', 'auto')
            .css('margin', '10px 10px 10px 0')
            .attr('src', image_url)
          );
      }).fail(function() { 
        $('.brand-logo')
          .html('')
      });
    }

    // 共通認証
    common_auth(site, pno, application_id);
    dfd_auth_common.promise()
    .then(function(){
      // GDPR
      gdpr(site, pno, application_id);
      dfd_gdpr.promise()
      .then(function(){

        //////////////////////////////////////////////////
        // サイト主処理 Step1 定義読み込み
        //////////////////////////////////////////////////
        $.when(
          // 都道府県
          $.getJSON('/json/references/states.json', function(param) {
            site.states = param;
          })
        )
        //////////////////////////////////////////////////
        // サイト主処理 Step2 ページ生成
        //////////////////////////////////////////////////
        .then(function() {
          // 現在の言語が不明な場合は言語リストの1つ目を設定
          if (!Object.keys(site.current).length) {
            site.current.lang = site.lang[0].value;
          }

          var $input = $('<select>', {id: 'target'});
          $.each(site.lang, function(i, v) {
            var label = '';
            switch (v.value) {
            case 'ja':
              label = 'JP';
              break;
            case 'en':
              label = 'EN';
              break;
            }

            if (v.value == site.current.lang) {
              $input.append(
                $('<option/>')
                .val(v.value)
                .html(label)
                .attr('selected', true)
              );
              document.cookie = 'islang='+ site.current.lang+'; path=/;';
            } else {
              $input.append(
                $('<option/>')
                .val(v.value)
                .html(label)
              );
            }
          });

          $input.append('</select>');

          $('#selector').append(
            $('<div/>', {class: 'row'}).append(
              $('<div/>', {class: 'right col m2 s12'}).append($input)
            )
          );

          $('select').formSelect();

          // URLにページ番号が無い場合は最初のページ番号を設定
          if (pno === "") {
            $.each(site.pages, function(k, v) {
              site.current.pno = k;
              return false;
            });

          }else{
            site.current.pno = pno;
          }

          // ページ番号リストを設定
          var i = 0;
          $.each(site.pages, function(k, v) {
            site.pno_list[i] = k;
            i++;
          });
          // パンくずリストの生成
          set_breadcrumbs();

          /**************************************************************************
           * 3) 申込フォーム
           **************************************************************************/

          // サイトタイトル設定
          $('title').html(site.title[site.current.lang] + ' - ' + $('title').html());

          // スタイル設定
          $.each(site.style, function(k, v) {
            $('body').css({'cssText': k + ': ' + v + ' !important;'});
          });

          // ページ生成
          dfd_f = make_page();
        }).then(function() { // 住所検索機能追加
          dfd_f.then(function() {
            if (site.current.lang === 'ja') {
              set_event_address();
            } else {
              $('[name="state"]').remove();
            }
          });
        }).then(function() {
          dfd_f.then(function() {
            // 言語切り替え
            $('#target').on('change', function() {
              $.confirm({
                title: CONFIRMATION_SWICH_LANG[site.current.lang].title,
                content: CONFIRMATION_SWICH_LANG[site.current.lang].content,
                buttons: {
                  formSubmit: {
                    text: CONFIRMATION_SWICH_LANG[site.current.lang].buttons.confirm,
                    btnClass: 'waves-effect waves-light blue',
                    action: function() {
                      site.current.lang = $('#target').val();
                      document.cookie = 'islang='+ site.current.lang+'; path=/;';
                      $('title').html(site.title[site.current.lang] + ' - ' + $('title').html());
                      make_page();
                      set_breadcrumbs();

                      $('#btn-back').html(site['labels']['button_back'][site.current.lang]);
                      $('#btn-next').html(site['labels']['button_next'][site.current.lang]);
                      $('#btn-confirm').html(site['labels']['button_confirm'][site.current.lang]);

                      $('#season_off_note').html('<h3>' + OUT_OF_TERM[$('#season_off').val()][site.current.lang] + '</h3>').show();
                    }
                  },
                  'cancel': {
                    text: CONFIRMATION_SWICH_LANG[site.current.lang].buttons.cancel,
                    action: function() {
                      $('#target').val(site.current.lang).formSelect();
                    }
                  }
                }
              });
            });
          });
        });
      });
    });
  });
}
// 共通認証

function common_auth(site, pno ,application_id){

  if (
    !pno
    &&
    !application_id
    &&
    site.common_auth.is_enabled == 1
    &&
    (
      site.common_auth.q1_value[site.current.lang] != ''
      ||
      site.common_auth.q2_value[site.current.lang] != ''
    )
  ) {
    $.confirm({
      title: '共通認証',
      columnClass: 'xsmall',
      content: function() {
        return make_common_auth(site.common_auth);
      },
      buttons: {
        formSubmit: {
          text: '送信',
          btnClass: 'btn-blue',
          action: function () {
            var v1 = true;
            var v2 = true;

            if (site.common_auth.q1_value[site.current.lang] != '') {
              var v1 = ($('[name="q1_value"]').val() === site.common_auth.q1_value[site.current.lang]);
            }

            if (site.common_auth.q2_value[site.current.lang] != '') {
              var v2 = ($('[name="q2_value"]').val() === site.common_auth.q2_value[site.current.lang]);
            }

            if (v1 && v2) {
              // TODO:セッションに共通認証OKの値をセット

              dfd_auth_common.resolve();
              return dfd_auth_common.promise();
            } else {
              $.alert('入力が正しくありません。');

              return false;
            }
          }
        }
      },
      onContentReady: function () {

        return dfd_auth_common.promise();
      }
    });
  } else {
    dfd_auth_common.resolve();
    return dfd_auth_common.promise();
  }
}

// GDPR

function gdpr(site, pno, application_id){
  if (!pno && !application_id && site.gdpr.is_enabled == 1) {
    $.confirm({
      title: '',
      content: function() {
        return make_gdpr(site.gdpr);
      },
      buttons: {
        formSubmit: {
          text: (site.current.lang === 'ja' ? '同意する' : 'Agree'),
          btnClass: 'btn-blue btn-gdpr-agree',
          action: function () {
            dfd_gdpr.resolve();
            return dfd_gdpr.promise();
          }
        },
        Cancel: {
          text: (site.current.lang === 'ja' ? '同意しない' : 'Not Agree'),
          btnClass: 'btn-blue btn-gdpr-not-agree',
          action: function () {
            location.href = site.gdpr.not_agree.return_url;
          }
        }
      },
      onContentReady: function() {
        $('#gdpr_lang').on('change', function() {
          site.current.lang = $(this).val();
          $('.btn-gdpr-agree').html((site.current.lang === 'ja' ? '同意する' : 'Agree'));
          $('.btn-gdpr-not-agree').html((site.current.lang === 'ja' ? '同意しない' : 'Not Agree'));
          $('#gdpr_text').html(site.gdpr.text[site.current.lang]);
          $('#gdpr_not_agree_alert').html(site.gdpr.not_agree.text[site.current.lang]);
        });
      }
    });
  }else{
    dfd_gdpr.resolve();
    return dfd_gdpr.promise();
  }
}