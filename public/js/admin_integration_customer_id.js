/** 顧客ID統合制御画面 */
var read_results = [];

// 1 検索条件をもとに通信、検索結果をテーブルに返す
function userlist_refresh(post_data) {
  console.log("userlist_refresh ");
  // $("#result1").hide();
  // console.log(post_data);
  $.ajax({
    url: application_url + "admin/integration_customer_id",
    type: 'post',
    dataType: 'json',
    data : post_data,
  }).done(function(response){
    console.log(response);
    read_results = response;

    // ページングの情報を計算
    /** 表示件数 */
    var pg_skip  = parseInt( $('*[name=display_number]').val() );
    /** 現在ページ */
    var pg_now_state    = parseInt(read_results.now_state); // num(pg_skip)
    /** 合計件数 */
    var pg_parent_child_counts = parseInt(read_results.parent_child_counts);
    /** 最大ページ */
    var pg_maxpage = Math.ceil( pg_parent_child_counts / pg_skip);
    /** 表示用 開始位置 */
    var count_skip = pg_now_state * pg_skip + 1;
    /** 表示用 完了位置 */
    var count_take = ((count_skip + pg_skip) > pg_parent_child_counts) ? pg_parent_child_counts : (count_skip + pg_skip-1) ;


    // 合計件数を表示する
    $('#result1 h5').text(' ' + pg_parent_child_counts + '件 のうち '+count_skip+'～'+count_take+'件');

    // ページングを表示する
    var html_paging = PaginationNumber(pg_maxpage , pg_now_state, "paging_click");
    
    $('#result1_pagination').html(html_paging);

    // 代表サンプル：ID result1_list のtbodyの内容をクリア、取得したリストに入れ替える
    $('table#result1_list tbody tr').remove();
    for(let Loop1 in read_results.parent_child) {
      var temp_linedata = read_results.parent_child[Loop1];
      // console.log(temp_linedata);

      temp_str = '<tr>';
      temp_str+= '<td class="right-align"><a href="#" onclick="parent_child_click(' + temp_linedata['id']
       +'); return false;"><img src="/img/perm_identity_black.svg"></a></td>'; // 編集
      temp_str+= '<td class="right-align"><a href="' + application_url + 'admin/integration_customer_id/delete?cid='
       + temp_linedata['id'] + '"><img src="/img/link_black_24dp.svg"></a></td>'; // 解除	
      temp_str+= '<td class="right-align">' + temp_linedata['id'] + '</td>'; // ID	
      temp_str+= '<td class="right-align">' + temp_linedata['parent_customer_code'] + '</td>'; // 親_顧客ID	
      temp_str+= '<td class="right-align">' + temp_linedata['child_customer_code'] + '</td>'; // 子_顧客ID	
      temp_str+= '<td class="left-align">' + temp_linedata['change_result'].replace(/(\\r)?\\n/g, '<br>') + '</td>'; // 変更詳細	
      // temp_str+= '<td class="right-align">' + temp_linedata['created_at'] + '</td>'; // 作成時刻	
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
 * お知らせ記事のクリック (更新モード)
 * @param {*} $val 
 */
function parent_child_click($val){
  var post_data = {};
  post_data['_token'] = _token;
  post_data['now_state']      = 0;
  post_data['now_cid']        = $val;
  post_data['display_number'] = 1;

  // フォームの値を所定IDに更新する。  
  $.ajax({
    url: application_url + "admin/integration_customer_id",
    type: 'post',
    dataType: 'json',
    data : post_data,
  }).done(function(response){
    M.toast({html: '変更対象の記事を選択しました ID:'+$val});  
    // console.log(response);
    read_results = response;
    var read_parent_child = read_results["parent_child"][0];
    console.log(read_parent_child);
    // cid
    $('#cid').val(read_parent_child.id);
    // 既存記事の更新と見出しを変更する
    $("#parent_child_heading").text("記事変更：ID " + read_parent_child.id);
    $("#btn_entry span").text("変更");

    // parent_customer_code
    $('#parent_customer_code').val( read_parent_child.parent_customer_code );
    
    // child_customer_code
    $('#child_customer_code').val( read_parent_child.child_customer_code );

    // change_result
    $('#change_result').val( DbtextToTextarea(read_parent_child.change_result) );
    // ex.  "When dynamically changing the value of a textarea " https://materializecss.com/text-inputs.html
    M.textareaAutoResize($('#change_result'));

  }).fail(function(error){
    console.log("error:");
    console.log(error);
    M.toast({html: '<i class="material-icons left">error</i> サーバ間通信で不具合が発生しました', classes: 'red darken-3'});
  });

  return false;
}

/**
 * ページ番号 クリック
 * @param {*} $val 
 */
function paging_click($val){
  // console.log($val);
  // preventDefault();
  search_click_a($val);
}

/**
 * ページ番号変更
 * @param {*} $val 
 */
function number_change(){
  // console.log($val);
  // preventDefault();
  search_click_a(0);
}

/**
 * 簡易
 * @param {*} val 
 */
function search_click_a(val){
  var post_data = {};
  post_data['_token'] = _token;
  // post_data['customer_code']    = $('*[name=customer_code]').val();
  // post_data['supplypoint_code'] = $('*[name=supplypoint_code]').val();
  // post_data['email']          = "";
  // post_data['zip_code']       = "";
  // post_data['customer_name']  = "";
  // post_data['phone']          = "";
  post_data['display_number'] = $('*[name=display_number]').val();
  post_data['now_state']      = val;
  // // バリデーション判定
  // $('#customer_code_err').html('');
  // $('#supplypoint_code_err').html('');
  // var error_message = "";
  // // マイページID
  // if (!((post_data['customer_code'].length == 10 && post_data['customer_code'].match(/[0-9a-zA-Z]{10}/gi)) 
  //    || post_data['customer_code'].length == 0) ) {
  //   error_message = '入力できるのは10桁の数字とアルファベットです';
  //   $('#customer_code_err').html(error_message);
  // }
  // // 供給地点特定番号
  // if (!((post_data['supplypoint_code'].length == 22 && post_data['supplypoint_code'].match(/[0-9]{22}/gi)) 
  //    || post_data['supplypoint_code'].length == 0) ) {
  //   error_message = '入力できるのは22桁の数字です';
  //   $('#supplypoint_code_err').html(error_message);
  // }

  // if (error_message != "") {
  //   return false;
  // }
  $('#now_tab').val(1);
  console.log(post_data);
  userlist_refresh(post_data);
}


/** コンストラクタ */
$(function(){
  // M.toast({html: '開発中'});
  // $('.tabs').tabs();

  // 画面起動時に全体表示させる
  search_click_a(0);

  // // 1a. 検索ボタン押下時(簡易) イベント
  // $('#btn_search').click(function(evt) {
  //   evt.preventDefault();
  //   console.log("btn_search ");
  //   search_click_a(0);

  // });

  // // 1b. 検索ボタン押下時(詳細) イベント
  // $('#btn_search_detail').click(function(evt) {
  //   evt.preventDefault();
  //   console.log("btn_search_detail ");
  //   search_click_b(0);
  // });
  
  // ページ表示数変更時
  $("#display_number").change(function(){
    $('#now_tab').val(1);
    number_change();
  });

});
