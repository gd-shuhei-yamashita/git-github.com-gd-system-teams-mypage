/** 請求データ取込 */

/** コンストラクタ */
$(function(){
  // M.toast({html: '開発中'});
});

var read_results = [];

/**
 * 表示リフレッシュ
 */
function loader1_refresh() {
  console.log("loader1_refresh");
  //console.log(read_results);

  // 表のデータが、read_results にあるので一覧に反映する
  // 合計件数表示 (100/0/100)  
  $icons = '<i class="material-icons left red-text">report_problem</i>';
  if (read_results.status == 200) {
    $icons = '<i class="material-icons left green-text">done</i>';
  }
  $('#result1 h5').html($icons + reader1_status["name"] + " 取り込み結果 (TOTAL:"+read_results.results[0]+" INSERT:"+read_results.results[1]
    +"/UPDATE:"+read_results.results[2]+"/NG:"+read_results.results[3]+")");

  // 代表サンプル：ID result1_list のtbodyの内容をクリア、取得したリストに入れ替える
  $('table#result1_list tbody tr').remove();
  for(let i in read_results.sheet_data) {
    var temp_linedata = read_results.sheet_data[i];
    $('table#result1_list tbody').append(
        '<tr>'
      +'<td>'+ temp_linedata[0] +'</td>' // line
      +'<td>'+ temp_linedata[1] +'</td>'
      +'<td>'+ temp_linedata[2] +'</td>'
      +'<td>'+ temp_linedata[3] +'</td>'
      +'<td>'+ temp_linedata[4] +'</td>'
      +'<td>'+ temp_linedata[5] +'</td>'
      +'<td>'+ temp_linedata[6] +'</td>'
      +'<td>'+ temp_linedata[7] +'</td>'
      +'<td>'+ temp_linedata[8] +'</td>'
      +'<td>'+ temp_linedata[9] +'</td>'
      +'<td>'+ temp_linedata[10] +'</td>'
      +'<td>'+ temp_linedata[11] +'</td>'
      +'<td>'+ temp_linedata[12] +'</td>'
      +'<td>'+ temp_linedata[13] +'</td>'
      +'<td>'+ temp_linedata[14] +'</td>'
      +'<td>'+ temp_linedata[15] +'</td>'
      +'<td>'+ temp_linedata[16] +'</td>'
      +'<td>'+ temp_linedata[17] +'</td>'
      +'<td>'+ temp_linedata[18] +'</td>'
      +'<td>'+ temp_linedata[19] +'</td>'
      +'<td>'+ temp_linedata[20] +'</td>' // エラーメッセージ等
      +'</tr>'
      );
  }
  
  $("#result1").fadeIn(300);
}

/** 関連 */
var reader1 = new FileReader();
var reader1_status = [];
// $('*[name=btn_upload_section]').val();
$('*[name=btn_upload_section]').change(function(evt) {
  var reader1file = evt.target.files[0];
  // console.log("file:" + reader1file.name);
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
    }
  };
  reader1.readAsDataURL(reader1file);
});

/** 1 使用量データ取込 */
// 1. 入力チェック
// エラーあり エラーメッセージを表示し、処理を中断する
// 2. DB登録[sections]
// ※ INSERT , すでに登録されていたら上書き

//  -> /admin/organizations/sectionに通信
$('#submit_section').submit(function(event) {
  console.log("1:section resistration: " );
  event.preventDefault();
  
  $("#btn_entry").prop('disabled', true);
  $("#progress_line").fadeIn(300);
  // ファイル名取得
  // var fname = $('#btn_upload_section')[0].files[0].name;
  // console.log(fname);

  // 処理
  var post_data = {};
  post_data['_token'] = _token;
  post_data['file_name'] = reader1_status["name"];
  post_data['btn_upload_section'] = reader1.result;

  // バリデーション判定
  $('.error').html('');
  var error_message = "";
  if (!post_data['btn_upload_section']) {
    error_message = '請求データ の csvファイルを登録して下さい';
    $('#btn_upload_section_err').html(error_message);
  }
  if (error_message != "") {
    return false;
  }

  // 更新
  console.log(post_data);
  // data : JSON.stringify(post_data),
  // postする
  $.ajax({
    url: application_url + "admin/capture_billingdata",
    type: 'post',
    dataType: 'json',
    data : post_data,
  }).done(function (response) {
    $("#btn_entry").prop('disabled', false);
    $("#progress_line").hide();
    console.log(response);
    if (response.status == 200) {
      $("#upload_section input[type=text]").val(""); // テキストフォームのファイル名表示を消す
      reader1 = new FileReader(); // jsで読み込んだデータを消す
           
      // // ファイル名初期化
      // $('input[type=file]').val('');
      // // IE バージョン判定 10以下
      // if (navigator.userAgent.match(/MSIE\s(7|8|9|10)\./i)) {
      //   $('#userfile_item').html('<input type="file" name="userfile">');
      // }

      // M.toast({html: '請求データ 登録完了しました'});
      alert('請求データ 登録完了しました');
      // response.sheet_data を代入
      read_results = response;
      // csv分析結果を一覧に表示する。
      loader1_refresh();
    } else {
      error_message = response.message;
      M.toast({html: '<i class="material-icons left">error</i>' + error_message, classes: 'red darken-3'});

      if (response.status == 409) {
        $('#btn_upload_section_err').html(error_message);
        $("#result1").hide();
      } else {
        $('#btn_upload_section_err').html(error_message);
        // データが受理され処理された場合
        // response.sheet_data を代入
        read_results = response;
        // csv分析結果を一覧に表示する。
        loader1_refresh();
      }

    }

  }).fail(function (error) {
    $("#btn_entry").prop('disabled', false);
    $("#progress_line").hide();
    console.log("error:");
    console.log(error);
    M.toast({html: 'Error Occured.'});
  });
});
