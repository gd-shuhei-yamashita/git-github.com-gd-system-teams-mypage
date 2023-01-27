$(function(){
  /**
   * |----------------------------------------------------------------------
   * |
   * | 初期処理
   * |
   * |----------------------------------------------------------------------
   */
  
  $(document).ready(function(){
    // materialize Sidenav有効
    $('.sidenav').sidenav();
    
    // materialize Collapsible有効
    $('.collapsible').collapsible();

    // materialize Tooltip有効
    $('.tooltipped').tooltip();

    // materialize DatePicker有効
    // $('.datepicker').datepicker();
    
    // materialize form select表示有効
    $('main select').formSelect(); // main 領域のみ。laravel debugbarを除外する

    // materialize タブ表示有効
    $('.tabs').tabs();
//    $('.tabs').tabs({
//      swipeable : true,
//      responsiveThreshold : 1920
//    });

    // materialize dropdown list 表示有効
    $('.dropdown-trigger').dropdown();

    // 複数選択有効
    $('.select2').select2();

    // materialize Modals 表示有効
    $('.modal').modal();

    // materialize DatePicker有効
    // デートピッカー
    $('.datepicker').datepicker({
      selectMonths: true,
      autoClose: true,
      format: 'yyyy/mm/dd',
      // format: 'd mmmm, yyyy',
      // i18n:{
      //   months:["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
      //   monthsShort:["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"],
      //   weekdays:["月曜","火曜","水曜","木曜","金曜","土曜","日曜"],
      //   weekdaysShort:["月","火","水","木","金","土","日"],
      //   weekdaysAbbrev:["月","火","水","木","金","土","日"],
      //   cancel: 'キャンセル',
      //   clear: 'クリア',
      //   done: 'OK',
      // },
      yearRange: 50
    });

  });

});
