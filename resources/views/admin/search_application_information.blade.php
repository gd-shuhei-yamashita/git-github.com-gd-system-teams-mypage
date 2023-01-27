{{-- 申込情報検索画面 --}}
@extends('layout.t_common')

@section('title','申込情報検索')

@section('pageCss')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<!--<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">-->



@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 2)
@section("cate2", 6)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')
<style>
nav {height: auto;}
nav ul a {font-size: revert; padding:0;color: #211816;}
.select-wrapper input.select-dropdown {height: 50px;border-bottom:none;}
[type="checkbox"]+span:not(.lever) {font-size:10pt}
label {color: #211816}

</style>

        <div class="l-main">
            <h2>申込情報検索<div class="h2-admin-information-border"></div>
            </h2>

            <div class="input-field-text">
                <a>申込情報を検索し該当ユーザを一覧できます、必要な場合ユーザの覗き見が可能です。</a>
            </div>
			<input type="hidden" id="now_tab" name="now_tab" value="0">
            <section class="typeA">
                <input id="TAB-A01" type="radio" name="TAB-A" checked="checked">
                <label class="tabLabel tabLabel1" for="TAB-A01">簡易検索</label>
                <div class="content">
                    <div class="form-area info">
                        <form id="submit_section" action="#" method="post">
                            <div class="input-area">
                                <div class="input-field input-field1">
                                    <label>マイページ ID（10文字）</label>
                                    <input id="customer_code" name="customer_code"  type="text" class="validate" data-length="10" placeholder="例:MC00000000">
                                </div>
								<div id="customer_code_err" class="error red-text" style="float: left;"></div>
                                <div class="input-field">
                                    <label>供給地点番号（22文字）</label>
                                    <input id="supplypoint_code" name="supplypoint_code" type="tel" class="validate" data-length="22" placeholder="例:0000000000000000000000">
                                </div>
								<div id="supplypoint_code_err" class="error red-text" style="float: left;"></div>
                            </div>

                            <div class="input-field register">
                                <button type="button" name="" id="btn_search">検索する<img src="/img/search_black_24dp.svg"></button>
                            </div>
                    </div>

                    <div class="result">
                        <label>表示件数</label>
                        <div class="input-field page-field">
                            <div class="pul">
                                <select id="display_number" name="display_number" class="pul">
								  <option value="2">2</option>
								  <option value="5">5</option>
								  <option value="10" selected>10</option>
								  <option value="50">50</option>
								  <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>

                <input id="TAB-A02" type="radio" name="TAB-A">
                <label class="tabLabel tabLabel2" for="TAB-A02">詳細検索</label>
                <div class="content">
                    <div class="form-area info">
                        <form>
                            <div class="input-area user">
                                <div class="input-field input-field1">
                                    <label>マイページ ID（10文字）</label>
                                    <input id="d_customer_code"  name="d_customer_code" type="text" class="validate" data-length="10" placeholder="例:MC00000000">
                                </div>
                                <div class="input-field">
                                    <label>お客様名（部分一致可）</label>
                                    <input id="d_customer_name" name="d_customer_name" type="text" class="validate" placeholder="例:御利用 遊座">
                                </div>
                                <div class="input-field input-field1">
                                    <label>電話番号</label>
                                    <input id="d_phone" name="d_phone" type="tel" class="validate" placeholder="例:090-1234-5678">
                                </div>
                                <div class="input-field">
                                    <label>メールアドレス（部分一致可）</label>
                                    <input id="d_email" name="d_email" type="text" class="validate" placeholder="test@example.com">
                                </div>
                                <div class="input-field input-field1">
                                    <label>郵便番号</label>
                                    <input id="d_zip_code" name="d_zip_code" type="tel" class="validate" placeholder="例:111-1111">
                                </div>
                                <div class="input-field">
                                    <label>供給地点番号（22文字）</label>
                                    <input id="d_supplypoint_code" name="d_supplypoint_code" type="tel" class="validate" data-length="22" placeholder="例:0000000000000000000000">
                                </div>
                            </div>
                            <div class="check">
                                <label><input type="checkbox" class="filled-in" id="d_search_testuser" name="d_search_testuser" value="1" /><span>管理者/ テストユーザー検索を行う</span></label>
                                <label><input type="checkbox" class="filled-in" id="d_search_deleteuser" name="d_search_deleteuser" value="1" /><span>削除済みユーザーの検索を行う</span></label>
                            </div>

                            <div class="input-field register">
                                <button type="submit" name="" id="btn_search_detail" >検索する<img src="/img/search_black_24dp.svg"></button>
                            </div>
                    </div>

                    <div class="result">
                        <label>表示件数</label>
                        <div class="input-field page-field">
                            <div class="pul">
                                <select id="d_display_number" name="d_display_number" class="pul">
								  <option value="2">2</option>
								  <option value="5">5</option>
								  <option value="10" selected>10</option>
								  <option value="50">50</option>
								  <option value="100">100</option>
                                </select>
                            </div>

                        </div>
                        </form>
                    </div>
                </div>
					<div class="result">
                            <div class="page" id="result1_pagination">
                                <a href="#"><img src="/img/chevron_upward_black.svg"></a>
                                <p>1/10</p>
                                <a href="#" class="right"><img src="/img/chevron_upward_black.svg"></a>
                            </div>
                    </div>
				                        <div class="table-area">
                            <table id="result1_list">
                                <thead>
                                    <tr>
                                        <th>種別</th>
                                        <th>マイページID</th>
                                        <th>お客様名</th>
                                        <th>電話番号</th>
                                        <th>メールアドレス</th>
                                        <th>郵便番号</th>
                                        <th>供給地点</th>
										<th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><a><img src="/img/perm_identity_black.svg">個</a></td>
                                        <td>ADMN001003</td>
                                        <td>mizuki.akimoto@grandata-grp.co.jp </td>
                                        <td>御利用 遊座</td>
                                        <td>113-3333</td>
                                        <td>090-9999-9999</td>
                                        <td> 9999999999999999999000 :<br>
                                            東京都豊島区南大塚X丁目XX-XX YYYYYYビルディングZ階</td>
                                    </tr>
                                    <tr>
                                        <td><a><img src="/img/perm_identity_black.svg">個</a></td>
                                        <td>ADMN001003</td>
                                        <td>mizuki.akimoto@grandata-grp.co.jp </td>
                                        <td>御利用 遊座</td>
                                        <td>113-3333</td>
                                        <td>090-9999-9999</td>
                                        <td> 9999999999999999999000 :<br>
                                            東京都豊島区南大塚X丁目XX-XX YYYYYYビルディングZ階</td>
                                    </tr>
                                    
                                </tbody>

                            </table>
                        </div>

            </section>

        </div>

<!-- 編集ボタン押した後のモーダルここから -->
<div id="modal1" class="modal">
  <div class="modal-content">
    <h4>ユーザ管理メニュー </h4>
    <h5>ID:<span id="edit_serial">0</span> / お客様名: <span id="edit_username">名前</span></h5>

    <p>削除フラグ 
      <div class="switch">
        <label>
          Off
          <input type="checkbox" id="edit_deleted">
          <span class="lever"></span>
          On
        </label>
      </div>
    </p>

    <p>初回認証
      <div class="switch">
        <label>
          Off
          <input type="checkbox" id="edit_ninshou">
          <span class="lever"></span>
          On
        </label>
      </div>
    </p>
    
  </div>
  <div class="modal-footer">
  <a href="#!" class="modal-action modal-close btn orange darken-1" id="edit_changed">反映</a>  <a href="#!" class="modal-action modal-close btn grey">キャンセル</a>
  </div>
</div>
<!-- 編集ボタン押した後のモーダルここまで -- >



<!--<main>
<div class="container">
  <div class="col s12">
    <div class="section">申込情報を検索し該当ユーザを一覧できます、必要な場合ユーザの覗き見が可能です。</div>

    <ul class="tabs tabs-fixed-width">
      <li class="tab"><a id="tab1" href="#test1">簡易</a></li>
      <li class="tab"><a id="tab2" href="#test2">詳細</a></li>
      <li class="tab disabled"><a id="tab3" href="#test3">選択条件</a></li>
    </ul>
    <input type="hidden" id="now_tab" name="now_tab" value="0">

    <div id="test1" class="col s12">
      <!-- 申込情報検索 -- >
      <form id="submit_section" action="#" method="post">
        <div class="row" style="margin-bottom: 0px;">
          <div class="input-field col s12 m5">
            <i class="material-icons prefix">account_circle</i>
            <input id="customer_code" name="customer_code"  type="text" class="validate" data-length="10" placeholder="例:MC00000000">
            <i class="material-icons searchclear ">clear</i>
            <label for="customer_code">マイページID (9~10文字)</label>
            <div id="customer_code_err" class="error red-text" style="float: left;"></div>
          </div>
          <div class="input-field col s12 m7">
            <i class="material-icons prefix">place</i>
            <input id="supplypoint_code" name="supplypoint_code" type="tel" class="validate" data-length="22" placeholder="例:0000000000000000000000">
            <i class="material-icons searchclear ">clear</i>
            <label for="supplypoint_code">供給地点特定番号 (22文字)</label>
            <div id="supplypoint_code_err" class="error red-text" style="float: left;"></div>
          </div>
        </div>

        <div class="row">
          <a id="btn_search" class="waves-effect waves-light btn col s10 offset-s1 m6 offset-m3 btn-large orange darken-1 tooltipped" data-html="true" data-position="down" data-tooltip="申込情報を各種キーから検索します。">
            <i class="material-icons left">person_pin</i>検索
          </a>
          <div class="input-field col s4 offset-s1 m2 offset-m1 ">
            <select id="display_number" name="display_number">
              <option value="2">2</option>
              <option value="5">5</option>
              <option value="10" selected>10</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <label>表示件数</label>
          </div>          
        </div>
      </form>
      <!-- /申込情報検索 -- >

    </div>

    <div id="test2" class="col s12" style="display:none">
      <!-- 申込情報検索 -- >
      <form id="submit_section" action="#" method="post">
        <div class="row" style="margin-bottom: 0px;">
          <div class="input-field col s12 m5">
            <i class="material-icons prefix">account_circle</i>
            <input id="d_customer_code"  name="d_customer_code" type="text" class="validate" data-length="10" placeholder="例:MC00000000">
            <i class="material-icons searchclear ">clear</i>
            <label for="d_customer_code">マイページID (9~10文字)</label>
            <div id="d_customer_code_err" class="error red-text" style="float: left;"></div>
          </div>
          <div class="input-field col s12 m7">
            <i class="material-icons prefix">place</i>
            <input id="d_supplypoint_code" name="d_supplypoint_code" type="tel" class="validate" data-length="22" placeholder="例:0000000000000000000000">
            <i class="material-icons searchclear ">clear</i>
            <label for="d_supplypoint_code">供給地点特定番号 (22文字)</label>
            <div id="d_supplypoint_code_err" class="error red-text" style="float: left;"></div>
          </div>
        </div>

        <div class="row" style="margin-bottom: 0px;">
          <div class="input-field col s12 m5">
            <i class="material-icons prefix">alternate_email</i>
            <input id="d_email" name="d_email" type="text" class="validate" placeholder="test@example.com">
            <i class="material-icons searchclear ">clear</i>
            <label for="d_email">メールアドレス [部分一致可]</label>
            <div id="d_email_err" class="error red-text" style="float: left;"></div>
          </div>
          <div class="input-field col s12 m5">
            <i class="material-icons prefix">local_post_office</i>
            <input id="d_zip_code" name="d_zip_code" type="tel" class="validate" placeholder="例:111-1111">
            <i class="material-icons searchclear ">clear</i>
            <label for="d_zip_code">郵便番号</label>
            <div id="d_zip_code_err" class="error red-text" style="float: left;"></div>
          </div>
        </div>

        <div class="row" style="margin-bottom: 0px;">
          <div class="input-field col s12 m5">
            <i class="material-icons prefix">person</i>
            <input id="d_customer_name" name="d_customer_name" type="text" class="validate" placeholder="例:御利用 遊座">
            <i class="material-icons searchclear ">clear</i>
            <label for="d_customer_name">お客様名 [部分一致可]</label>
            <div id="d_customer_name_err" class="error red-text" style="float: left;"></div>
          </div>
          <div class="input-field col s12 m5">
            <i class="material-icons prefix">phone</i>
            <input id="d_phone" name="d_phone" type="tel" class="validate" placeholder="例:090-1234-5678">
            <i class="material-icons searchclear ">clear</i>
            <label for="d_phone">電話番号 </label>
            <div id="d_phone_err" class="error red-text" style="float: left;"></div>
          </div>
        </div>

        <div class="row" >
          <div class="col s12 m5">
          <label>
            <input type="checkbox" class="filled-in" id="d_search_testuser" name="d_search_testuser" value="1" />
            <span>管理者／テストユーザ検索を行う</span>
          </label>
          </div>
          <div class="col s12 m5">
          <label>
            <input type="checkbox" class="filled-in" id="d_search_deleteuser" name="d_search_deleteuser" value="1" />
            <span>削除済みユーザの検索を行う</span>
          </label>
          </div>
        </div>

        <div class="row">
          <a id="btn_search_detail" class="waves-effect waves-light btn col s10 offset-s1 m6 offset-m3 btn-large orange darken-1 tooltipped" data-html="true" data-position="down" data-tooltip="申込情報を各種キーから検索します。">
            <i class="material-icons left">person_pin</i>検索
          </a>

          <div class="input-field col s4 offset-s1 m2 offset-m1 ">
            <select id="d_display_number" name="d_display_number">
              <option value="" disabled >表示件数</option>
              <option value="2">2</option>
              <option value="5">5</option>
              <option value="10" selected>10</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <label>Number Select</label>
          </div>
        </div>
        <input type="hidden" id="d_now_state" name="d_now_state" value="0">
      </form>
      <!-- /申込情報検索 -- >
    </div>

    <div id="test3" class="col s12">
      <p>&nbsp;</p>
    </div>
    
    <!-- results_area -- >
    <div id="result1" class="row z-depth-1" style="display:none">
      <div class="col s12 m6">
        <h5>検索結果：(1件 のうち 1～1件)</h5>
      </div>
      <div class="col s12 m6" id="result1_pagination">
        <ul class="pagination">
          <li class="disabled"><a href="#!"><i class="material-icons">chevron_left</i></a></li>
          <li class="active"><a href="#!">1</a></li>
          <li class="waves-effect"><a href="#!">2</a></li>
          <li class="waves-effect"><a href="#!">3</a></li>
          <li class="waves-effect"><a href="#!">4</a></li>
          <li class="waves-effect"><a href="#!">5</a></li>
          <li class="waves-effect"><a href="#!"><i class="material-icons">chevron_right</i></a></li>
        </ul>
      </div>

      <div class="col s12" style="width:100%;height:100%;overflow-x:scroll;margin:0;padding:8">
          <table id="result1_list" style="white-space: nowrap;">
            <thead>
              <tr>
                <th>Line</th>
                <th>種別</th>
                <th>マイページID</th>
                <th>メールアドレス</th>
                <th>お客様名</th>
                <th>郵便番号</th>
                <th>ご連絡先電話番号</th>
                <th>供給地点</th>
                <th>初回認証</th>
                <th>編集</th>
              </tr>
            </thead>

            <tbody>
              <tr>
                <td>1</td>
                <td><i class="material-icons">person</i></td>
                <td>MC00000042</td>
                <td>test01@example.com</td>
                <td>御利用 遊座</td>
                <td>1111111</td>
                <td>09012345678</td>
                <td> 0101160611780002062100 : <br/>東京都豊島区南池袋2丁目49番地7号</td>
                <td>○</td>
                <td>-</td>
              </tr>
            </tbody>
          </table>
      </div>
    </div>
    <!-- /results_area -- >
    <br/>
    <br/>
    <br/>
    <br/>
  </div>
</div>
<!-- 編集ボタン押した後のモーダルここから -- >
<div id="modal1" class="modal">
  <div class="modal-content">
    <h4>ユーザ管理メニュー </h4>
    <h5>ID:<span id="edit_serial">0</span> / お客様名: <span id="edit_username">名前</span></h5>

    <p>削除フラグ 
      <div class="switch">
        <label>
          Off
          <input type="checkbox" id="edit_deleted">
          <span class="lever"></span>
          On
        </label>
      </div>
    </p>

    <p>初回認証
      <div class="switch">
        <label>
          Off
          <input type="checkbox" id="edit_ninshou">
          <span class="lever"></span>
          On
        </label>
      </div>
    </p>
    
  </div>
  <div class="modal-footer">
  <a href="#!" class="modal-action modal-close btn orange darken-1" id="edit_changed">反映</a>  <a href="#!" class="modal-action modal-close btn grey">キャンセル</a>
  </div>
</div>
<!-- 編集ボタン押した後のモーダルここまで -- >
</main>-->

@include('layout.t_copyright2')
@yield('copyright2')
@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')

<script src="{{asset('js/entry.js') }}"></script>
<!--<script src="{{asset('js/reminder.js') }}"></script>-->
<script src="{{asset('js/admin_search_information.js') }}"></script>
<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
