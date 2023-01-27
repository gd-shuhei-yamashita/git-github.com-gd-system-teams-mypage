//  郵便番号検索ウィンドウ
var STATE_DEFAULT = {
  'ja': {
    'value': '',
    'label': '' // 都道府県
  },
  'en': {
    'value': '',
    'label': '' // state
  }
}

var CONFIRMATION_SWICH_LANG = {
  'ja': {
    'buttons': {
      'confirm': '実行',
      'cancel': 'キャンセル'
    },
    'title': '切り替え確認',
    'content': '言語を切り替えると入力内容がクリアされます。本当に切り替えますか？'
  },
  'en': {
    'buttons': {
      'confirm': 'confirm',
      'cancel': 'cancel'
    },
    'title': 'Confirmation',
    'content': 'Changing the language will clear the input contents. Do you really want to switch?'
  }
}

var CONFIRMATION_MULTIPLE_ADDRESSES_MATCHED = {
  'ja': {
    'buttons': {
      'action': '設定',
      'cancel': 'キャンセル'
    },
    'title': '複数住所が該当しました。',
    'alert': '住所を選択してください。'
  },
  'en': {
    'buttons': {
      'action': 'configuration',
      'cancel': 'cancel'
    },
    'title': 'Multiple addresses are applicable.',
    'alert': 'Please select your address.'
  }
}

var BUTTONS = {
  'ja': {
    'btn-back': '戻る',
    'btn-next': '次へ',
    'btn-confirm': '内容確認'
  },
  'en': {
    'btn-back': 'Back',
    'btn-next': 'Next',
    'btn-confirm': 'Confirmation'
  }
}

// var MONEY_UNIT = {
//   'ja': {
//     ''
//   }
// }