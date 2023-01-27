function changeService() {
  if ($("#service_electric").is(':checked')) {
    $('.electric_last_day').css('display', 'block');
    $('.gas_last_day').css('display', 'none');
  } else if ($("#service_gas").is(':checked')) {
    $('.electric_last_day').css('display', 'none');
    $('.gas_last_day').css('display', 'block');
  } else if ($("#service_electric_gas").is(':checked')) {
    $('.electric_last_day').css('display', 'block');
    $('.gas_last_day').css('display', 'block');
  } else {
    $('.electric_last_day').css('display', 'none');
    $('.gas_last_day').css('display', 'none');
  }
}

function changeMoving() {
  if ($("#moving").is(':checked')) {
    $('.moving_info').css('display', 'block');
  } else {
    $('.moving_info').css('display', 'none');
  }
}

function showBalloon() {
  var wObjballoon = document.getElementById("makeImg");
  if (wObjballoon.className == "balloon1") {
    wObjballoon.className = "balloon_meter";
  } else {
    wObjballoon.className = "balloon1";
  }
}

function showBalloon_invoice() {
  var wObjballoon = document.getElementById("makeImg2");
  if (wObjballoon.className == "balloon2") {
    wObjballoon.className = "balloon_invoice";
  } else {
    wObjballoon.className = "balloon2";
  }
}

function setContractInfo(contract_list) {
  if(contract_list[$('#plan').val()]) {
    $("#add").html(contract_list[$('#plan').val()]['address']);
    $("#supplypoint_code").html(contract_list[$('#plan').val()]['supplypoint_code']);
    $("#name").html(contract_list[$('#plan').val()]['contract_name']);
    $("#plan_name").html(contract_list[$('#plan').val()]['plan']);
    $("input[name='add']").val(contract_list[$('#plan').val()]['address']);
    $("input[name='supplypoint_code']").val(contract_list[$('#plan').val()]['supplypoint_code']);
    $("input[name='name']").val(contract_list[$('#plan').val()]['contract_name']);
    $("input[name='plan_name']").val(contract_list[$('#plan').val()]['plan']);
  } else {
    $("#add").html('');
    $("#supplypoint_code").html('');
    $("#name").html('');
    $("#plan_name").html('');
    $("input[name='add']").val('');
    $("input[name='supplypoint_code']").val('');
    $("input[name='name']").val('');
    $("input[name='plan_name']").val('');
  }
}

function display_meter_note() {
  if ($("#meter_yes").is(':checked')) {
    $("#note_meter_yes").html('※解体日時の確認の為、カスタマーよりお電話させていただきます。');
  } else {
    $("#note_meter_yes").html('');
  }
}

$(function () {

  changeService();
  $('input[name="service"]').change(function () {
    changeService();
  });

  changeMoving();
  $('input[name="moving"]').change(function () {
    changeMoving();
  });

  new AgeRestriction('year1', 'month1', 'day1', 5);
  new AgeRestriction('year2', 'month2', 'day2', 5);
  new AgeRestriction('year3', 'month3', 'day3', 10);

  // 引っ越し先開始日
  if (start_year) {
    $('#year1').val(start_year);
  }
  if (start_month) {
    $('#month1').val(start_month);
  }
  if (start_day) {
    $('select#day1').empty();
    var stack_day = "<option value=''>選択</option>";
    for(var i = 1; i <= 31; i++) {
      stack_day += '<option value="' + i + '">' + i +'</option>';
    }
    $('select#day1').append(stack_day);
    $('#day1').val(start_day);
  }

  // 電気最終日
  if (electric_last_year) {
    $('#year2').val(electric_last_year);
  }
  if (electric_last_month) {
    $('#month2').val(electric_last_month);
  }
  if (electric_last_day) {
    $('select#day2').empty();
    var stack_day = "<option value=''>選択</option>";
    for(var i = 1; i <= 31; i++) {
      stack_day += '<option value="' + i + '">' + i +'</option>';
    }
    $('select#day2').append(stack_day);
    $('#day2').val(electric_last_day);
  }

  // ガス最終日
  if (gas_last_year) {
    $('#year3').val(gas_last_year);
  }
  if (gas_last_month) {
    $('#month3').val(gas_last_month);
  }
  if (gas_last_day) {
    $('select#day3').empty();
    var stack_day = "<option value=''>選択</option>";
    for(var i = 1; i <= 31; i++) {
      stack_day += '<option value="' + i + '">' + i +'</option>';
    }
    $('select#day3').append(stack_day);
    $('#day3').val(gas_last_day);
  }

  let contract_list = [];
  contracts.forEach(function(contract){
    contract_list[contract['supplypoint_code']] = contract;
  });

  if ($('#plan').val()) {
    setContractInfo(contract_list);
  }
  $('#plan').change(function () {
    setContractInfo(contract_list);
  });

  display_meter_note();
  $('input[name="meter"]').change(function () {
    display_meter_note();
  });

  $('#mail_form').validate({
    rules: {
      reason: {
        required: true,
      },
      service: {
        required: true,
      },
      name: {
        required: true,
      },
      mail: {
        required: true,
        email: true,
      },
      tel: {
        number: true,
        maxlength: 11,
        minlength: 11,
      },
      moving: {
        required: true,
      },
      start_year: {
        required: '#moving:checked',
      },
      start_month: {
        required: '#moving:checked',
      },
      start_day: {
        required: '#moving:checked',
      },
      new_postal: {
        required: '#moving:checked',
        number: true,
        maxlength: 7,
        minlength: 7,
      },
      new_add: {
        required: '#moving:checked',
      },
      electric_last_year: {
        required: function (element) {
          return $('#service_electric:checked') || $('#service_electric_gas:checked');
        }
      },
      electric_last_month: {
        required: function (element) {
          return $('#service_electric:checked') || $('#service_electric_gas:checked');
        }
      },
      electric_last_day: {
        required: function (element) {
          return $('#service_electric:checked') || $('#service_electric_gas:checked');
        }
      },
      gas_last_year: {
        required: function (element) {
          return $('#service_gas:checked') || $('#service_electric_gas:checked');
        }
      },
      gas_last_month: {
        required: function (element) {
          return $('#service_gas:checked') || $('#service_electric_gas:checked');
        }
      },
      gas_last_day: {
        required: function (element) {
          return $('#service_gas:checked') || $('#service_electric_gas:checked');
        }
      },
      meter: {
        required: true,
      },
      postal_send: {
        required: true,
        number: true,
        maxlength: 7,
        minlength: 7,
      },
      add_send: {
        required: true,
      },
      purapori: {
        required: true,
      },
      plan: {
        required: true,
      },
    },
    messages: {
      reason: {
        required: '選択して下さい',
      },
      service: {
        required: '選択して下さい'
      },
      name: {
        required: 'お名前を入力して下さい',
      },
      mail: {
        required: 'メールアドレスを入力して下さい',
        email: 'メールアドレスの形式が正しくありません。',
      },
      tel: {
        number: '半角数字で入力してください',
        maxlength: '半角数字は11桁で入力してください。',
        minlength: '半角数字は11桁で入力してください。',
      },
      moving: {
        required: '選択して下さい',
      },
      start_year: {
        required: '選択して下さい',
      },
      start_month: {
        required: '選択して下さい',
      },
      start_day: {
        required: '選択して下さい',
      },
      new_postal: {
        required: '郵便番号を入力して下さい',
        number: '半角数字7桁で入力してください。',
        maxlength: '半角数字7桁で入力してください。',
        minlength: '半角数字7桁で入力してください。',
      },
      new_add: {
        required: '住所を入力して下さい',
      },
      electric_last_year: {
        required: '選択して下さい',
      },
      electric_last_month: {
        required: '選択して下さい',
      },
      electric_last_day: {
        required: '選択して下さい',
      },
      gas_last_year: {
        required: '選択して下さい',
      },
      gas_last_month: {
        required: '選択して下さい',
      },
      gas_last_day: {
        required: '選択して下さい',
      },
      meter: {
        required: '選択して下さい',
      },
      postal_send: {
        required: '郵便番号を入力して下さい',
        number: '半角数字7桁で入力してください。',
        maxlength: '半角数字7桁で入力してください。',
        minlength: '半角数字7桁で入力してください。',
      },
      add_send: {
        required: '住所を入力して下さい',
      },
      purapori: {
        required: 'プライバシーポリシーを確認の上チェックしてください',
      },
      plan: {
        required: '選択して下さい',
      },
    },

    errorPlacement: function (error, element) {
      switch (element.attr('name')) {
        case "reason":
          error.insertAfter($('#error_reason'));
          break;
        case "service":
          error.insertAfter($('#error_service'));
          break;
        case "start_year":
          error.insertAfter($('#year1'));
          break;
        case "start_month":
          error.insertAfter($('#month1'));
          break;
        case "start_day":
          error.insertAfter($('#day1'));
          break;
        case "electric_last_year":
          error.insertAfter($('#year2'));
          break;
        case "electric_last_month":
          error.insertAfter($('#month2'));
          break;
        case "electric_last_day":
          error.insertAfter($('#day2'));
          break;
        case "gas_last_year":
          error.insertAfter($('#year3'));
          break;
        case "gas_last_month":
          error.insertAfter($('#month3'));
          break;
        case "gas_last_day":
          error.insertAfter($('#day3'));
          break;
        case "meter":
          error.insertAfter($('#error_meter'));
          break;
        case "moving":
          error.insertAfter($('#error_moving'));
          break;
        case "purapori":
          error.insertAfter($('#error7'));
          break;
        default:
          error.insertAfter(element);
      }
    }
  })

})
