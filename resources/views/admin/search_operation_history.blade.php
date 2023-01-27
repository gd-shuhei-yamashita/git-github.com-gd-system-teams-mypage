{{-- 操作履歴検索画面 --}}
@extends('layout.t_common')

@section('title','操作履歴検索')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<!--<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">-->

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 2)
@section("cate2", 7)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')

        <div class="l-main">
            <h2>操作履歴検索<div class="h2-admin-border"></div>
            </h2>

            <div class="input-field-text">
                <a>日付、ログインアカウントをキーとして操作履歴を検索し、表示します</a>
            </div>
			<input type="hidden" id="now_tab" name="now_tab" value="0">

                    <div class="form-area info">
                        <form>
                            <div class="input-area">
                                <div class="input-field input-field2">
                                    <label>マイページ ID（10文字）</label>
                                    <input id="customer_code" name="customer_code"  type="text" class="validate" data-length="10" placeholder="例：TS00000000">
                                </div>
                                <div class="time">
                                <div class="input-field input-field3">
                                    <label>表示期間(過去)</label>
                                    <input placeholder="日付(例:2019/01/01)" id="notice_date_from" name="notice_date_from" type="text" class="validate no-autoinit"  value="{{ old('notice_date') ? old('notice_date') : $forms['date'] }}">
                                </div>
                                <p>～</p>
                                <div class="input-field input-field3">
                                    <label>(未来)</label>
                                    <input placeholder="日付(例:2019/01/01)" id="notice_date_to" name="notice_date_to" type="text" class="validate no-autoinit" value="{{ old('notice_date') ? old('notice_date') : $forms['date'] }}">
                                </div>
                                </div>
                            </div>

                            <div class="input-field register">
                                <button type="button" name="" id="btn_search">検索する<img src="/img/search_black_24dp.svg"></button>
                            </div>
                        </form>
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
					<div class="result">
                            <div class="page" id="result1_pagination">
                                <a href="#"><img src="/img/chevron_upward_black.svg"></a>
                                <p>1/10</p>
                                <a href="#" class="right"><img src="/img/chevron_upward_black.svg"></a>
                            </div>
                    </div>
                        <div class="table-area rireki">
                            <table id="result1_list">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>時間</th>
                                        <th>マイページID</th>
                                        <th>設定名</th>
                                        <th>要求メソット</th>
                                        <th>ファイル名</th>
                                        <th>要求結果</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1111111</td>
                                        <td>2021-08-23<br>06:35:00</td>
                                        <td>ADMN001003</td>
                                        <td>search_operation_history </td>
                                        <td>GET</td>
                                        <td>null</td>
                                        <td>200</td>
                                    </tr>
                                    
                                </tbody>

                            </table>
                        </div>
                    </div>


        </div>




<!-- <main>
<div class="container">
  <div class="col s12">
  <!-- 操作履歴検索 -- >
    <div class="section">日付、ログインアカウントをキーとして操作履歴を検索し、表示する</div>

    <ul class="tabs tabs-fixed-width">
      <li class="tab"><a class="active" href="#test1">簡易</a></li>
      <li class="tab disabled"><a href="#test2">詳細</a></li>
      <li class="tab disabled"><a href="#test3">選択条件</a></li>
    </ul>
    <input type="hidden" id="now_tab" name="now_tab" value="0">

    <div id="test1" class="col s12">
      <!-- 操作履歴検索 -- >
      <form id="submit_section" action="#" method="post">
        <div class="row" style="margin-bottom: 0px;">
          <div class="input-field col s12 m5">
            <i class="material-icons prefix">account_circle</i>
            <input id="customer_code" name="customer_code"  type="text" class="validate" data-length="10">
            <i class="material-icons searchclear ">clear</i>
            <label for="customer_code">マイページID (9~10文字)</label>
            <div id="customer_code_err" class="error red-text" style="float: left;"></div>
          </div>

          <div class="input-field col s6 m3">
            <i class="material-icons prefix">date_range</i>
            <input placeholder="日付(例:2019/01/01)" id="notice_date_from" name="notice_date_from" type="text" class="validate datepicker no-autoinit"  value="{{ old('notice_date') ? old('notice_date') : $forms['date'] }}">
            <i class="material-icons searchclear ">clear</i>
            <label for="notice_date_from">表示期間 (過去)～</label>
            <div id="notice_date_from_err" class="error red-text" style="float: left;">{{$errors->first('notice_date')}}</div>
          </div>

          <div class="input-field col s6 m3">
            <i class="material-icons prefix">arrow_right_alt</i>
            <input placeholder="日付(例:2019/01/01)" id="notice_date_to" name="notice_date_to" type="text" class="validate datepicker no-autoinit" value="{{ old('notice_date') ? old('notice_date') : $forms['date'] }}">
            <i class="material-icons searchclear ">clear</i>
            <label for="notice_date_to"> ～(未来)</label>
            <div id="notice_date_to_err" class="error red-text" style="float: left;">{{$errors->first('notice_date')}}</div>
          </div>

        </div>

        <div class="row">
          <a id="btn_search" class="waves-effect waves-light btn col s10 offset-s1 m6 offset-m3 btn-large orange darken-1 tooltipped" data-html="true" data-position="down" data-tooltip="申込情報を各種キーから検索します。">
            <i class="material-icons left">person_pin</i>検索
          </a>
          <div class="input-field col s4 offset-s1 m2 offset-m1 ">
            <select id="display_number" name="display_number">
              <option value="10" selected>10</option>
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="500">500</option>
            </select>
            <label>表示件数</label>
          </div>          
        </div>
      </form>
      <!-- /操作履歴検索 -- >

    </div>

    <div id="test2" class="col s12" style="display:none">
      <!-- 操作履歴検索 -- >
      <form id="submit_section" action="#" method="post">
        <div class="row" style="margin-bottom: 0px;">
          <div class="input-field col s12 m5">
            <i class="material-icons prefix">account_circle</i>
            <input id="d_customer_code"  name="d_customer_code" type="text" class="validate" data-length="10">
            <i class="material-icons searchclear ">clear</i>
            <label for="d_customer_code">マイページID (9~10文字)</label>
            <div id="d_customer_code_err" class="error red-text" style="float: left;"></div>
          </div>
          <div class="input-field col s12 m7">
            <i class="material-icons prefix">calendar_today</i>
            <input id="d_supplypoint_code" name="d_supplypoint_code" type="tel" class="validate" data-length="22">
            <i class="material-icons searchclear ">clear</i>
            <label for="d_supplypoint_code">表示期間 ～</label>
            <div id="d_supplypoint_code_err" class="error red-text" style="float: left;"></div>
          </div>
        </div>

        <div class="row" style="margin-bottom: 0px;">
          <div class="input-field col s12 m5">
            <i class="material-icons prefix">alternate_email</i>
            <input id="d_email" name="d_email" type="text" class="validate">
            <i class="material-icons searchclear ">clear</i>
            <label for="d_email">メールアドレス [部分一致可]</label>
            <div id="d_email_err" class="error red-text" style="float: left;"></div>
          </div>
          <div class="input-field col s12 m5">
            <i class="material-icons prefix">local_post_office</i>
            <input id="d_zip_code" name="d_zip_code" type="tel" class="validate">
            <i class="material-icons searchclear ">clear</i>
            <label for="d_zip_code">郵便番号 (例:111-1111)</label>
            <div id="d_zip_code_err" class="error red-text" style="float: left;"></div>
          </div>
        </div>

        <div class="row" style="margin-bottom: 0px;">
          <div class="input-field col s12 m5">
            <i class="material-icons prefix">person</i>
            <input id="d_customer_name" name="d_customer_name" type="text" class="validate">
            <i class="material-icons searchclear ">clear</i>
            <label for="d_customer_name">お客様名 (例:御利用 遊座) [部分一致可]</label>
            <div id="d_customer_name_err" class="error red-text" style="float: left;"></div>
          </div>
          <div class="input-field col s12 m5">
            <i class="material-icons prefix">phone</i>
            <input id="d_phone" name="d_phone" type="tel" class="validate">
            <i class="material-icons searchclear ">clear</i>
            <label for="d_phone">電話番号 (例:090-1234-5678)</label>
            <div id="d_phone_err" class="error red-text" style="float: left;"></div>
          </div>
        </div>

        <div class="row" >
          <div class="col s12 m5">
          <label>
            <input type="checkbox" class="filled-in" id="d_phone" name="d_phone"  />
            <span>管理者,テストユーザ検索を行う</span>
          </label>
          </div>
          <div class="col s12 m5">
          <label>
            <input type="checkbox" class="filled-in" id="d_phone" name="d_phone"  />
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
              <option value="10" selected>10</option>
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="500">500</option>
            </select>
            <label>Number Select</label>
          </div>
        </div>
        <input type="hidden" id="d_now_state" name="d_now_state" value="0">
      </form>
      <!-- /操作履歴検索 -- >
    </div>

    <div id="test3" class="col s12">
      <p>&nbsp;</p>
    </div>
  </div>


    <!-- results_area -- >
    <div id="result1" class="row z-depth-1" style="display:none">
      <div class="col s12 m12">
        <h5>一覧：(1件 のうち 1～1件)</h5>
      </div>

      <div class="col s12 m12" id="result1_pagination">
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

      <div class="col s12" style="margin:0;padding:8">
          <table id="result1_list" >
            <thead>
              <tr>
                <th>ID</th>
                <th>時間</th>
                <th>マイページID</th>
                <th>設定名</th>
                <th>要求メソッド</th>
                <th>ファイル名</th>
                <th>要求結果</th>
              </tr>
            </thead>

            <tbody>
              <tr>
                <td>4</td>
                <td>1/1 01:00</td>
                <td>MC00000001</td>
                <td>capture_application_information</td>
                <td>GET</td>
                <td>使用量データ02.csv</td>
                <td>200</td>
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
</main>-->
@include('layout.t_copyright2')
@yield('copyright2')
@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="{{asset('js/admin_search_history.js') }}"></script>
<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
