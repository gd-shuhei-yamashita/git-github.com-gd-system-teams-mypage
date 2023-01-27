/** 供給地点特定番号紐付変更画面 */
var read_results = [];

// 1 検索条件をもとに通信、検索結果をテーブルに返す
function userlist_refresh(post_data) {
  console.log("replacement_history_refresh ");
  // $("#result1").hide();
  // console.log(post_data);
  $.ajax({
    url: application_url + "admin/change_supplypoint_linkage",
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
    var pg_replacement_history_counts = parseInt(read_results.replacement_history_counts);
    /** 最大ページ */
    var pg_maxpage = Math.ceil( pg_replacement_history_counts / pg_skip);
    /** 表示用 開始位置 */
    var count_skip = pg_now_state * pg_skip + 1;
    /** 表示用 完了位置 */
    var count_take = ((count_skip + pg_skip) > pg_replacement_history_counts) ? pg_replacement_history_counts : (count_skip + pg_skip-1) ;


    // 合計件数を表示する
    $('#result1 h5').text('検索結果 : ' + pg_replacement_history_counts + '件 のうち '+count_skip+'～'+count_take+'件');

    // ページングを表示する
    var html_paging = PaginationNumber(pg_maxpage , pg_now_state, "paging_click");
    $('#result1_pagination').html(html_paging);

    // 代表サンプル：ID result1_list のtbodyの内容をクリア、取得したリストに入れ替える
    $('table#result1_list tbody tr').remove();
    for(let Loop1 in read_results.replacement_history) {
      var temp_linedata = read_results.replacement_history[Loop1];
      // console.log(temp_linedata);
      
      // contract 契約データ から supplypoint_code 供給地点特定番号 の一覧を取り出す\
      var supplypoint_code = [];
      for(let Loop2 in temp_linedata['contract']){
        supplypoint_code.push(temp_linedata['contract'][Loop2]['supplypoint_code'] + " : " + temp_linedata['contract'][Loop2]['address']); 
      }
      temp_str = '<tr>';
      temp_str+= '<td class="left-align">' + '</td>'; // ID
      temp_str+= '<td class="left-align">' + temp_linedata['old_code'] + '</td>'; // 旧番号
      temp_str+= '<td class="left-align">' + temp_linedata['new_code'] + '</td>'; // 新番号

      temp_str+= '<td class="left-align">&nbsp;' + temp_linedata['df_contract'].replace(/(\r)?\n/g, "<br/>") + '</td>'; // 供給地点特定番号	
      temp_str+= '<td class="left-align">&nbsp;' + temp_linedata['df_billing'].replace(/(\r)?\n/g, "<br/>") + '</td>'; // 供給地点特定番号	
      temp_str+= '<td class="left-align">&nbsp;' + temp_linedata['df_usage_t'].replace(/(\r)?\n/g, "<br/>") + '</td>'; // 供給地点特定番号	

      temp_str+= '<td class="right-align">' + $.datepicker.formatDate('yy/mm/dd', new Date(temp_linedata['created_at']) ) + '</td>'; // メールアドレス	
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
  // if ($('#now_tab').val() == 1) {
    search_click_a($val);
  // } else {
  //   search_click_b($val);
  // }
}

/**
 * ページ番号変更
 * @param {*} $val 
 */
function number_change(){
  // console.log($val);
  // preventDefault();
  // console.log( $('#now_tab').val() );
  // if ($('#now_tab').val() == 1) {
    search_click_a(0);
  // } else {
  //   search_click_b(0);
  // }  
}

/**
 * 簡易
 * @param {*} val 
 */
function search_click_a(val){
  var post_data = {};
  post_data['_token'] = _token;
  post_data['display_number'] = $('*[name=display_number]').val();
  post_data['now_state']      = val;

  console.log(post_data);
  userlist_refresh(post_data);
}


/** コンストラクタ */
$(function(){
  // M.toast({html: '開発中'});
  // // $('.tabs').tabs();

  // // 画面起動時に全体表示させる
  search_click_a(0);

  // // 1a. 検索ボタン押下時(簡易) イベント
  // $('#btn_search').click(function(evt) {
  //   evt.preventDefault();
  //   console.log("btn_search ");
  //   search_click_a(0);

  // });


  
  // ページ表示数変更時
  $("#display_number").change(function(){
    number_change();
  });

});
