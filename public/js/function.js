// 各種ライブラリ

// jsonファイルアップロード時
// ex. javascriptでBase64  https://qiita.com/weal/items/1a2af81138cd8f49937d
var Base64a = {
  encode: (function(i, tbl) {
      for(i=0,tbl={64:61,63:47,62:43}; i<62; i++) {tbl[i]=i<26?i+65:(i<52?i+71:i-4);} //A-Za-z0-9+/=
      return function(arr) {
          var len, str, buf;
          if (!arr || !arr.length) {return "";}
          for(i=0,len=arr.length,buf=[],str=""; i<len; i+=3) { //6+2,4+4,2+6
              str += String.fromCharCode(
                  tbl[arr[i] >>> 2],
                  tbl[(arr[i]&3)<<4 | arr[i+1]>>>4],
                  tbl[i+1<len ? (arr[i+1]&15)<<2 | arr[i+2]>>>6 : 64],
                  tbl[i+2<len ? (arr[i+2]&63) : 64]
              );
          }
          return str;
      };
  }()),
  decode: (function(i, tbl) {
      for(i=0,tbl={61:64,47:63,43:62}; i<62; i++) {tbl[i<26?i+65:(i<52?i+71:i-4)]=i;} //A-Za-z0-9+/=
      return function(str) {
          var j, len, arr, buf;
          if (!str || !str.length) {return [];}
          for(i=0,len=str.length,arr=[],buf=[]; i<len; i+=4) { //6,2+4,4+2,6
              for(j=0; j<4; j++) {buf[j] = tbl[str.charCodeAt(i+j)||0];}
              arr.push(
                  buf[0]<<2|(buf[1]&63)>>>4,
                  (buf[1]&15)<<4|(buf[2]&63)>>>2,
                  (buf[2]&3)<<6|buf[3]&63
              );
          }
          if (buf[3]===64) {arr.pop();if (buf[2]===64) {arr.pop();}}
          return arr;
      };
  }())
};

/**
 * csvファイルを動的にダウンロードできる
 * 指定したidのリンク自身からダウンロード実施
 * 
 * ex. ブラウザでBase64で受け取ったファイルをダウンロードする https://www.hos.co.jp/blog/20170213/
 * @param {*} id 
 * @param {*} base64 
 */
function downloadCsv(id , base64) {
  var mime_ctype = "text/csv";
  var blob = toBlob(base64, mime_ctype);

  if (window.navigator.msSaveBlob) {
      // IEやEdgeの場合、Blob URL Schemeへと変換しなくともダウンロードできる
      window.navigator.msSaveOrOpenBlob(blob, "file.csv"); 
  } else {
      // BlobをBlob URL Schemeへ変換してリンクタグへ埋め込む
      $("#"+id).prop("href", window.URL.createObjectURL(blob));
      // リンクをクリックする
      document.getElementById(id).click();
  }
  return 0;
}

/**
 * Base64とMIMEコンテンツタイプからBlobオブジェクトを作成する。
 * 日本語対応。
 * 
 * @param base64 
 * @param mime_ctype MIMEコンテンツタイプ
 * @returns Blob
 */
function toBlob(base64, mime_ctype) {
  // 日本語の文字化けに対処するためBOMを作成する。
  var bom = new Uint8Array([0xEF, 0xBB, 0xBF]);

  var bin = atob(base64.replace(/^.*,/, ''));
  var buffer = new Uint8Array(bin.length);
  for (var i = 0; i < bin.length; i++) {
      buffer[i] = bin.charCodeAt(i);
  }
  // Blobを作成
  try {
      var blob = new Blob([bom, buffer.buffer], {
          type: mime_ctype,
      });
  } catch (e) {
      return false;
  }
  return blob;
}

/**
 * textareaへ代入する際のエスケープ、改行処理を適切におこなう
 * @param {*} str 
 */
function DbtextToTextarea(str) {
    // php htmlentities の逆にあたる処理
    var ret_str = str.replace(/&lt;/g, '<');
    ret_str = ret_str.replace(/&gt;/g, '>');
    ret_str = ret_str.replace(/&quot;/g, '"');
    ret_str = ret_str.replace(/&apos;/g, "'");
    ret_str = ret_str.replace(/(\\r)?\\n/g, "\n");
    return ret_str;
}

/**
 * ページング番号の表示を返す
 * @param {*} pg_maxpage    最大ページ
 * @param {*} pg_now_state  現在ページ
 * @param {*} function_name 関数名  paging_click
 */
function PaginationNumber( pg_maxpage , pg_now_state, function_name ) {
    var html_paging = "";
    /** ページングの並び数 */
    var def_space = 10;
    /** ページのページング現在 */
    var grand_now_state = Math.floor(pg_now_state / def_space);
    /** ページのページング最大 */
    var grand_maxpage   = Math.ceil(pg_maxpage / def_space);
    /** 表示範囲 */
    var grand_pagerange = (grand_now_state*def_space + def_space);
    // ページングを表示する
    // html_paging = "<ul class='pagination'>";
	html_paging = "";
    // 前へ戻る
    if (pg_now_state > 0) {
        html_paging+= "<a href='#' onclick='"+function_name+"("+(pg_now_state - 1)+"); return false;' ><img src='/img/chevron_upward_black.svg'></a>";
    } else {
        html_paging+= "<a href='#' onclick='return false;'><img src='/img/chevron_upward_black.svg'></a>";
    }
    // 前の def_space ページ
    if (grand_now_state > 0) {
        html_paging+= "<a href='#' onclick='"+function_name+"("+((grand_now_state*def_space) - 1)+"); return false;' > ... </a>&nbsp;";
    } else {
        html_paging+= "<a href='#' onclick='return false;'> ... </a>&nbsp;";
    }
  
    // ページング番号生成 ( def_space 毎にする)
    for (Loop1 = (grand_now_state*def_space); Loop1 < ((grand_pagerange > pg_maxpage) ? pg_maxpage : grand_pagerange) ; Loop1++) {
		if ( pg_now_state == Loop1 ) {
			html_paging+= "<a href='#'  onclick='"+function_name+"("+(Loop1)+"); return false;' >["+ (Loop1+1 ) +"]</a>&nbsp;";
		} else {
			html_paging+= "<a href='#'  onclick='"+function_name+"("+(Loop1)+"); return false;' >"+ (Loop1+1 ) +"</a>&nbsp;";
		}
    }

    // 次の def_space ページ
    if (grand_now_state < (grand_maxpage-1)) {
        html_paging+= "<a href='#' onclick='"+function_name+"("+(grand_pagerange )+"); return false;' > ... </a>&nbsp;";
    } else {
        html_paging+= "<a href='#' onclick='return false;'> ... </a>&nbsp;";
    }

    // 次に進む
    if (pg_now_state < (pg_maxpage-1)) {
        html_paging+= "<a href='#' class='right' onclick='"+function_name+"("+(pg_now_state + 1)+"); return false;' ><img src='/img/chevron_upward_black.svg'></a>";
    } else {
        html_paging+= "<a href='#' class='right' onclick='return false;'><img src='/img/chevron_upward_black.svg'></a>";
    }
    // html_paging+= "</ul>";

    return html_paging;
}

/**
 * Get the URL parameter value
 * ex. JavaScriptでGETパラメーターを取得する http://www-creators.com/archives/4463
 * @param  name {string} パラメータのキー文字列
 * @return  url {url} 対象のURL文字列（任意）
 */
function getUrlParam(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

/**
 * ログアウト  
 */
$('.menu_logout').on('click', function() {
    event.preventDefault();
    $.confirm({
        title: '',
        content: 'ログアウトしてよろしいですか？',
        buttons: {
        formSubmit: {
            text: 'はい',
            action: function () {
                // location.href = '/logout';
                location.href = $('a.menu_logout').attr('href');
                // console.log($('a.menu_logout').attr('href')); // $('.menu_logout')
            }
        },
        キャンセル: function () {}
        },
        onContentReady: function () {
        var jc = this;
        this.$content.find('#password_edit').on('submit', function (e) {
            e.preventDefault();
            jc.$$formSubmit.trigger('click');
        });
        }
    });
});

$(function(){
    /**
     * クリアボタンのついたフォームで ボタン押下時の削除 (初期化時に実行されます)
     */
    $('.searchclear').click(function(){
        $(this).parent().find('input').val('');
    });

    /**
     * パスワードの表示・非表示切替
     */
    $(".toggle-password").click(function () {
        console.log("toggle-password click");
        // 入力フォームの取得
        // var input = $(this).parent().prev("input");
        var input = $(this).parent().find("input");
        // type切替
        if (input.attr("type") == "password") {
            // iconの切り替え visibility
            $(this).text("visibility"); //  visibility / visibility_off
            input.attr("type", "text");
        } else {
            // iconの切り替え visibility
            $(this).text("visibility_off"); //  visibility / visibility_off
            input.attr("type", "password");
        }
    });
});

