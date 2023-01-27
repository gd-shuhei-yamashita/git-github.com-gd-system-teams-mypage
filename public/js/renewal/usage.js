/**
 * 使用量・請求金額 js
 */
$(function(){
  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   * Controller
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const Controller = (function() {
    return {
      /**
       * initialize
       */
      init : function() {
        Graph.init();
        SelectArea.init();
        BillingList.init();
        Csv.init();
      },
    }
  })();


  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   * セレクトエリア
   * - ご利用サービスを選択
   * - 住所を選択
   * - ご利用プランを選択
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const SelectArea = (function() {
    /* define */
    const AJAX_URL = application_url + 'confirm_usagedata/pulldown';
    const SELECT_SERVICE = '.js-select-service';
    const SELECT_ADDRESS = '.js-select-address';
    const SELECT_PLAN    = '.js-select-plan';
    const DISPLAY_BUTTON = '.js-display-button';
    const PLAN_YEAR      = '.js-plan-year';
    const PLAN_USER_NAME = '.js-paln-user';
    const PLAN_STATUS    = '.js-paln-status';

    let _this;
    let _contracts;
    let _showCount = 0;
    let _targetYear = null;
    let _targetMonth = null;
    let _yearList = [];

    /* method */
    return {
      /**
       * initialize
       */
      init : function() {
        _this = this;
        this.show();
      },

      /**
       * 請求データを非同期通信で取得して表示する
       */
      show: function() {
        _showCount++;
        const options = {
          url: AJAX_URL,
          type: 'post',
          data : {}
        };
        GdMypage.ajax.run(options, function(response) {
          $('#pulldown').html(response.html);
          _contracts = response.contract_list;
          _this.setEvent();
          if (_showCount === 1) {
            // 初回のみのイベント
            _this.setValueFromParameter();
            Csv.setPulldown(response.range);
            Csv.getCsvData();
          } else {
            _this.fireEvent(SELECT_SERVICE);
          }
          _this.setEventDisplay();
          _this.fireEventDisplay();
        });
      },

      /**
       * ご利用サービス、住所を選択したときのイベント登録
       */
      setEvent: function() {
        $(SELECT_SERVICE).on('change', function() {
          _this.fireEvent(SELECT_SERVICE);
        });
        $(SELECT_ADDRESS).on('change', function() {
          _this.fireEvent(SELECT_ADDRESS);
        });
      },

      /**
       * 選択イベント発火
       * @param {string} eventName イベント名
       */
      fireEvent: function(eventName) {
        const serviceType = $(SELECT_SERVICE).val();
        let selected;
        switch (eventName) {
          // ご利用サービスを選択
          case SELECT_SERVICE:
            const addressList = [];
            selected = '';
            $.each(_contracts, function(code, contract) {
              if (serviceType === contract.type) {
                addressList.push(contract.address);
              }
            });
            $(SELECT_ADDRESS + ' option').each(function() {
              const disabled = !addressList.includes($(this).val());
              $(this).prop('disabled', disabled);
              if (selected === '' && !disabled) selected = $(this).val();
            });
            $(SELECT_ADDRESS).val([selected]);
          // 住所を選択
          case SELECT_ADDRESS:
            const address = $(SELECT_ADDRESS).val();
            const planList = [];
            selected = '';
            $.each(_contracts, function(code, contract) {
              if (serviceType === contract.type && address === contract.address) {
                planList.push(contract.plan_name);
              }
            });
            $(SELECT_PLAN + ' option').each(function() {
              const disabled = !planList.includes($(this).text());
              $(this).prop('disabled', disabled);
              if (selected === '' && !disabled) selected = $(this).val();
            });
            $(SELECT_PLAN).val([selected]);
            break;
          default:
            break;
        }
      },

      /**
       * 表示するボタンを選択したときのイベント登録
       */
      setEventDisplay: function() {
        $(DISPLAY_BUTTON).on('click', function() {
          _this.setYear(null);
          _this.fireEventDisplay();
        });
        $(PLAN_YEAR).on('change', function() {
          _this.setYear($(this).val());
          _this.fireEventDisplay();
        });
      },

      /**
       * 表示するボタンを選択したときのイベント登録
       */
      fireEventDisplay: function() {
        const supplypointCode = _this.getSupplypointCode();
        _this.setBilldingStatus(supplypointCode);
        Graph.setCalendar(_this.getYear(), _yearList);
        Graph.create(_this.getYear(), _this.getMonth(), supplypointCode);
      },

      /**
       * ご利用期間、利用者名、ステータスをセットする
       * @param {string} supplypointCode 供給地点特定番号
       */
      setBilldingStatus: function(supplypointCode) {
        const selectedContract = _contracts[supplypointCode];
        const usePeriod = selectedContract.use_period;
        _yearList = [];
        $(PLAN_USER_NAME).text(selectedContract.user_name);
        $(PLAN_STATUS).text(selectedContract.status_name).addClass('status-' + selectedContract.status_code);
        let periodSelectOptions = '<option value="" disabled="">対象年を選択してください</option>';
        if (usePeriod.first_billing_date && usePeriod.latest_billing_date) {
          const minYear = GdMypage.utility.getYearFromYYYYMM(usePeriod.first_billing_date);
          const maxYear = GdMypage.utility.getYearFromYYYYMM(usePeriod.latest_billing_date);
          if (_this.getYear() === null && maxYear) {
            _this.setYear(maxYear);
            _this.setMonth((new Date()).getMonth() + 1);
          }
          for (let year = minYear; year <= maxYear; year++) {
            periodSelectOptions += '<option value="' + year + '"'+ ( year === _this.getYear() ? ' selected' : '') + '>' + year + ' 年' + '</option>';
            _yearList.push(year);
          }
        } else {
          periodSelectOptions += '<option value="">データがありません</option>';
        }
        $(PLAN_YEAR).empty().append(periodSelectOptions);
      },


      /**
       * GETパラメータから値をセットする
       */
      setValueFromParameter: function() {
        const params = (new URL(document.location)).searchParams;
        const supplypointCode = params.get('supplypoint_code');
        const date = params.get('date');
        if (date && date.length === 6) {
          _this.setYear(GdMypage.utility.getYearFromYYYYMM((date)));
          _this.setMonth(GdMypage.utility.getMonthFromYYYYMM((date)));
        }
        if (!date || !supplypointCode) return _this.fireEvent(SELECT_SERVICE);
        let type = '';
        let address = '';
        $.each(_contracts, function(code, contract) {
            if (code === supplypointCode) {
              type = contract.type;
              address = contract.address;
            }
        });
        if (type) {
          $(SELECT_SERVICE).val([type]);
          _this.fireEvent(SELECT_SERVICE);
          $(SELECT_ADDRESS).val([address]);
          _this.fireEvent(SELECT_ADDRESS);
          $(SELECT_PLAN).val([supplypointCode]);
        }
        Csv.setPulldownMonth(_this.getMonth());
      },

      /**
       * 選択中のご利用期間（年）を返す
       * @return {number|null}
       */
      getYear: function() {
        return _targetYear || null;
      },

      /**
       * 利用期間（年）を選択する
       * @param {number|null} year
       */
      setYear: function(year) {
        $(PLAN_YEAR).val([year]);
        _targetYear = year === null ? null : parseInt(year);
      },

      /**
       * @return {number|null}
       */
      getMonth: function() {
        return _targetMonth || null;
      },

      /**
       * 
       * @param {number} month
       */
      setMonth: function(month) {
        _targetMonth = month;
      },

      /**
       * 選択中の供給地点特定番号を返す
       */
      getSupplypointCode: function() {
        return $(SELECT_PLAN).val() || false;
      },

      /**
       * 選択中のサービスを返す
       */
      getService: function() {
        return $(SELECT_SERVICE).val() || false;
      },
    }

  })();


  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   * グラフ描画
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const Graph = (function() {
    /* define */
    const CTX = document.getElementById('myBarChart');
    const AJAX_URL = application_url + 'confirm_usagedata/billing';
    const DEFAULT_DATA = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    const MONTH_LABRLS = ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'];
    const CHART_LABELS = {
      billing: '請求金額',
      usage: '使用量'
    };
    const CHART_UNIT = {
      billing: '円',
      usage: 'kWh'
    };
    const CHART_COLORS = {
      billing: 'rgba(219,39,91,0.5)',
      usage: 'rgba(219,39,91,0.5)',
      clicked: 'rgba(219,39,91, 0.8)',
    };
    const GRAPH_AREA = '.graph-area';
    const DATE    = '.js-chart-date';
    const BILLING = '.js-chart-billing';
    const USAGE   = '.js-chart-usage';
    const TYPE_CHANGE = '.js-chart-switch';
    const CALENDAR_YEAR = '.js-calendar-year';
    const CALENDAR_PREV = '.js-calendar-prev';
    const CALENDAR_NEXT = '.js-calendar-next';

    let _this;
    let _myBarChart;
    let _chartYear = null;
    let _chartMonth = null;
    let _chartYearList = null;
    let _chartType = 'billing'; // billing or usage
    let _chartData = {
      billing: DEFAULT_DATA,
      usage: DEFAULT_DATA
    };

    /* method */
    return {
      /**
       * initialize
       */
      init : function() {
        _this = this;
        this.initShow();
        this.setEvent();
      },

      /**
       * イベント設定
       */
      setEvent : function() {
        // 請求と使用量の切り替え
        $(TYPE_CHANGE).on('click', function() {
          $(TYPE_CHANGE).removeClass('active');
          $(this).addClass('active')
          _chartType = $(this).data('type');
          _this.update();
        });
        // グラフをクリックイベント：クリックされた月の情報をsummayに反映し色を濃くする
        CTX.addEventListener('click', e => {
          const elements = _myBarChart.getElementAtEvent(e);
          if (elements.length) {
            const index = elements[0]._index;
            _chartMonth = (index + 1);
            _this.setSummary();
            _this.getChartColors(),
            _myBarChart.data.datasets = [
              {
                label: CHART_LABELS[_chartType] + '(' + CHART_UNIT[_chartType] + ')',
                data: _chartData[_chartType],
                backgroundColor: _this.getChartColors(),
              }
            ];
            // アニメーションなしでアップデート
            _myBarChart.update({
              duration: 0,
            });
            SelectArea.setMonth(_chartMonth);
          }
        });
        // カレンダーイベント登録
        $(CALENDAR_PREV + ',' + CALENDAR_NEXT).on('click', function() {
          const year = $(this).data('year');
          SelectArea.setYear(year);
          SelectArea.fireEventDisplay();
        });
      },

      /**
       * グラフ初期表示
       */
      initShow : function() {
        _myBarChart = new Chart(CTX, {
          type: 'bar',
          data: {
            labels: MONTH_LABRLS,
            datasets: [
              {
                label: CHART_LABELS[_chartType] + '(' + CHART_UNIT[_chartType] + ')',
                data: DEFAULT_DATA,
                backgroundColor: [CHART_COLORS[_chartType]],
              }
            ]
          },
          options: {
            /* グラフタイトル */
            title: {
              display: true,
              fontColor: 'rgba(255, 255, 255, 0)',
              padding: 2
            },
            /* 凡例 */
            legend: {
              display: false,
            },
            /* 軸 */
            scales: {
              yAxes: [ _this.getYAxes(DEFAULT_DATA) ]
            },
            responsive: true,
            /* アスペクト比維持 */
            maintainAspectRatio: false,
            /* ツールチップ */
            tooltips: {
              callbacks: {
                title: (function (tooltipItem, data){
                  return _chartYear + '年' + tooltipItem[0].xLabel;
                }).bind(this),
                label: (function (tooltipItem, data){
                  const label = CHART_LABELS[_chartType];
                  const amount = GdMypage.utility.number_format(tooltipItem.yLabel);
                  const unit = CHART_UNIT[_chartType];
                  return  label + ' : ' + amount + unit;
                }).bind(this),
              }
            }
          }
        });
      },

      /**
       * グラフのデータを取得して描写する
       * @param {number} year 対象年
       * @param {number} month 対象月月
       * @param {string} supplypointCode 供給地点特定番号
       */
      create : function(year, month, supplypointCode) {
        const loadingClass = 'loading';
        _this.setChartData({});
        _chartYear = null;
        _chartMonth = null;
        if (!year || !supplypointCode || !_chartYearList.includes(year)) {
          _this.update();
          BillingList.reset();
          return;
        }
        $(GRAPH_AREA).addClass(loadingClass);
        _this.setSummary(loading = true);
        GdMypage.ajax.run({
          url: AJAX_URL,
          type: 'post',
          data: {
            'year': year,
            'supplypoint_code': supplypointCode
          }
        }, function(response) {
          $(GRAPH_AREA).removeClass(loadingClass);
          BillingList.write(response.html);
          _chartYear = year;
          _chartMonth = month;
          _this.setChartData(response.billing_list);
          _this.update();
        }, function(error) {
          $(GRAPH_AREA).removeClass(loadingClass);
          _this.setSummary();
          BillingList.reset();
        });
      },

      /**
       * chart用のデータをセットする
       * @param {object} billingList
       */
      setChartData : function(billingList) {
        let isSet, dateKey;
        _chartData.billing = [];
        _chartData.usage = [];
        for (let index = 1; index <= 12; index++) {
          dateKey = _chartYear + (index < 10 ? '0' + index : '' + index);
          isSet = false;
          $.each(billingList, function(key, billing) {
            if (dateKey.toString() === billing.usage_date.toString()) {
              const billing_amount = GdMypage.utility.number_unformat(billing.billing_amount);
              const usage_amount = parseInt(billing.usage);
              _chartData.billing.push(billing_amount);
              _chartData.usage.push(usage_amount);
              isSet = true;
              return true;
            }
          });
          if (!isSet) {
            _chartData.billing.push(null);
            _chartData.usage.push(null);
          }
        }
      },

      /**
       * グラフ描画（更新）
       */
      update : function() {
        _this.controlTab();
        const data = _chartData[_chartType];
        _myBarChart.options.scales.yAxes = [ _this.getYAxes(data) ];
        _myBarChart.data.datasets = [
          {
            label: CHART_LABELS[_chartType] + '(' + CHART_UNIT[_chartType] + ')',
            data: data,
            backgroundColor: _this.getChartColors(),
          }
        ];
        _myBarChart.update({
          duration: 400,
        });
        _this.setSummary();
      },

      /**
       * Y軸のメモリを計算
       * @return {array}
       */
      getYAxes: function(data){
        const steps = [
          { threshold: -1, step: 5, max: 50 },
          { threshold: 42, step: 10, max: 100 },
          { threshold: 90, step: 15, max: 150 },
          { threshold: 140, step: 20, max: 200 },
          { threshold: 180, step: 30, max: 300 },
          { threshold: 280, step: 50, max: 500 },
          { threshold: 450, step: 100, max: 1000 },
          { threshold: 800, step: 300, max: 3000 },
          { threshold: 2800, step: 500, max: 5000 },
          { threshold: 4000, step: 1000, max: 10000 },
          { threshold: 8000, step: 2000, max: 20000 },
          { threshold: 18000, step: 5000, max: 50000 },
          { threshold: 40000, step: 10000, max: 100000 },
          { threshold: 80000, step: 50000, max: 500000 },
          { threshold: 450000, step: 100000, max: 1000000 },
        ];
        const maxAmount = Math.max(...data);
        let max = 0;
        let step = 0;
        for (let index = 0; index < steps.length; index++) {
          const item = steps[index];
          if (maxAmount > item.threshold) {
            max = item.max;
            step = item.step;
          }
        }
        return {
          ticks: {
            suggestedMax: max,
            suggestedMin: 0,
            stepSize: step,
            callback: function(value, index, values){
              return  value +  CHART_UNIT[_chartType];
            }
          }
        };
      },

      /**
       * グラフの色オプションを作成
       * @return {array}
       */
      getChartColors: function() {
        CHART_COLORS[_chartType]
        const backgroundColors = [];
        const data = _chartData[_chartType];
        for (let i = 1; i <= data.length; i++) {
          backgroundColors.push(i === _chartMonth ? CHART_COLORS.clicked : CHART_COLORS[_chartType]);
        }
        return backgroundColors;
      },

      /**
       * カレンダー情報をセット
       * @param {array} data
       */
      setCalendar: function(year, yearList) {
        const disabledClass = 'disabled';
        _chartYearList = yearList;
        $(CALENDAR_YEAR).text(year !== null ? year : '----');
        if (year !== null && _chartYearList.includes( (year - 1) )) {
          $(CALENDAR_PREV).removeClass(disabledClass).data('year', (year - 1));
        } else {
          $(CALENDAR_PREV).addClass(disabledClass).data('year', 0);
        }
        if (year !== null && _chartYearList.includes( (year + 1) )) {
          $(CALENDAR_NEXT).removeClass(disabledClass).data('year', (year + 1));
        } else {
          $(CALENDAR_NEXT).addClass(disabledClass).data('year', 0);
        }
      },

      /**
       * サマリー情報を更新
       * @param {boolean|void} loading ローディングフラグ
       */
      setSummary: function(loading) {
        const defaultBilling = '-----';
        const defaultUsage = '-----';
        const usageMonth = _chartMonth - 1;
        let billing, usage, date;
        if (loading !== void 0) {
          billing = defaultBilling;
          usage = defaultUsage;
          date = '読み込み中...';
        } else {
          try {
            billing = GdMypage.utility.number_format(_chartData.billing[usageMonth]);
            usage = GdMypage.utility.number_format(_chartData.usage[usageMonth]);
            date = (_chartYear ? _chartYear  : '----') + '年';
            date += (_chartYear && _chartMonth ? _chartMonth : '--' ) + '月';
          } catch (error) {
            billing = defaultBilling;
            usage = defaultUsage;
            date = '----年--月';
          }
        }
        if (['null', 'NaN', 'undefined'].includes(billing)) billing = defaultBilling;
        if (['null', 'NaN', 'undefined'].includes(usage)) usage = defaultUsage;
        $(DATE).text(date);
        $(BILLING).text(billing);
        $(USAGE).text(usage);
      },

      /**
       * 請求額 / 使用量の切り替えの表示コントロール
       */
      controlTab: function() {
        const $tab = $('.graph-area-switch');
        const service = SelectArea.getService();
        if (['electric'/*, 'gas'*/].includes(service)) {
          $tab.show();
        } else {
          _chartType = 'billing';
          $tab.hide();
        }
      }
    }
  })();


  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   * 請求金額一覧
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const BillingList = (function() {
    /* define */
    const $CONTENTS = $('#billing-list');
    const CSV_BUTTON = '#btn_download_csv';
    const AJAX_URL = application_url + 'confirm_usagedata/export_chart'

    let _this;

    /* method */
    return {
      /**
       * initialize
       */
      init : function() {
        _this = this;
        this.setEvent();
      },

      /**
       * ダウンロードボタン 押下時イベント
       */
      setEvent : function() {
        $(CSV_BUTTON).on('click', function() {
          _this.csvDownload();
        });
      },

      /**
       * 請求金額一覧テーブルを更新する
       * @param {string} html
       */
      write : function(html) {
        $CONTENTS.html(html);
        _this.abledButton();
      },

      /**
       * 請求金額一覧テーブルをリセットする
       */
      reset: function() {
        $CONTENTS.find('tbody').html('');
        _this.diabledButton();
      },

      /**
       * 請求一覧を一括出力(CSV)
       */
      csvDownload : function() {
        GdMypage.ajax.run({
          url: AJAX_URL,
          type: 'post',
          data: {
            'year': SelectArea.getYear(),
            'supplypoint_code': SelectArea.getSupplypointCode()
          }
        }, function(response) {
          GdMypage.csv.download(response.file_name, response.csv_data_encode);
        });
      },

      /**
       * ボタンを押せるようにする
       */
      abledButton: function() {
        $(CSV_BUTTON).prop('disabled', false);
      },

      /**
       * ボタンを押せないようにする
       */
      diabledButton: function() {
        $(CSV_BUTTON).prop('disabled', true);
      }
    }
  })();


  /* +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
   * 利用期間を指定して一括出力
   * いらない
   * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++ */
  const Csv = (function() {
    /* define */
    const YEAR = '.js-original_billing_year';
    const MONTH = '.js-original_billing_month';
    const CSV_BUTTON = '.js-original_csv_download';
    const AJAX_URL = application_url + 'confirm_usagedata/export_chart_original';

    let _this;
    let _csvData;
    let _fileName;
    let _cache = {};

    /* method */
    return {
      /**
       * initialize
       */
      init : function() {
        _this = this;
        this.setEvent();
      },


      /**
       * 期間 選択時イベント
       * ダウンロードボタン 押下時イベント
       */
      setEvent : function() {
        $(YEAR).on('change', function() {
          _this.getCsvData();
        });
        $(MONTH).on('change', function() {
          _this.getCsvData();
        });
        $(CSV_BUTTON).on('click', function() {
          _this.csvDownload();
        });
      },

      /**
       * 請求一覧を一括出力(CSV)
       */
      getCsvData : function() {
        const year = $(YEAR).val();
        const month = $(MONTH).val();
        // if (_cache.hasOwnProperty(year + month)) {
        //   _fileName = _cache[year + month].file_name;
        //   _csvData = _cache[year + month].csv_data_encode;
        //   return;
        // }
        _this.diabledButton();
        GdMypage.ajax.run({
          url: AJAX_URL,
          type: 'post',
          data: {
            year: year,
            month: month
          }
        }, function(response) {
          _this.abledButton();
          _fileName = response.file_name;
          _csvData = response.csv_data_encode;
          _cache[year + month] = response;
        }, function(error) {
          _this.diabledButton();
          // GdMypage.toast.error(typeof error === 'string' ? error : 'エラーが発生しました');
        });
      },

      /**
       * csvダウンロード
       */
      csvDownload : function() {
        GdMypage.csv.download(_fileName, _csvData);
      },

      /**
       * csvダウンロードボタンを活性化する
       */
      abledButton: function() {
        $(CSV_BUTTON).prop('disabled', false);
      },

      /**
       * csvダウンロードボタンを非活性化する
       */
      diabledButton: function() {
        $(CSV_BUTTON).prop('disabled', true);
      },

      /**
       * 年のプルダウンを作成する
       */
      setPulldown: function(range) {
        const startYear = GdMypage.utility.getYearFromYYYYMM(range.first_billing_date);
        const endYear = GdMypage.utility.getYearFromYYYYMM(range.latest_billing_date);
        let html = '<option value="">年</option>';
        for (let year = startYear; year <= endYear; year++) {
          html += '<option value="' + year + '"'+ ( year === endYear ? ' selected' : '') + '>' + year + '年</option>';
        }
        $(YEAR).empty().append(html);
      },

      /**
       * 月の値をセットする
       */
      setPulldownMonth: function(month) {
        const val = GdMypage.utility.getZeroPad(month, 2);
        $(MONTH).val([val]);
      },

    }
  })();

  Controller.init();
});
