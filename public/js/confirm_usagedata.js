/** 利用料データ取込 */
var read_results = [];
var myBarChart = "";
/** 契約情報 */
var contracts = [];
/** 日付情報 */
var newest_date = new Date(); 
var now_date = newest_date.getFullYear();
var now_date_m = newest_date.getMonth() + 1;
var use_date_y = 0;
var use_date_m = 0;

/**
 * 1 使用場所一覧取得 表示リフレッシュ
 */
function usage_refresh() {
  var post_data = {};
  post_data['_token'] = _token;

  $.ajax({
    url: application_url + "confirm_usagedata/pulldown",
    type: 'post',
    dataType: 'json',
    data : post_data,
  }).done(function(response){

    contracts = response['contracts'];
    // getパラメータに値がある場合、プルダウンを指定のものにして初期化する
    var now_supplypoint_code = getUrlParam("supplypoint_code");

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

    // service の表示変更
    var service_flags = {
      electric: false, 
      gas: false, 
      wifi: false, 
      option: false
    };
    $.each(contracts, function(key, contract){
      if (contract['pps_type']) {
        if (contract['pps_type'] == 1 || contract['pps_type'] == 5) {
          service_flags['electric'] = true;
          contracts[key]['service'] = 'electric';
        } else if (contract['pps_type'] == 2 || contract['pps_type'] == 3 || contract['pps_type'] == 4) {
          service_flags['gas'] = true;
          contracts[key]['service'] = 'gas';
        } else if (contract['supplypoint_code'].substring(0,2) == 'GP') {
          service_flags['wifi'] = true;
          contracts[key]['service'] = 'wifi';
        }
      } 
      else {
        if (contract['supplypoint_code'].length == 22) {
          service_flags['electric'] = true;
          contracts[key]['service'] = 'electric';
        } else if (contract['supplypoint_code'].substring(0,2) == 'GP') {
          service_flags['wifi'] = true;
          contracts[key]['service'] = 'wifi';
        } else {
          service_flags['gas'] = true;
          contracts[key]['service'] = 'gas';
        }
      }
    });


    $("#service select").empty();
    var stack_service = "<option value='' disabled>---------</option>";
    if (service_flags['electric']) {
      stack_service += "<option value='electric'>電気</option>";
    }
    if (service_flags['gas']) {
      stack_service += "<option value='gas'>ガス</option>";
    }
    if (service_flags['wifi']) {
      stack_service += "<option value='wifi'>WiMAX</option>";
    }
    if (service_flags['option']) {
      stack_service += "<option value='option'>オプション</option>";
    }
    $('#service select').append(stack_service); // 子要素追加


    if (now_supplypoint_code) { // URLで供給地点番号が指定されているとき
      address_refresh(service);
      $.each(contracts, function(key, contract){
        if (now_supplypoint_code == contract['supplypoint_code']) {
          service_selected(contract);
          address_refresh(contract['service']);
          address_selected(contract['address'])
          plan_refresh(contract['service'], contract['address']);
          plan_selected(now_supplypoint_code);
          return false;
        }
      })
    } else { //デフォルト表示
      var service = $('*[name=service]').val();

      address_refresh(service);
      var address = $('*[name=address]').val();
      plan_refresh(service, address);
    }

    if (service == 'wifi') {
      $('.graph-area').hide();
    } else {
      $('.graph-area').show();
    }

    // 初回のみ一番最初の要素を選ぶ、使用場所一覧取得イベントを実施
    usage_billing_refresh( $('*[name=supplypoint_code]').val() );

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
 * 年プルダウンの更新
 * @param supplypoint_code 供給地点特定番号 
 */
function year_list_refresh(supplypoint_code) {
  var billing_range = '';
  $.each(contracts, function(key, contract){
    if (supplypoint_code == contract['supplypoint_code']) {
      billing_range = contract['billing_range'];
    }
  })
  if (billing_range['first_billing_date']) {
    var first_year = String(billing_range['first_billing_date']).substring(0, 4);
  } else {
    var first_year = 2018;
  }
  if (billing_range['latest_billing_date']) {
    var last_year = String(billing_range['latest_billing_date']).substring(0, 4);
  } else {
    var last_year = now_date;
  }

  // 対象年の年度表示
  $("#billing_date select").empty();
  var stack_years = "<option value='' disabled>対象年を選択してください</option>";
  if (billing_range['first_billing_date'] || billing_range['latest_billing_date']) {
    for(var now_year = last_year; now_year >= first_year; now_year--) {
      if (use_date_y > 0) {
        var temp_selected = (now_year == use_date_y ? " selected" : "");
      } else if (now_date_m === 1) {
        var temp_selected = (now_year == now_date - 1 ? " selected" : "");
      } else {
        var temp_selected = (now_year == now_date ? " selected" : "");
      }
      stack_years += '<option value="' + now_year + '" ' + temp_selected + '>' + now_year +' 年</option>';
    }  
  }
  $('#billing_date select').append(stack_years); // 子要素追加
  $('#billing_date select.materializecss_sam').formSelect(); // materializecss_sam 要素のみ 表示の再反映  
}

/**
 * 指定された供給地点番号でサービスをセレクト
 * @param supplypoint_code 供給地点番号
 */
function service_selected( contract ) {
  if (contract['pps_type']) {
    if (contract['pps_type'] == 1 || contract['pps_type'] == 5) {
      $("#service select option[value='electric']").prop('selected', true);
    } else if (contract['pps_type'] == 2 || contract['pps_type'] == 3 || contract['pps_type'] == 4) {
      $("#service select option[value='gas']").prop('selected', true);
    } else if (contract['supplypoint_code'].substring(0,2) == 'GP') {
      $("#service select option[value='wifi']").prop('selected', true);
    }
  } 
  // else {
  //   $("#service select option[value='']").prop('selected', true);
  // }
  else {
    if (contract['supplypoint_code'].length == 22) {
      $("#service select option[value='electric']").prop('selected', true);
    } else if (contract['supplypoint_code'].substring(0,2) == 'GP') {
      $("#service select option[value='wifi']").prop('selected', true);
    } else {
      $("#service select option[value='gas']").prop('selected', true);
    }
  }
}

/**
 * 選択されたサービス、住所でプラン一覧を更新
 * 
 * @param service 選択されたサービス
 * @param address 選択された住所
 */
function plan_refresh( service, address ) {
  $("#supplypoint_code select").empty();
  var stack_plan = "<option value='' disabled>---------</option>";
  $.each(contracts, function(key, contract){
    if (contract['address'] == address && contract['service'] == service) {
      stack_plan += "<option value='" + contract['supplypoint_code'] + "'>" + contract['plan'] + "</option>";
    }
  })
  $('#supplypoint_code select').append(stack_plan);
}

/**
 * 指定された供給地点番号でプランをセレクト
 * @param supplypoint_code 供給地点番号
 */
function plan_selected( supplypoint_code ) {
  $("#supplypoint_code select option[value='"+ supplypoint_code +"']").prop('selected', true);
}

/**
 * 選択されたサービスで住所一覧を更新
 * 
 * @param service 選択されたサービス 
 */
function address_refresh( service ) {
  $("#address select").empty();
  var stack_address = "<option value='' disabled>---------</option>";
  var contract_address= [];
  $.each(contracts, function(key, contract){
    if (contract['service'] == service) {
      contract_address.push(contract['address']);
    }
  });
  // 重複削除
  var set = new Set(contract_address);
  var address_list = Array.from(set);
  $.each(address_list, function(key, address){
    stack_address += "<option value='" + address + "'>" + address + "</option>";
  });
  
  $('#address select').append(stack_address);
}

/**
 * 指定された住所をセレクト
 * @param address 住所
 */
 function address_selected( address ) {
  $("#address select option[value='"+ address +"']").prop('selected', true);
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
    $('#get_contract_name').text( response['contract']["contract_name"] );

    // 年プルダウンリストの更新
    year_list_refresh($('*[name=supplypoint_code]').val());

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
$(function(){


  // グラフ初期化
  // グラフ描画ここから
  console.log("グラフ初期化開始");
  var ctx = document.getElementById("myBarChart");
  ctx.height = 380;
  
  myBarChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
      datasets: [
        {
          label: '請求金額(円)',
          data: [ 0,0,0,0,0,0,0,0,0,0,0,0 ],
          backgroundColor: "rgba(219,39,91,0.5)"
        }
      ]
    },
    options: {
      title: {
        display: true,
        text:  '----年 請求金額'
      },
      scales: {
        yAxes: [{
          ticks: {
            suggestedMax: 100,
            suggestedMin: 0,
            stepSize: 10,
            callback: function(value, index, values){
              return  value +  '円'
            }
          }
        }]
      },
      responsive: true,
      maintainAspectRatio: false, // アスペクト比維持
    }
  });
  console.log("グラフ初期化完了");

  // 1 使用場所一覧取得 表示リフレッシュ 実行
  usage_refresh();

  // サービス変更時
  $('*[name=service]').on('change', function() {
    var service = $('*[name=service]').val();
    address_refresh(service);
    var address = $('*[name=address]').val();
    plan_refresh(service, address);
    if (service == 'wifi') {
      $('.graph-area').hide();
    } else {
      $('.graph-area').show();
    }
  });

  // 住所変更時
  $('*[name=address]').on('change', function() {
    var service = $('*[name=service]').val();
    var address = $('*[name=address]').val();
    plan_refresh(service, address);
  });

  // 表示ボタンクリック
  $('#graph_display').on('click', function() {
    var supplypoint_code = $('*[name=supplypoint_code]').val();
    usage_billing_refresh(supplypoint_code);
  });

  // 3  電力情報の取得 変更時イベント billing_date：対象年
  $('*[name=billing_date]').change(function(evt) {
    console.log("change: billing_date " + evt.target.value);
    usage_billing_getlist(evt.target.value);
    
  });


  // 4  利用料内訳 ダウンロードボタン 押下時イベント
  $('#btn_download_csv').click(function(evt) {
    // 
    console.log("click: btn_download_csv " + evt.target.value);
    evt.preventDefault();
    // usage_export_chart(evt.target.value);

    // 4 CSV/XLS 一括出力  
    // 使用場所、対象年に従ったcsv/xlsを出力。
    var post_data = {};
    post_data['_token'] = _token;
    post_data['supplypoint_code'] = $('*[name=supplypoint_code]').val();
    post_data['billing_date']     = $('*[name=billing_date]').val();

    console.log(post_data);
    $.ajax({
      url: application_url + "confirm_usagedata/export_chart",
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


  });

  // ToDo:5 電気利用料内訳.csv ダウンロードボタン 押下時イベント 
  $('#btn_original_download_csv').click(function(evt) {
    // 
    console.log("click: btn_original_download_csv " + evt.target.value);
    evt.preventDefault();
    // usage_export_chart(evt.target.value);

    // 5 CSV/XLS 一括出力  
    // 使用場所、対象年月に従ったcsv/xlsを出力。
    var post_data = {};
    post_data['_token'] = _token;
    post_data['supplypoint_code'] = $('*[name=supplypoint_code]').val();
    /* NOTE : 請求年月だと重複することがあり得る為、利用年月に変更
              ただし、nameやid属性の名称は変えていない */
    post_data['billing_date']     = $('*[name=original_billing_date]').val();
    post_data['billing_month']     = $('*[name=original_billing_month]').val();

    console.log(post_data);
    $.ajax({
      url: application_url + "confirm_usagedata/export_chart_original",
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


  });


});
