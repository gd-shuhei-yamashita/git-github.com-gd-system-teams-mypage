/** お知らせ登録画面 */
var read_results = [];

// 1 検索条件をもとに通信、検索結果をテーブルに返す
function noticelist_refresh(post_data) {
  console.log("noticelist_refresh ");
  // $("#result1").hide();
  // console.log(post_data);
  $.ajax({
    url: application_url + "admin/regist_notice",
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
    $('#result1 h5').text(' ' + pg_notice_counts + '件 のうち '+count_skip+'～'+count_take+'件');

    // ページングを表示する
    var html_paging = PaginationNumber(pg_maxpage , pg_now_state, "paging_click");
    
    $('#result1_pagination').html(html_paging);

    // 代表サンプル：ID result1_list のtbodyの内容をクリア、取得したリストに入れ替える
    $('table#result1_list tbody tr').remove();
    for(let Loop1 in read_results.notice) {
      var temp_linedata = read_results.notice[Loop1];
      // console.log(temp_linedata);
      
      // contract 契約データ から supplypoint_code 供給地点特定番号 の一覧を取り出す\
      var supplypoint_code = [];
      for(let Loop2 in temp_linedata['contract']){
        supplypoint_code.push(temp_linedata['contract'][Loop2]['supplypoint_code'] + " : " + temp_linedata['contract'][Loop2]['address']); 
      }
      temp_str = '<tr>';
      temp_str+= "<td><a href='#' onclick='notice_click("+temp_linedata['id']+"); return false;'>";
      temp_str+= '<img src="/img/edit_black_24dp.svg"></a></td>'; // 編集
      temp_str+= '<td><a href="' + application_url + 'admin/regist_notice/delete?cid='
      + temp_linedata['id'] + '"><img src="/img/delete_black_24dp.svg"></a></td>'; // 削除
      temp_str+= '<td>' + temp_linedata['id'] + '</td>'; // 	
      temp_str+= "<td>" + $.datepicker.formatDate('yy/mm/dd', new Date(temp_linedata['notice_date']) ) + '</td>'; // 	
      temp_str+= '<td>' + temp_linedata['notice_comment'].replace(/(\\r)?\\n/g, '<br>') + '</td>'; // コメント
      if (temp_linedata['url'] == null ) {
        url_string = '';
      } else {
        url_string = temp_linedata['url'];
      }
      temp_str+= '<td>' + url_string + '</td>'; // URL
      if (temp_linedata['send_email_flag']) {
        send_email = '有';
      } else {
        send_email = '無';
      }
      temp_str+= '<td>' + send_email + '</td>'; // メール
      if (temp_linedata['notice_relation']) {
        notice_relation = '一部';
        temp_str+= "<td><a href='#' onclick='download_relation("+temp_linedata['id']+"); return false;'>一部</a></td>";
    } else {
        temp_str+= '<td>' + '全体' + '</td>'; // 対象
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

function download_relation(notice_id) {
  var post_data = {};
  post_data['_token'] = _token;
  post_data['notice_id'] = notice_id;
  $.ajax({
    url: application_url + "admin/regist_notice/download",
    type: 'post',
    dataType: 'json',
    data : post_data,
  }).done(function(response){
    console.log(response);
    read_results = response;

    // 一時的なダウンロードリンク 生成
    $("#file_dl").remove();
    $("div#download_temp_area").append('<a id="file_dl" download="'+response["file_name"]+'" href="" download ></a><br>');
    
    // csv分析結果をダウンロードする。
    downloadCsv("file_dl", response["encoded_csv"]);

  }).fail(function(error){
    console.log("error:");
    console.log(error);
    M.toast({html: '<i class="material-icons left">error</i> サーバ間通信で不具合が発生しました', classes: 'red darken-3'});
  });
}

function check_file_display(){
  if($('input:radio[name="notice_relation"]:checked').val() == 1) {
    $('#upload_section').show();
  } else {
    $('#upload_section').hide();
  }
}

/**
 * お知らせ記事のクリック (更新モード)
 * @param {*} $val 
 */
function notice_click($val){
  var post_data = {};
  post_data['_token'] = _token;
  post_data['now_state']      = 0;
  post_data['now_cid']        = $val;
  post_data['display_number'] = 1;

  // フォームの値を所定IDに更新する。  
  $.ajax({
    url: application_url + "admin/regist_notice",
    type: 'post',
    dataType: 'json',
    data : post_data,
  }).done(function(response){
    M.toast({html: '変更対象の記事を選択しました ID:'+$val});  
    // console.log(response);
    read_results = response;
    var read_notice = read_results["notice"][0];
    console.log(read_notice);
    // cid
    $('#cid').val(read_notice.id);
    // 既存記事の更新と見出しを変更する
    $("#notice_heading").text("記事変更：ID " + read_notice.id);
    $("#btn_entry").html('変更する<img src="/img/arrow_right_black.svg">');

    // notice_date
    $('#notice_date').val($.datepicker.formatDate('yy/mm/dd', new Date(read_notice.notice_date) ) );

    if (read_notice.send_email_flag) {
      $('input[name=send_mail]:eq(1)').prop('checked', true);
    } else {
      $('input[name=send_mail]:eq(0)').prop('checked', true);
    }

    if (read_notice.notice_relation) {
      $('input[name=notice_relation]:eq(1)').prop('checked', true);
    } else {
      $('input[name=notice_relation]:eq(0)').prop('checked', true);
    }
    check_file_display();

    // url
    $('#url').val(read_notice.url);
    
    // notice_comment
    $('#notice_comment').val( DbtextToTextarea(read_notice.notice_comment) );
    // ex.  "When dynamically changing the value of a textarea " https://materializecss.com/text-inputs.html
    M.textareaAutoResize($('#notice_comment'));

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
  // console.log( $('#now_tab').val() );
  search_click_a($val);
  return false;
}

/**
 * ページ表示数変更
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

  // memo : バリデーションをすべて サーバ側の対応に振ったので全てコメント

  // // バリデーション判定
  // $('#customer_code_err').html('');
  // $('#supplypoint_code_err').html('');
  var error_message = "";
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

  if (error_message != "") {
    return false;
  }
  // $('#now_tab').val(1);
  console.log(post_data);
  noticelist_refresh(post_data);
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
    // M.toast({html: '表示数変更'});
    number_change();
  });
  
  check_file_display();
  $('input[name="notice_relation"]').change(function () {
    check_file_display();
  });

  $('*[name=btn_upload_section]').change(function(evt) {
    var reader1file = evt.target.files[0];
    var reader1_status = [];

    reader1_status["name"] = reader1file.name;
    reader1_status["type"] = reader1file.type;
    reader1_status["size"] = reader1file.size;
    console.log(reader1_status);
  
    // 初期バリデーション判定,csvのみ受け付ける
    if (!reader1_status["name"].match(/\.(csv)$/i)) {
      M.toast({html: '<i class="material-icons left">error</i> ファイル形式がcsvと異なります。 ' , classes: 'red darken-3'});
      return 1;
    }
  
    reader1 = new FileReader();
    reader1.onload = function(e) {
      if(reader1.error) {
        console.log("file_reader error ");
      } else {
        console.log("binary_base64:" +  reader1.result );
        $('input[name="file_data"]').val(reader1.result);
      }
    };
    reader1.readAsDataURL(reader1file);
  });

  $('#btn_entry').on('click', function() {
    if($('input:radio[name="send_mail"]:checked').val() == 1 && $('#cid').val() == 0) {
      notice_date = new Date($('#notice_date').val());
      notice_date.setHours(11);
      notice_date.setMinutes(0);
      notice_date.setSeconds(0);
      notice_date.setMilliseconds(0);
      today = new Date();
      if (today < notice_date) {
        $result = confirm('指定日のAM11:00に自動でメールが送信されます。よろしいですか？');
      } else {
        $result = confirm('即時メールが送信されます。よろしいですか？');
      }
      return $result;
    } else {
      return true;
    }
  });

});
