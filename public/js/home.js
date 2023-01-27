/** ホーム画面 */
var read_results = [];

// 1 検索条件をもとに通信、検索結果をテーブルに返す
function noticelist_refresh(post_data) {
  console.log("noticelist_refresh ");
  // $("#result1").hide();
  // console.log(post_data);
  $.ajax({
    url: application_url + "home",
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
    var pg_notice_counts = parseInt(read_results.notice_counts);
    /** 最大ページ */
    var pg_maxpage = Math.ceil( pg_notice_counts / pg_skip);
    /** 表示用 開始位置 */
    var count_skip = pg_now_state * pg_skip + 1;
    /** 表示用 完了位置 */
    var count_take = ((count_skip + pg_skip) > pg_notice_counts) ? pg_notice_counts : (count_skip + pg_skip-1) ;


    // 合計件数を表示する
    // $('#result1 h5').text(' ' + pg_notice_counts + '件 のうち '+count_skip+'～'+count_take+'件');

    // ページングを表示する
    var html_paging = PaginationNumber(pg_maxpage , pg_now_state, "paging_click");
    
    // $('#result1_pagination').html(html_paging);

    // 代表サンプル：ID result1_list のtbodyの内容をクリア、取得したリストに入れ替える
    $('#result1_list li').remove();
    for(let Loop1 in read_results.notice) {
      var temp_linedata = read_results.notice[Loop1];
      // console.log(temp_linedata);
      
      // contract 契約データ から supplypoint_code 供給地点特定番号 の一覧を取り出す\
      var supplypoint_code = [];
      for(let Loop2 in temp_linedata['contract']){
        supplypoint_code.push(temp_linedata['contract'][Loop2]['supplypoint_code'] + " : " + temp_linedata['contract'][Loop2]['address']); 
      }

      /** コメント */
      var temp_head = temp_linedata['notice_comment'].replace(/(\\r)?\\n/g, '');

      /** お知らせ  */
      temp_str = '<li>';
      temp_str+= '<span class="date">' + $.datepicker.formatDate('yy/mm/dd', new Date(temp_linedata['notice_date']) ) + '</span>'; // 	日付
      // temp_str+= '<div class="col m10 s8">';
      temp_str+= (temp_linedata['url']) ? '<a target="_blank" href="'+temp_linedata['url']+'">' + temp_head + '</a>' : temp_head; // URL
      temp_str+= '</li>'; 
      // temp_str+= '</div>';

      $('#result1_list ').append( temp_str );
    }

    // $("#result1").fadeIn(300);

  }).fail(function(error){
    console.log("error:");
    console.log(error);
    M.toast({html: '<i class="material-icons left">error</i> サーバ間通信で不具合が発生しました', classes: 'red darken-3'});
  });

}

// ご請求金額の表示 Billing amount
function billing_amount_refresh(post_data_b) {
  // 請求金額の読み込み
  // ajaxで読み込む。通信先は home/billing_amount 。
  $.ajax({
    url: application_url + "home/billing_amount",
    type: 'post',
    dataType: 'json',
    data : post_data_b,
  }).done(function(response){
    console.log(response);
    read_results = response;

    $(".service").empty();
    temp_str = '';
    $.each(read_results['contracts'], function(key, contract){
      temp_str += '<tr>';
      if (contract['pps_type']) {
        if (contract['pps_type'] == 1 || contract['pps_type'] == 5) {
          temp_str += '<td><i class="fa-regular fa-lightbulb"></i>' + contract['plan'] + '</td>';
        } else if (contract['pps_type'] == 2 || contract['pps_type'] == 3 || contract['pps_type'] == 4) {
          temp_str += '<td><i class="fa-solid fa-fire"></i>' + contract['plan'] + '</td>';
        } else if (contract['supplypoint_code'].substring(0,2) == 'GP') {
          temp_str += '<td><i class="fa-solid fa-wifi"></i>' + contract['plan'] + '</td>';
        } else {
          temp_str += '<td>' + contract['plan'] + '</td>';
        }
      } else {
        if (contract['supplypoint_code'].length == 22) {
          temp_str += '<td><i class="fa-regular fa-lightbulb"></i>' + contract['plan'] + '</td>';
        } else if (contract['supplypoint_code'] == 'wifi') {
          temp_str += '<td><i class="fa-solid fa-wifi"></i>' + contract['plan'] + '</td>';
        } else if (contract['supplypoint_code'].substring(0,2) == 'GP') {
          temp_str += '<td><i class="fa-solid fa-wifi"></i>' + contract['plan'] + '</td>';
        } else if (contract['supplypoint_code'] == 0) {
          temp_str += '<td>' + contract['plan'] + '</td>';
        } else {
          temp_str += '<td><i class="fa-solid fa-fire"></i>' + contract['plan'] + '</td>';
        }
      }
      // else {
        // temp_str += '<td>' + contract['plan'] + '</td>';
      // }

      temp_str += '<td>';
      temp_str += '<div class="t_flex">';
      if (contract['contract_billing_amount'] > 0) {
        temp_str +=  contract['contract_billing_amount'].toLocaleString() + '円';
      } else {
        if (contract['contract_billing_count'] > 0) {
          temp_str +=  '請求データは毎月の20日頃に反映致します。';
        } else {
          temp_str +=  '請求データがありません。請求データは初回請求月の20日に反映いたします。';
        }
      }
      var yyyy = parseInt(read_results["billing_date"].slice(0, 4));
      var mm = parseInt(read_results["billing_date"].slice(4, 6)) - 1;
      if (mm === 0) {
        yyyy -= 1;
        mm = 12;
      }
      mm = (mm < 10 ? '0' : '') + mm;
      temp_str += '<div class="link-btn">';
      temp_str += '<button type="button" onclick="location.href=' 
      + "'" + application_url + 'confirm_usagedata?date=' + yyyy + mm + "&supplypoint_code=" + contract['supplypoint_code'] + "'" 
      + '">詳細<img src="img/arrow_right_black.svg">';
      temp_str += '</button>';
      temp_str += '</div>';
      temp_str += '</div>';
      temp_str += '</td>';
      temp_str += '</tr>';
    })
    $(".service").append( temp_str );
    $("#claim").text(read_results["claim"]);
    if (read_results["billing_amount_total"] > 0) {
      $(".amount").text(read_results["total"]);
    } else {
      if( read_results["billing_count"] > 0){
        $(".amount").text("請求データは毎月の20日頃に反映致します。");
      } else {
        $(".amount").text("請求データがありません。請求データは初回請求月の20日に反映いたします。");
      }
    }

  }).fail(function(error){
    console.log("error:");
    console.log(error);
    M.toast({html: '<i class="material-icons left">error</i> サーバ間通信で不具合が発生しました', classes: 'red darken-3'});
  });
  

}

// 請求データの最古と最新の年月を取得
function billing_range(post_data_b) {
  $.ajax({
    url: application_url + "home/billing_range",
    type: 'post',
    dataType: 'json',
    data : post_data_b,
  }).done(function(response){

    var d = new Date();
    // 日付フォーマット
    var formatteddate = d.getFullYear() + (d.getMonth()+1).toString().padStart(2, '0');

    if (response['latest_billing_date']) {
      post_data_b['billing_date'] = response['latest_billing_date'];
      $("#first_billing_date").val(response['first_billing_date']);
      $("#latest_billing_date").val(response['latest_billing_date']);  
    } else {
      post_data_b['billing_date'] = formatteddate;
      $("#first_billing_date").val(formatteddate);
      $("#latest_billing_date").val(formatteddate);  
    }

    billing_amount_refresh(post_data_b);

    $("#bill_month").val(post_data_b['billing_date']);

    set_month_link()

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
  search_click_a($val);
  return false;
}

/**
 * ページ番号変更
 * @param {*} $val 
 */
function number_change(){
  // console.log($val);
  // preventDefault();
  // console.log( $('#now_tab').val() );
  search_click_a(0);
  return false;
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
  noticelist_refresh(post_data);
}

/**
 * 先月、翌月リンク
 */
function set_month_link(){
  bill_month = $("#bill_month").val();
  $('#other-month').empty()
  if (bill_month <= $("#first_billing_date").val()) {
    var stack_month_link = '先月 ';
  } else {
    var stack_month_link = '<a href="#" id="bill_last_month">先月</a> ';
  }
  stack_month_link += '<img src="img/code_black.svg">';
  if (bill_month >= $("#latest_billing_date").val()) {
    stack_month_link += ' 翌月';
  } else {
    stack_month_link += ' <a href="#" id="bill_next_month">翌月</a>';
  }
  $('#other-month').append(stack_month_link);
}

/** コンストラクタ */
$(function(){
  // M.toast({html: '開発中'});

  var post_data = {};
  post_data['_token'] = _token;
  post_data['now_state']      = 0;
  post_data['display_number'] = $('*[name=display_number]').val();
  noticelist_refresh(post_data);

  // ページ表示数変更時
  $("#display_number").change(function(){
    number_change();
  });
  
  // ご請求金額の表示 Billing amount
  // month 要素があれば実行する
  if (document.getElementById("month") != null) {

    var post_data_b = {};
    post_data_b['_token'] = _token;

    // 最新の請求月でTOP表示
    billing_range(post_data_b);

    // 先月・翌月の設定
    $(document).on("click", "#bill_last_month",function(){ // 先月
      var bill_month = $("#bill_month").val();
      // 最古以前の明細は閲覧不可
      if (bill_month <= $("#first_billing_date").val()) {
        return false;
      }
      var nd = new Date( bill_month.substr(0,4), bill_month.substr(4,2) -2, 1);

      var post_data = {};
      post_data['_token'] = _token;
      post_data["billing_date"] = nd.getFullYear() + (nd.getMonth()+1).toString().padStart(2, '0');
      billing_amount_refresh( post_data );
      $("#bill_month").val(post_data["billing_date"]);

      set_month_link()

      // M.toast({html: '<i class="material-icons left">error</i> '+post_data["billing_date"]+' bill_last_month', classes: 'red darken-3'});
      return false;
    });
    $(document).on("click", "#bill_next_month", function(){ // 翌月
      var bill_month = $("#bill_month").val();
      // 最新以降の明細は閲覧不可
      if (bill_month >= $("#latest_billing_date").val()) {
        return false;
      }
      var nd = new Date( bill_month.substr(0,4), bill_month.substr(4,2), 1);

      var post_data = {};
      post_data['_token'] = _token;
      post_data["billing_date"] = nd.getFullYear() + (nd.getMonth()+1).toString().padStart(2, '0');
      billing_amount_refresh( post_data );
      $("#bill_month").val(post_data["billing_date"]);

      set_month_link()

      // M.toast({html: '<i class="material-icons left">error</i> '+post_data["billing_date"]+' bill_last_month', classes: 'red darken-3'});
      return false;
    });
  }

});
