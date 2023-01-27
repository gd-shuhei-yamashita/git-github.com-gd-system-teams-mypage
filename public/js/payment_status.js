function get_billing(billing_date_start = null, billing_date_end = null) {
  var post_data = {};
  post_data['_token'] = _token;
  post_data['billing_date_start'] = billing_date_start;
  post_data['billing_date_end'] = billing_date_end;

  $("#status_list").empty();
  var loading_sentence = "<div class='status_month'>";
  loading_sentence += "<div class='title_month'>----年--月分</div>";
  loading_sentence += "<div class='status_detail'>";
  loading_sentence += "<ul>";
  loading_sentence += "<li>";
  loading_sentence += "<p>読み込み中</p>";
  loading_sentence += "</li>";
  loading_sentence += "<li class='amount'>";
  loading_sentence += "<p>ご請求金額(税込)</p>";
  loading_sentence += "<p class='price'> ----- 円</p>";
  loading_sentence += "</li>";
  loading_sentence += "</ul>";
  loading_sentence += "</div></div>";
  $('#status_list').append(loading_sentence);

  $.ajax({
    url: application_url + "payment_status/billing_list",
    type: 'post',
    dataType: 'json',
    data : post_data,
  }).done(function(response){
    $("#status_list").empty();
    var year_list = [];
    var latest_year = '';
    if (response['billing_list'].length == 0) {
      var empty_sentence = "<div class='status_month'>";
      empty_sentence += "<div class='title_month'>----年--月分</div>";
      empty_sentence += "<div class='status_detail'>";
      empty_sentence += "<ul>";
      empty_sentence += "<li>";
      empty_sentence += "<p>請求情報がありません</p>";
      empty_sentence += "</li>";
      empty_sentence += "<li class='amount'>";
      empty_sentence += "<p>ご請求金額(税込)</p>";
      empty_sentence += "<p class='price'> ----- 円</p>";
      empty_sentence += "</li>";
      empty_sentence += "</ul>";
      empty_sentence += "</div></div>";
      $('#status_list').append(empty_sentence);
    } else {
      var dt = new Date();
      var y = dt.getFullYear();
      var m = ("00" + (dt.getMonth()+1)).slice(-2);
      var today = String(y) + String(m);

      $.each(response['billing_list'], function(index, val) {
        date_y = String(val['billing_date']).substring(0,4);
        date_m = String(val['billing_date']).substring(4,6);
        var dayDiff = Number(today) - Number(date_y + date_m);
        if (!latest_year) {
          latest_year = date_y;
        }
        if (!year_list.includes(date_y)) {
          year_list.push(date_y);
        }
        if (latest_year != date_y) {
          return;
        }
        var stack_billings = "<div class='status_month'>";
        stack_billings += "<div class='title_month'>" + date_y + "年" + date_m + "月分" + "</div>";
        stack_billings += "<div class='status_detail'>";

        stack_billings += "<ul>";
        stack_billings += "<li>";
        stack_billings += "<p>支払い状況</p>";
        if (!val['payment_amount']) {
          if ( dayDiff >= 2) {
            stack_billings += "<p class='status_flag'><span class='unpaid'>未払い</span></p>";
          } else {
            stack_billings += "<p class='status_flag'><span class='confirm'>確認中</span></p>";
          }
        } else if (val['billing_amount'] <= val['payment_amount']) {
          stack_billings += "<p class='status_flag'><span class='paid'>支払済</span></p>";
        }
        stack_billings += "</li>";
        stack_billings += "<li class='amount'>";
        stack_billings += "<p>ご請求金額(税込)</p>";
        stack_billings += "<p class='price'>"+ val['billing_amount'] +" 円</p>";

        stack_billings += "</li>";
        stack_billings += "</ul>";
        stack_billings += "<div class='link-btn'>";
        if (date_m == '01') {
          detail_date = String(date_y - 1) + '12';
        } else {
          detail_date = date_y + ("00" + String(date_m - 1)).slice(-2);
        }
        stack_billings += "<button type='button' onclick=location.href='" + "/payment_status/detail?date=" + detail_date + "'>";
        stack_billings += "詳細<img src='img/arrow_right_black.svg'>";
        stack_billings += "</button>";
        stack_billings += "</div></div></div>";
        $('#status_list').append(stack_billings);
      });
    }

    if (!billing_date_start && !billing_date_end) {
      var stack_years = "";
      $('*[name=billing_year]').empty();
      console.log(year_list);
      $.each(year_list, function(index, val) {
        stack_years += "<option value='" + val + "'>" + val + "年</option>";
        console.log(stack_years);
      });
      $('*[name=billing_year]').append(stack_years);
    }

  }).fail(function(error){
    console.log("error: get_billing failed.");
    console.log(error);
    M.toast({html: '<i class="material-icons left">error</i> サーバ間通信で不具合が発生しました', classes: 'red darken-3'});
  });

}

$(function(){

  get_billing();

  $('*[name=billing_year]').on('change', function () {
    $('*[name=billing_month]').val('0');
    get_billing($('*[name=billing_year]').val() + '01', $('*[name=billing_year]').val() + '12');
  });
  $('*[name=billing_month]').on('change', function () {
    var select_month = String($('*[name=billing_month]').val());
    if (select_month != '0') {
      get_billing($('*[name=billing_year]').val() + select_month, $('*[name=billing_year]').val() + select_month);
    } else {
      get_billing($('*[name=billing_year]').val() + '01', $('*[name=billing_year]').val() + '12');
    }
  });
});


/** 利用料データ取込 */
var read_results = [];
var myBarChart = "";
/**
 * 1 使用場所一覧取得 表示リフレッシュ
 */
function usage_refresh() {
  // 使用場所一覧取得
  // 上記x点、 -> /confirm_usagedata/pulldown に通信
  // トークンは_token で取得可能。

  // customer_codeはサーバ側(PHP)でsessionに保持している
  var post_data = {};
  post_data['_token'] = _token;

  // console.log(post_data);
  $.ajax({
    url: application_url + "confirm_usagedata/pulldown",
    type: 'post',
    dataType: 'json',
    data : post_data,
  }).done(function(response){
    // console.log(response);

    // getパラメータに値がある場合、プルダウンを指定のものにして初期化する
    var now_supplypoint_code = getUrlParam("supplypoint_code");
    console.log(now_supplypoint_code);
    var newest_date = new Date(); 
    var now_date = newest_date.getFullYear();
    var now_date_m = newest_date.getMonth() + 1;
    // getUrlParam("date")
    // console.log(now_date_m);
    var use_date_y = 0;
    var use_date_m = 0;
    if (getUrlParam("date") > 201800) {
      param_date_y = getUrlParam("date").substring(0,4);
      param_date_m = getUrlParam("date").substring(4,6);

      if ( Number(param_date_y) < now_date ) {
        use_date_y = Number(param_date_y);
      }
      
      if (Number(param_date_m) >= 1 && Number(param_date_m) <= 12) {
        use_date_m = Number(param_date_m);
      }
    }

    console.log(now_date + '/' + now_date_m );

    // contracts の表示変更
    $("#supplypoint_code select").empty();    
    var stack_contracts = "<option value='' disabled>---------</option>";
    var contracts_count = response['contracts'].length;
    for(var i = 0; i < contracts_count; i++) {
      var temp_contracts = response['contracts'][i];
      //  var temp_selected = (i == 0 ?" selected" : "");
      var temp_selected = (temp_contracts['supplypoint_code'] == now_supplypoint_code ? " selected" : "");
      stack_contracts += '<option value="' + temp_contracts['supplypoint_code'] + '" ' + temp_selected + '>' 
      + temp_contracts['plan'] +'</option>';
      // console.log(temp_contracts);
    }
    $('#supplypoint_code select').append(stack_contracts); // 子要素追加
    // $('#supplypoint_code select.materializecss_sam').formSelect(); // materializecss_sam 要素のみ 表示の再反映  


    // 初回のみ一番最初の要素を選ぶ、使用場所一覧取得イベントを実施
    usage_billing_refresh( $('*[name=supplypoint_code]').val() );

    // 対象年の年度表示
    $("#billing_date select").empty();
    var stack_years = "<option value='' disabled>対象年を選択してください</option>";
    for(var now_year = 2018; now_year <= newest_date.getFullYear(); now_year++) {

      if (use_date_y > 0) {
        var temp_selected = (now_year == use_date_y ? " selected" : "");
      } else if (now_date_m === 1) {
        var temp_selected = (now_year == now_date - 1 ? " selected" : "");
      } else {
        var temp_selected = (now_year == now_date ? " selected" : "");
      }
      stack_years += '<option value="' + now_year + '" ' + temp_selected + '>' + now_year +' 年</option>';
    }
    $('#billing_date select').append(stack_years); // 子要素追加
    $('#billing_date select.materializecss_sam').formSelect(); // materializecss_sam 要素のみ 表示の再反映  

    /* 利用年月を指定して CSV一括出力 プルダウン */ 
    // 対象年
    $("#original_billing_date select").empty();
    var stack_years2 = "<option value='' disabled>対象年を選択してください</option>";
    for(var now_year = 2018; now_year <= newest_date.getFullYear(); now_year++) {
      if (use_date_y > 0) {
        var temp_selected2 = (now_year == use_date_y ? " selected" : "");
      } else if (now_date_m === 1) {
        var temp_selected2 = (now_year == now_date - 1 ? " selected" : "");
      } else {
        var temp_selected2 = (now_year == now_date ? " selected" : "");
      }
      stack_years2 += '<option value="' + now_year + '" ' + temp_selected2 + '>' + now_year +' 年</option>';
    }
    $('#original_billing_date select').append(stack_years2); // 子要素追加
    $('#original_billing_date select.materializecss_sam').formSelect(); // materializecss_sam 要素のみ 表示の再反映  

    // 対象月
    $("#original_billing_month select").empty();
    var stack_month = "<option value='' disabled>対象月を選択してください</option>";
    for(var now_month = 1; now_month <= 12; now_month++) {
      if (use_date_m > 0) {
        var temp_selected3 = (now_month == use_date_m ? " selected" : "");
      } else if (now_date_m === 1) {
        var temp_selected3 = (now_month == 12 ? " selected" : "");
      } else {
        var temp_selected3 = (now_month == now_date_m -1 ? " selected" : "");
      }

      if (now_month > 9) {
        stack_month += '<option value="' + now_month + '" ' + temp_selected3 + '>' + now_month +' 月</option>';
      } else {
        stack_month += '<option value="' + '0' + now_month + '" ' + temp_selected3 + '>0' + now_month +' 月</option>';
      }
    }
    $('#original_billing_month select').append(stack_month); // 子要素追加
    $('#original_billing_month select.materializecss_sam').formSelect(); // materializecss_sam 要素のみ 表示の再反映  
    /* 利用年月を指定して CSV一括出力 プルダウン */

    // <select name="billing_date"   class="materializecss_sam">
    // <option value="" disabled >対象年を選択してください</option>
    // <option value="2017">2017年</option>
    // <option value="2018">2018年</option>
    // <option value="2019" selected>2019年</option>
    // </select>

  }).fail(function(error){
    console.log("error: usage_refresh failed.");
    console.log(error);
    M.toast({html: '<i class="material-icons left">error</i> サーバ間通信で不具合が発生しました', classes: 'red darken-3'});
  });


}

/**
 * 2 対象年プルダウン
 */
function usage_billing_refresh( supplypoint_code ) {
  //  使用場所 詳細取得
  //  対象年一覧取得  
  // 上記x点、 -> /confirm_usagedata/billing_pulldown に通信
  // トークンは_token で取得可能。

  // customer_codeはサーバ側(PHP)でsessionに保持している
  var post_data = {};
  post_data['_token'] = _token;
  post_data['supplypoint_code'] = supplypoint_code;

  console.log(post_data);
  $.ajax({
    url: application_url + "confirm_usagedata/billings_pulldown",
    type: 'post',
    dataType: 'json',
    data : post_data,
  }).done(function(response){
    // console.log(response);

    // contractの表示変更
    // $('#get_contract_name').val( response['contract']["contract_name"] );
    $('#get_contract_name').text( response['contract']["contract_name"] );
    // $('#get_plan').val( response['contract']["plan"] );
    $('#get_plan').text( response['contract']["address"] );

    if (response['contract']["status"] == 1 || response['contract']["status"] == 2 || response['contract']["status"] == 6 || response['contract']["status"] == 7 ) {
      $('#plan_status').removeClass();
      $('#plan_status').addClass('procedure_blue');
      $('#plan_status').text( '契約中' );
    } else if (response['contract']["status"] == 4 || response['contract']["status"] == 5) {
      $('#plan_status').removeClass();
      $('#plan_status').addClass('procedure_red');
      $('#plan_status').text( '解約完了' );
    } else if (response['contract']["status"] == 3) {
      $('#plan_status').removeClass();
      $('#plan_status').addClass('procedure_black');
      $('#plan_status').text( '申込キャンセル' );
    } else {
      $('#plan_status').removeClass();
      $('#plan_status').text( '' );
    }
    
    // 3 電力情報の取得 実行する
    usage_billing_getlist( $('*[name=billing_date]').val() );

  }).fail(function(error){
    console.log("error: usage_billing_refresh failed.");
    console.log(error);
    M.toast({html: '<i class="material-icons left">error</i> サーバ間通信で不具合が発生しました', classes: 'red darken-3'});
  });

}

// 3 電力情報の取得
// 対象年に従った電力情報一覧表示の取得
// グラフの反映
// 使用量（UsageT）から利用料（Billing）に変更
function usage_billing_getlist( billing_date ) {
  //  使用場所 詳細取得
  //  対象年一覧取得  
  // 上記x点、 -> /confirm_usagedata/billings_getlist に通信
  // トークンは_token で取得可能。

  // customer_codeはサーバ側(PHP)でsessionに保持している
  var post_data = {};
  post_data['_token'] = _token;
  post_data['supplypoint_code'] = $('*[name=supplypoint_code]').val();
  post_data['billing_date'] = billing_date;
  
  // console.log(post_data);
  $.ajax({
    url: application_url + "confirm_usagedata/billings_getlist",
    type: 'post',
    dataType: 'json',
    data : post_data,
  }).done(function(response){
    // console.log(response);

    // 一覧の図表化
    // response.sheet_data を代入
    read_results = response;
    // csv分析結果を一覧に表示する。
    loader1_refresh();

  }).fail(function(error){
    console.log("error:");
    console.log(error);
    M.toast({html: '<i class="material-icons left">error</i> サーバ間通信で不具合が発生しました', classes: 'red darken-3'});
  });

}

/**
 * 請求一覧 表示リフレッシュ
 * ※グラフの描画もここで行う。
 */
function loader1_refresh() {
  console.log("loader1_refresh");
  //console.log(read_results);

  // 対象年：yyyy
  var billing_date = read_results['billing_date'];

  // 利用料　描画
  var billing_val = [];
  var billing_val_max = 0; // 最大
  var temp_billinglist = []; // 利用料の月とindexの関係をここに取り出す

  // 判定準備
  billing_count = read_results['billings'].length;
  for (var i=0; i < billing_count; i++) {
    temp_billinglist.push( read_results['billings'][i].usage_date );
  }
  // console.log(temp_billinglist);
 
  // billings の 「対象年MM /MMは01-12月」を走査、利用月が存在したらその請求額を取得。無ければその年月は0
  for (var i=1; i < 13; i++) {
    // YYYYMMで年月の表現、一覧を出す。  
    var temp_date = billing_date * 100 + i;
    // console.log( "date : " + temp_date );
    // console.log( temp_billinglist.indexOf(temp_date)  );

    // 利用料グラフを正しく埋める
    if (temp_billinglist.indexOf(temp_date) >= 0) {
      var now_billing = read_results['billings'][ temp_billinglist.indexOf(temp_date) ].billing_amount;
      billing_val.push( now_billing );
      // 使用量最大値を更新
      billing_val_max = (now_billing > billing_val_max) ? now_billing : billing_val_max; // 最大
    } else {
      billing_val.push( 0 );
    }
  }

  // console.log(billing_val);
  // グラフサイズを変更する usage_t_specをbillingで置き換え
  // console.log( read_results['usage_t_spec'] );
  console.log("billing_val_max : " + billing_val_max);
  step_size = 10;
  if (read_results.usage_t_spec) {
    step_size = ( billing_val_max >3000 ) ? 1000 : (( billing_val_max >500 ) ? 500 : 100 );
    // step_size = read_results['usage_t_spec']['step_size'];
    // options.scales.yAxes.ticks.stepSize: 10
    myBarChart.options.scales.yAxes[0].ticks.stepSize = step_size;
    console.log( step_size );
  }

  // グラフ描画ここから
  console.log("グラフ描画開始");
  myBarChart.options.title.text = [ billing_date + '年 ご利用状況' ];

  myBarChart.data.datasets = [
    {
      label: '請求金額(円)',
      data: billing_val,
      backgroundColor: "rgba(219,39,91,0.5)"
    }       
  ];

  myBarChart.update({
    duration: 400,
    // easing: 'easeOutBounce'
    // easing: 'easeOutCubic'      
  });
  console.log("グラフ描画完了");
  // /グラフ描画ここまで

  // 代表サンプル：ID result1_list theadをクリア、取得したリストに入れ替える
  $('table#result1_list thead tr').remove();
  var billname_count = 0; // 内訳明細の段数  
  temp_str = '<tr>';
  temp_str += '<th class="right-align">請求年月</th>'; // 請求年月
  temp_str += '<th class="right-align">合計金額</th>'; // 合計金額
  temp_str += '<th class="right-align">利用期間</th>'; // 利用期間
  // for(let i in read_results.billings_itemize['name']) {
  //   var temp_linedata = read_results.billings_itemize['name'][i];
  //   // console.log(temp_linedata);
  //   temp_str += '<th class="right-align">'+ temp_linedata + '</th>';
  //   billname_count++;
  // }
  // temp_str += '<th class="right-align">消費税</th>'; // 消費税
  temp_str += '<th class="right-align">詳細表示</th>'; // 詳細表示
  temp_str += '</tr>';
  $('table#result1_list thead').append( temp_str );
  // console.log("order: " + billname_count);

  // 代表サンプル：ID result1_list のtbodyの内容をクリア、取得したリストに入れ替える
  var supplypoint_code = $('*[name=supplypoint_code]').val();
  $('table#result1_list tbody tr').remove();
  for(let i in read_results.billings) {
    var temp_linedata = read_results.billings[i];
    // console.log(temp_linedata);
    var temp_itemize_code = temp_linedata["itemize_code"];
    temp_str = '<tr>';
    temp_str+= '<td class="date">'+ String(temp_linedata['billing_date']).substr(0,4) + "年"
    + String(temp_linedata['billing_date']).substr(4,2)  +'月分</td>'; // 請求年月

    temp_str+= '<td class="en">'+ String(temp_linedata['billing_amount']).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,') +'円</td>'; // 合計金額
    // temp_str+= '<td class="right-align">'+ temp_linedata['tax'] +'</td>'; // 消費税
    var start_date = new Date(temp_linedata['start_date']);
    var end_date = new Date(temp_linedata['end_date']);
    start_date = (start_date.getMonth() + 1) + '月' + start_date.getDate() + '日';
    end_date = (end_date.getMonth() + 1) + '月' + end_date.getDate() + '日';
    temp_str+= '<td class="date">'+ start_date + "～"
    + end_date +'</td>'; // 利用年月

    temp_str+= '<td><button type="button" onclick="location.href=\'' + application_url;
    temp_str+= 'confirm_usagedata/detail?date=' + String(temp_linedata['usage_date']) + '&supplypoint_code=' + supplypoint_code;
    temp_str+= '\'">確認</button></td>'; // 詳細表示
    temp_str+= '</tr>';

    $('table#result1_list tbody').append( temp_str );
  }
  
  $("#result1").fadeIn(300);
}



/** コンストラクタ */
// $(function(){


//   // グラフ初期化
//   // グラフ描画ここから
//   console.log("グラフ初期化開始");
//   var ctx = document.getElementById("myBarChart");
//   ctx.height = 380;
  
//   myBarChart = new Chart(ctx, {
//     type: 'bar',
//     data: {
//       labels: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
//       datasets: [
//         {
//           label: '請求金額(円)',
//           data: [ 0,0,0,0,0,0,0,0,0,0,0,0 ],
//           backgroundColor: "rgba(219,39,91,0.5)"
//         }
//       ]
//     },
//     options: {
//       title: {
//         display: true,
//         text:  '----年 請求金額'
//       },
//       scales: {
//         yAxes: [{
//           ticks: {
//             suggestedMax: 100,
//             suggestedMin: 0,
//             stepSize: 10,
//             callback: function(value, index, values){
//               return  value +  '円'
//             }
//           }
//         }]
//       },
//       responsive: true,
//       maintainAspectRatio: false, // アスペクト比維持
//     }
//   });
//   console.log("グラフ初期化完了");

//   // 1 使用場所一覧取得 表示リフレッシュ 実行
//   usage_refresh();

//   // 2 使用場所一覧取得 変更時イベント　supplypoint_code：お使いの場所
//   $('*[name=supplypoint_code]').change(function(evt) {
//     console.log("change: supplypoint_code " + evt.target.value);
//     usage_billing_refresh(evt.target.value);
//   });

//   // 3  電力情報の取得 変更時イベント billing_date：対象年
//   $('*[name=billing_date]').change(function(evt) {
//     console.log("change: billing_date " + evt.target.value);
//     usage_billing_getlist(evt.target.value);
    
//   });


//   // 4  利用料内訳 ダウンロードボタン 押下時イベント
//   $('#btn_download_csv').click(function(evt) {
//     // 
//     console.log("click: btn_download_csv " + evt.target.value);
//     evt.preventDefault();
//     // usage_export_chart(evt.target.value);

//     // 4 CSV/XLS 一括出力  
//     // 使用場所、対象年に従ったcsv/xlsを出力。
//     var post_data = {};
//     post_data['_token'] = _token;
//     post_data['supplypoint_code'] = $('*[name=supplypoint_code]').val();
//     post_data['billing_date']     = $('*[name=billing_date]').val();

//     console.log(post_data);
//     $.ajax({
//       url: application_url + "confirm_usagedata/export_chart",
//       type: 'post',
//       dataType: 'json',
//       data : post_data,
//     }).done(function(response){
//       console.log(response);
//       read_results = response;

//       // 一時的なダウンロードリンク 生成
//       $("#file_dl").remove();
//       $("div#download_temp_area").append('<a id="file_dl" download="'+response["file_name"]+'" href="" download ></a><br>');
      
//       // csv分析結果をダウンロードする。
//       downloadCsv("file_dl", response["encoded_csv"]);

//     }).fail(function(error){
//       console.log("error:");
//       console.log(error);
//       M.toast({html: '<i class="material-icons left">error</i> サーバ間通信で不具合が発生しました', classes: 'red darken-3'});
//     });


//   });

//   // ToDo:5 電気利用料内訳.csv ダウンロードボタン 押下時イベント 
//   $('#btn_original_download_csv').click(function(evt) {
//     // 
//     console.log("click: btn_original_download_csv " + evt.target.value);
//     evt.preventDefault();
//     // usage_export_chart(evt.target.value);

//     // 5 CSV/XLS 一括出力  
//     // 使用場所、対象年月に従ったcsv/xlsを出力。
//     var post_data = {};
//     post_data['_token'] = _token;
//     post_data['supplypoint_code'] = $('*[name=supplypoint_code]').val();
//     /* NOTE : 請求年月だと重複することがあり得る為、利用年月に変更
//               ただし、nameやid属性の名称は変えていない */
//     post_data['billing_date']     = $('*[name=original_billing_date]').val();
//     post_data['billing_month']     = $('*[name=original_billing_month]').val();

//     console.log(post_data);
//     $.ajax({
//       url: application_url + "confirm_usagedata/export_chart_original",
//       type: 'post',
//       dataType: 'json',
//       data : post_data,
//     }).done(function(response){
//       console.log(response);
//       read_results = response;

//       // 一時的なダウンロードリンク 生成
//       $("#file_dl").remove();
//       $("div#download_temp_area").append('<a id="file_dl" download="'+response["file_name"]+'" href="" download ></a><br>');
      
//       // csv分析結果をダウンロードする。
//       downloadCsv("file_dl", response["encoded_csv"]);

//     }).fail(function(error){
//       console.log("error:");
//       console.log(error);
//       M.toast({html: '<i class="material-icons left">error</i> サーバ間通信で不具合が発生しました', classes: 'red darken-3'});
//     });


//   });


// });
