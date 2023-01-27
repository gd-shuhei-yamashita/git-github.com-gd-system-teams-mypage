/** 申込情報検索画面 */
var read_results = [];

// 1 検索条件をもとに通信、検索結果をテーブルに返す
function historylist_refresh(post_data) {
  console.log("historylist_refresh ");
  // $("#result1").hide();
  // console.log(post_data);
  $.ajax({
    url: application_url + "admin/search_operation_history",
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
    var pg_users_counts = parseInt(read_results.operation_history_counts);
    /** 最大ページ */
    var pg_maxpage = Math.ceil( pg_users_counts / pg_skip);
    /** 表示用 開始位置 */
    var count_skip = pg_now_state * pg_skip + 1;
    /** 表示用 完了位置 */
    var count_take = ((count_skip + pg_skip) > pg_users_counts) ? pg_users_counts : (count_skip + pg_skip-1) ;


    // 合計件数を表示する
    $('#result1 h5').text('' + pg_users_counts + '件 のうち '+count_skip+'～'+count_take+'件');

    // ページングを表示する
    var html_paging = PaginationNumber(pg_maxpage , pg_now_state, "paging_click");
    
    $('#result1_pagination').html(html_paging);

    // 代表サンプル：ID result1_list のtbodyの内容をクリア、取得したリストに入れ替える
    $('table#result1_list tbody tr').remove();
    for(let Loop1 in read_results.operation_history) {
      var temp_linedata = read_results.operation_history[Loop1];
      // console.log(temp_linedata);
      
      // contract 契約データ から supplypoint_code 供給地点特定番号 の一覧を取り出す\
      var supplypoint_code = [];
      for(let Loop2 in temp_linedata['contract']){
        supplypoint_code.push(temp_linedata['contract'][Loop2]['supplypoint_code'] + " : " + temp_linedata['contract'][Loop2]['address']); 
      }
      temp_str = '<tr>';
      temp_str+= '<td class="right-align">' + temp_linedata['id'] + '</td>'; // id	
      temp_str+= '<td class="right-align">' + temp_linedata['created_at'] + '</td>'; // id	
      temp_str+= '<td class="left-align">' + temp_linedata['user_id'] + '</td>'; // マイページID	
      temp_str+= '<td class="left-align">' + temp_linedata['route'] + '</td>'; // route
      temp_str+= '<td class="left-align">' + temp_linedata['method'] + '</td>'; // method	
      temp_str+= '<td class="left-align">' + temp_linedata['file_name'] + '</td>'; // ファイル名	
      temp_str+= '<td class="left-align">' + temp_linedata['status'] + '</td>'; // status	
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
  post_data['customer_code']    = $('*[name=customer_code]').val();
  post_data['notice_date_from'] = $('*[name=notice_date_from]').val();
  post_data['notice_date_to']   = $('*[name=notice_date_to]').val();
  post_data['display_number']   = $('*[name=display_number]').val();
  post_data['now_state']        = val;
  // バリデーション判定
  $('#customer_code_err').html('');
  // $('#supplypoint_code_err').html('');
  $('#notice_date_from_err').html('');
  $('#notice_date_to_err').html('');

  var error_message = "";
  // マイページID
  if (!((post_data['customer_code'].match(/^[0-9a-zA-Z]{9,10}$/gi)) 
     || post_data['customer_code'].length == 0) ) {
    error_message = '入力できるのは9~10桁の数字とアルファベットです';
    $('#customer_code_err').html(error_message);
  }
  // 表示期間 from
  if (!((post_data['notice_date_from'].length > 8 && post_data['notice_date_from'].match(/[0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2}/gi)) 
     || post_data['notice_date_from'].length == 0 ) ) {
    error_message = '入力できるのは日付だけです';
    $('#notice_date_from_err').html(error_message);
  }
  // 表示期間 to
  if (!((post_data['notice_date_to'].length > 8 && post_data['notice_date_to'].match(/[0-9]{4}\/[0-9]{1,2}\/[0-9]{1,2}/gi)) 
     || post_data['notice_date_to'].length == 0 ) ) {
    error_message = '入力できるのは日付だけです';
    $('#notice_date_to_err').html(error_message);
  }
  // ToDo:表示期間 from to 大小関係が逆の位置なら入れ替える
  

  if (error_message != "") {
    return false;
  }
  $('#now_tab').val(1);
  console.log(post_data);
  historylist_refresh(post_data);
}

/**
 * 詳細
 * @param {*} val 
 */
function search_click_b(val){

    // console.log("btn_search " + evt.target.value);

    var post_data = {};
    post_data['_token'] = _token;
    post_data['customer_code']    = $('*[name=d_customer_code]').val();
    post_data['supplypoint_code'] = $('*[name=d_supplypoint_code]').val();

    post_data['email']          = $('*[name=d_email]').val();
    post_data['zip_code']       = $('*[name=d_zip_code]').val();
    post_data['customer_name']  = $('*[name=d_customer_name]').val();
    post_data['phone']          = $('*[name=d_phone]').val();
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
    $('#now_tab').val(2);
    historylist_refresh(post_data);
}

/** コンストラクタ */
$(function(){
  // M.toast({html: '開発中'});
  // $('.tabs').tabs();

  // 画面起動時に全体表示させる
  search_click_a(0);

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

});
