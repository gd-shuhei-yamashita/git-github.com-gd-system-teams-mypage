/** 申込情報検索画面 */
var read_results = [];
/** 編集ユーザパラメータ */
var user_param = [];

// 1 検索条件をもとに通信、検索結果をテーブルに返す
function userlist_refresh(post_data) {
  console.log("userlist_refresh ");
  // $("#result1").hide();
  // console.log(post_data);
  $.ajax({
    url: application_url + "admin/search_application_information",
    type: 'post',
    dataType: 'json',
    data : post_data,
  }).done(function(response){
    console.log(response);
    read_results = response;

    // ページングの情報を計算
    /** 表示件数 */
    var pg_skip  = parseInt(( $('#now_tab').val() == 1) ? $('*[name=display_number]').val() : $('*[name=d_display_number]').val() );
    /** 現在ページ */
    var pg_now_state    = parseInt(read_results.now_state); // num(pg_skip)
    /** 合計件数 */
    var pg_users_counts = parseInt(read_results.users_counts);
    /** 合計副件数 */
    var pg_users_sub_counts = parseInt(read_results.users_sub_counts);
    /** 最大ページ */
    var pg_maxpage = Math.ceil( pg_users_counts / pg_skip);
    /** 表示用 開始位置 */
    var count_skip = pg_now_state * pg_skip + 1;
    /** 表示用 完了位置 */
    var count_take = ((count_skip + pg_skip) > pg_users_counts) ? pg_users_counts : (count_skip + pg_skip-1) ;
    /** */
    var db_accesspoint_now = parseInt(read_results.db_accesspoint_now);

    // 合計件数を表示する
    var append_txt = "";
    if (pg_users_sub_counts > 0) {
      append_txt = ((db_accesspoint_now == 2) ? '個' : '企') + "DBは" + pg_users_sub_counts + "件";
    }
    $('#result1 h5').text('検索結果 : ' + pg_users_counts + '件中 '+count_skip+'～'+count_take+'件 ' + append_txt);

    // ページングを表示する
    var html_paging = PaginationNumber(pg_maxpage , pg_now_state, "paging_click");
    
    $('#result1_pagination').html(html_paging);

    // 代表サンプル：ID result1_list のtbodyの内容をクリア、取得したリストに入れ替える
    $('table#result1_list tbody tr').remove();
    for(let Loop1 in read_results.users) {
      var temp_linedata = read_results.users[Loop1];
      // console.log(temp_linedata);
      
      // contract 契約データ から supplypoint_code 供給地点特定番号 の一覧を取り出す\
      var supplypoint_code = [];
      var role_icon = (temp_linedata['role'] == 1) ? 'star' : (temp_linedata['role'] == 2) ? 'security' : (temp_linedata['role'] == 9) ? 'person' : '';
      for(let Loop2 in temp_linedata['contract']){
        supplypoint_code.push(temp_linedata['contract'][Loop2]['supplypoint_code'] + " : <br/>" + temp_linedata['contract'][Loop2]['address']); 
      }

      // どのdbからからの出力かの表示をアイコン内に行う
      db_from="";
      db_from_param="";
      if (temp_linedata['db_from']){
        db_from = (temp_linedata['db_from'] == 1) ? '個' : '企'; // 1 個人 / 2 企業
        db_from_param = "&db_from=" + temp_linedata['db_from']; // 0:シングル / 1:ダブル|個人 / 2:ダブル|企業
      }
      temp_str = '<tr>';
      // temp_str+= '<td class="right-align">' + '</td>'; // 	
      if (temp_linedata['role'] !== 9) {
        temp_str+= '<td><a><img src="/img/perm_identity_black.svg">' + db_from + '</a></td>'; // 種別
      } else {
        temp_str+= '<td class="right-align"><a href="' + application_url + 'admin/search_application_information/users_peek?customer_code='
        + temp_linedata['customer_code'] + db_from_param + '"><img src="/img/perm_identity_black.svg">' + db_from + '</a></td>'; // 種別 
      }
      temp_str+= '<td class="right-align">' + temp_linedata['customer_code'] + '</td>'; // マイページID	
      temp_str+= '<td class="right-align">' + temp_linedata['name'] + '</td>'; // お客様名	
      temp_str+= '<td class="right-align">' + temp_linedata['phone'] + '</td>'; // ご連絡先電話番号	
      temp_str+= '<td class="right-align">' + temp_linedata['email'] + '</td>'; // メールアドレス	
      temp_str+= '<td class="right-align">' + temp_linedata['zip_code'] + '</td>'; // 郵便番号	
      temp_str+= '<td class="left-align">&nbsp;&nbsp;' + supplypoint_code.join(' <br/>/ ') + '</td>'; // 供給地点特定番号	
      // temp_str+= '<td class="left-align">' + ((temp_linedata['email_verified_at'] != null) ? "○" : "未") + '</td>'; // 初回登録済みか	  
      if (temp_linedata['role'] == 1 || (temp_linedata['role'] == 2  )) {
        temp_str+= '<td>&nbsp;</td>'
      } else {
        temp_str+= '<td class="left-align"><button type="button" class="waves-effect waves-light btn-floating edit-btn" id="user_' + temp_linedata['id'] + '">メニュー</button></td>'; // 編集ボタン
      }   
      temp_str+= '</tr>';

      $('table#result1_list tbody').append( temp_str );
    }

    $("#result1").fadeIn(300);

  }).fail(function(error){
    console.log("error:");
    console.log(error);
    M.toast({html: '<i class="material-icons left">error</i> サーバ間通信で不具合が発生しました', classes: 'red darken-3'});
  });

}

/**
 * ページ番号 クリック
 * @param {*} $val 
 */
function paging_click($val){
  // console.log($val);
  // preventDefault();
  // console.log( $('#now_tab').val() );
  if ($('#now_tab').val() == 1) {
    search_click_a($val);
  } else {
    search_click_b($val);
  }
}

/**
 * ページ番号変更
 * @param {*} $val 
 */
function number_change(){
  // console.log($val);
  // preventDefault();
  // console.log( $('#now_tab').val() );
  if ($('#now_tab').val() == 1) {
    search_click_a(0);
  } else {
    search_click_b(0);
  }  
}

/**
 * 簡易
 * @param {*} val 
 */
function search_click_a(val){
  var post_data = {};
  post_data['_token'] = _token;
  post_data['type']   = 0;
  post_data['customer_code']    = $('*[name=customer_code]').val();
  post_data['supplypoint_code'] = $('*[name=supplypoint_code]').val();
  post_data['email']          = "";
  post_data['zip_code']       = "";
  post_data['customer_name']  = "";
  post_data['phone']          = "";
  post_data['search_testuser']   = "";
  post_data['search_deleteuser'] = "";
  post_data['display_number'] = $('*[name=display_number]').val();
  post_data['now_state']      = val;
  // バリデーション判定
  $('#customer_code_err').html('');
  $('#supplypoint_code_err').html('');
  var error_message = "";
  // マイページID
  if (!((post_data['customer_code'].match(/^[0-9a-zA-Z]{9,10}$/gi)) 
     || post_data['customer_code'].length == 0) ) {
    error_message = '入力できるのは9~10桁の数字とアルファベットです';
    $('#customer_code_err').html(error_message);
  }
  // 供給地点特定番号
  if (!((post_data['supplypoint_code'].length == 22 && post_data['supplypoint_code'].match(/[0-9]{22}/gi)) 
     || post_data['supplypoint_code'].length == 0) ) {
    error_message = '入力できるのは22桁の数字です';
    $('#supplypoint_code_err').html(error_message);
  }

  if (error_message != "") {
    return false;
  }

  // cookieに記録
  $.cookie( "sai_search_post" , JSON.stringify(post_data));

  $('#now_tab').val(1);
  console.log(post_data);
  userlist_refresh(post_data);
}

/**
 * 詳細
 * @param {*} val 
 */
function search_click_b(val){

    // console.log("btn_search " + evt.target.value);

    var post_data = {};
    post_data['_token'] = _token;
    post_data['type']   = 1;
    post_data['customer_code']    = $('*[name=d_customer_code]').val();
    post_data['supplypoint_code'] = $('*[name=d_supplypoint_code]').val();

    post_data['email']          = $('*[name=d_email]').val();
    post_data['zip_code']       = $('*[name=d_zip_code]').val();
    post_data['customer_name']  = $('*[name=d_customer_name]').val();
    post_data['phone']          = $('*[name=d_phone]').val();
    post_data['search_testuser']   = $('*[name=d_search_testuser]').prop('checked')?1:0;
    post_data['search_deleteuser'] = $('*[name=d_search_deleteuser]').prop('checked')?1:0;
    post_data['display_number'] = $('*[name=d_display_number]').val();
    post_data['now_state']      = val;


    // バリデーション判定
    $('#d_customer_code_err').html('');
    $('#d_supplypoint_code_err').html('');
    $('#d_email_err').html('');
    $('#d_zip_code_err').html('');
    $('#d_customer_name_err').html('');
    $('#d_phone_err').html('');
    var error_message = "";
    // マイページID
    if (!((post_data['customer_code'].match(/^[0-9a-zA-Z]{9,10}$/gi)) 
       || post_data['customer_code'].length == 0) ) {
      error_message = '入力できるのは9~10桁の数字とアルファベットです';
      $('#d_customer_code_err').html(error_message);
    }
    // 供給地点特定番号
    if (!((post_data['supplypoint_code'].length == 22 && post_data['supplypoint_code'].match(/[0-9]{22}/gi)) 
       || post_data['supplypoint_code'].length == 0) ) {
      error_message = '入力できるのは22桁の数字です';
      $('#d_supplypoint_code_err').html(error_message);
    }
    // メールアドレス
    if (!((post_data['email'].length > 0 && post_data['email'].match(/^[!-~]*$/gi)) 
       || post_data['email'].length == 0) ) {
      error_message = '入力できるのは半角文字列だけです';
      $('#d_email_err').html(error_message);
    }
    // 郵便番号: 7桁の単なる数字だった場合、間に-ハイフンを入れて調整する
    if (post_data['zip_code'].length == 7 && post_data['zip_code'].match(/[0-9]{7}/gi)) {
      // 更新
      post_data['zip_code'] = post_data['zip_code'].substr(0,3) + '-' +post_data['zip_code'].substr(3,4);
      $('*[name=d_zip_code]').val(post_data['zip_code']);
    }
    // 郵便番号
    if (!((post_data['zip_code'].length == 8 && post_data['zip_code'].match(/[0-9]{3}\-[0-9]{4}/gi)) 
       || post_data['zip_code'].length == 0) ) {
      error_message = '入力できるのは7桁の数字です';
      $('#d_zip_code_err').html(error_message);
    }
    // お客様名->禁則なし
    // 電話番号:10桁の単なる数字だった場合間に-ハイフンを入れて調整する (03-1234-5678)
    if (post_data['phone'].length == 10 && post_data['phone'].match(/[0-9]{10}/gi)) {
      // 更新
      post_data['phone'] = post_data['phone'].substr(0,2) + '-' + post_data['phone'].substr(2,4) + '-'
       + post_data['phone'].substr(6,4);
      $('*[name=d_phone]').val(post_data['phone']);
    }
    // 電話番号:11桁の単なる数字だった場合間に-ハイフンを入れて調整する (090-1234-5678)
    if (post_data['phone'].length == 11 && post_data['phone'].match(/[0-9]{11}/gi)) {
      // 更新
      post_data['phone'] = post_data['phone'].substr(0,3) + '-' + post_data['phone'].substr(3,4) + '-'
       + post_data['phone'].substr(7,4);
      $('*[name=d_phone]').val(post_data['phone']);
    }
    // 電話番号
    if (!((post_data['phone'].length >= 10 && post_data['phone'].match(/^[0-9\-]+$/gi)) 
       || post_data['phone'].length == 0) ) {
      error_message = '入力できるのは10桁以上の数字とハイフンです';
      $('#d_phone_err').html(error_message);
    }

    if (error_message != "") {
      return false;
    }
    // cookieに記録
    $.cookie( "sai_search_post" , JSON.stringify(post_data));
    
    $('#now_tab').val(2);
    userlist_refresh(post_data);
}

/** コンストラクタ */
$(function(){
  // M.toast({html: '開発中'});
  // $('.tabs').tabs();
  $('select').formSelect();

  // 1a. 検索ボタン押下時(簡易) イベント
  $('#btn_search').click(function(evt) {
    evt.preventDefault();
    console.log("btn_search ");
    search_click_a(0);

  });

  // 1b. 検索ボタン押下時(詳細) イベント
  $('#btn_search_detail').click(function(evt) {
    evt.preventDefault();
    console.log("btn_search_detail ");
    search_click_b(0);
  });

  // 表のイベント追加
  // ex. jQueryで動的に追加した要素はクリックイベントが発火しない？いやそんなことはないぞ https://qiita.com/ayies128/items/5d044bc08b9308767f4c  
  // 更新される要素内を常に判定する
  $('#result1_list').on("click", ".edit-btn", function (evt) {
    var now_id = $(this).attr("id").replace("user_", ""); // ID取得 ex.javascript id取得 jquery http://www.4web8.com/4441.html
    console.log("edit_button_clicked:", now_id);
    // 読み出されている read_results.users　から各種パラメータ取得
    user_param = null;
    for (let Loop1 in read_results.users) {
      var temp_linedata = read_results.users[Loop1];
      if (temp_linedata["id"] == now_id) {
        user_param = temp_linedata;
      }
    }

    $('#edit_serial').text( now_id ); // ID表示　
    if (!user_param) {
      M.toast({html: '<i class="material-icons left">error</i> 編集を行うための情報が取得できませんでした', classes: 'red darken-3'});
    } else {
      $('#edit_username').text( user_param['name'] ); // 名前表示　
      $('#edit_deleted').prop("checked", (user_param['deleted_at'] != null) ? true : false ); // 削除フラグ 表示　
      $('#edit_ninshou').prop("checked", (user_param['email_verified_at'] != null) ? true : false ); // 初回登録済みか 表示　
    }
    // モーダル立ち上げる ex. materializecss modal open  https://stackoverflow.com/questions/40430576/how-i-can-open-a-materialize-modal-when-a-window-is-ready
    $('.modal').modal('open');
    $('#edit_changed').attr("disabled", true);
  });
  
  // モーダル内スイッチ変更 (削除フラグ edit_deleted)
  $('#edit_deleted').change(function (evt) {
    console.log("edit_deleted changed:");
    $('#edit_changed').attr("disabled", false);
  });

  // モーダル内スイッチ変更 (初回認証 edit_ninshou)
  $('#edit_ninshou').change(function (evt) {
    console.log("edit_ninshou changed:");
    $('#edit_changed').attr("disabled", false);
  });

  // モーダル内更新反映ボタン
  $('#edit_changed').click(function (evt) {
    console.log("edit_changed clicked");
    // ToDo:通信実施
    var post_data = {
      '_token': _token,
      'user_id' : $('#edit_serial').text(),
      "deleted" : $('#edit_deleted').prop("checked"),
      "ninshou" : $('#edit_ninshou').prop("checked"),
    };
    console.log("post_data:", post_data);

    $.ajax({
      url: application_url + "admin/search_application_information/store",
      type: 'post',
      dataType: 'json',
      data: post_data,
    }).done(function (response) {
      console.log(response);
      read_results = response;
      M.toast({ html: 'ユーザステータスを更新いたしました。' });
      number_change(); // 表示反映
    }).fail(function(error){
      console.log("error:");
      console.log(error);
      M.toast({html: '<i class="material-icons left">error</i> サーバ間通信で不具合が発生しました', classes: 'red darken-3'});
    });;

  });

  // ページ表示数変更時
  $("#display_number").change(function(){
    $('#now_tab').val(1);
    number_change();
  });
  // ページ表示数変更時
  $("#d_display_number").change(function(){
    $('#now_tab').val(2);
    number_change();
  });
  
  // クッキーに合わせて表示設定
  $postget = JSON.parse( $.cookie( "sai_search_post") );
  // console.log($postget );

  // マイページID (簡易/詳細)
  if ($postget.customer_code != null) {
    $('*[name=customer_code]').val($postget.customer_code);
    $('*[name=d_customer_code]').val($postget.customer_code);
  }

  // 供給地点特定番号 (簡易/詳細)
  if ($postget.supplypoint_code != null) {
    $('*[name=supplypoint_code]').val($postget.supplypoint_code);
    $('*[name=d_supplypoint_code]').val($postget.supplypoint_code);
  }
  
  // メールアドレス  
  // post_data['email']          = $('*[name=d_email]').val();
  if ($postget.email != null) {
    $('*[name=d_email]').val($postget.email);
  }

  // 郵便番号  
  // post_data['zip_code']       = $('*[name=d_zip_code]').val();
  if ($postget.zip_code != null) {
    $('*[name=d_zip_code]').val($postget.zip_code);
  }

  // お客様名  
  // post_data['customer_name']  = $('*[name=d_customer_name]').val();
  if ($postget.customer_name != null) {
    $('*[name=d_customer_name]').val($postget.customer_name);
  }

  // 電話番号  
  // post_data['phone']          = $('*[name=d_phone]').val();
  if ($postget.phone != null) {
    $('*[name=d_phone]').val($postget.phone);
  }

  // 管理者／テストユーザ検索を行う  
  if ($postget.search_testuser == 1) {
    $('*[name=d_search_testuser]').prop('checked', true);
  }

  // 削除済みユーザの検索を行う  
  if ($postget.search_deleteuser == 1) {
    $('*[name=d_search_deleteuser]').prop('checked', true);
  }
  
  // 画面起動時に全件表示させる
  // search_click_a(0);
  if ($postget.type == 1) {
    // タブ2詳細 表示/検索条件での表示  
    $('#tab2').addClass('active');
    search_click_b(0);
    console.log("tab2 type" );
  } else {
    // タブ1簡易 表示/検索条件での表示
    $('#tab1').addClass('active');
    search_click_a(0);
    console.log("tab1 type" );
  }

});
