{{-- 譲渡変更画面 --}}
@extends('layout.t_common')

@section('title','譲渡変更')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<!--<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">-->

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 2)
@section("cate2", 2)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')

        <div class="l-main">
            <h2>譲渡変更<div class="h2-admin-border"></div>
            </h2>

            <div class="input-field-text">
                <a>マイページ ID に紐付く供給地点特定番号を別のマイページ ID に譲渡します。</a>
            </div>
            <div class="mds">
            <p>新規登録</p>
            </div>

                    <div class="form-area info">
                        <form id="submit_section" action="{{ route('change_transfer_store') }}" method="post">
						<input type="hidden" name="cid" id="cid" value="0" />{{-- cidを書き換えて 0 : 新規 / 1 ～ 該当IDに更新 --}}
						<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                            <div class="input-area">
                                <div class="input-field input-field4">
                                    <label>供給地点特定番号（22文字）</label>
									<input type="text"  placeholder="例：0000000000000000000000" id="supplypoint_code"  name="supplypoint_code" class="validate" value="{{ old('supplypoint_code') ? old('supplypoint_code') : '' }}">
									<div id="supplypoint_code_err" class="error red-text" style="float: left;">{{$errors->first('supplypoint_code')}}</div>
                                </div>
                                <div class="time">
                                <div class="input-field input-field3">
                                    <label>マイページ移行元</label>
									<input type="text"  id="assignment_before_customer_code" name="assignment_before_customer_code" class="validate" placeholder="例：TS00000000" value="{{ old('assignment_before_customer_code') }}">
									<div id="assignment_before_customer_code_err" class="error red-text" style="float: left;">{{$errors->first('assignment_before_customer_code')}}</div>
                                </div>
                                <p>→</p>
                                <div class="input-field input-field3">
                                    <label>マイページ移行先</label>
									<input type="text"  id="assignment_after_customer_code" name="assignment_after_customer_code"class="validate" placeholder="例：TS00000000" value="{{ old('assignment_after_customer_code') }}">
									<div id="assignment_after_customer_code_err" class="error red-text" style="float: left;">{{$errors->first('assignment_after_customer_code')}}</div>
                                </div>
                                </div>
                            </div>

                            <div class="input-area">
                                <div class="input-field input-field4">
                                    <label>譲渡日</label>
									<input type="text"  placeholder="日付(例:2019/01/01)" id="assignment_date" name="assignment_date" class="validate no-autoinit" value="{{ old('assignment_date') ? old('assignment_date') : $forms['date'] }}">
									<div id="assignment_date_err" class="error red-text" style="float: left;">{{$errors->first('assignment_date')}}</div>
                                </div>
                                <div class="time">
                                <div class="input-field input-field3">
                                    <label>（変更前顧客）<br class="nopc">最終請求年月</label>
                                    <input type="text"  placeholder="年月(例:201901)" id="before_customer_billing_end" name="before_customer_billing_end" class="validate  no-autoinit" value="{{ old('before_customer_billing_end') ? old('before_customer_billing_end') : '' }}">
									<div id="before_customer_billing_end_err" class="error red-text" style="float: left;">{{$errors->first('before_customer_billing_end')}}</div>
                                </div>
                                <p>→</p>
                                <div class="input-field input-field3">
                                    <label>（変更後顧客）<br class="nopc">初回請求年月</label>
                                    <input type="text"  placeholder="年月(例:201902)" id="after_customer_billing_start" name="after_customer_billing_start" class="validate  no-autoinit" value="{{ old('after_customer_billing_start') ? old('after_customer_billing_start') : '' }}">
									<div id="after_customer_billing_start_err" class="error red-text" style="float: left;">{{$errors->first('after_customer_billing_start')}}</div>
                                </div>
                                </div>
                            </div>

                            <div class="input-field register">
                                <button id="btn_entry" type="submit" name="">登録/変更<img src="/img/arrow_right_black.svg"></button>
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
                            <div class="page" id="result1_pagination">
                                <a href="#"><img src="/img/chevron_upward_black.svg"></a>
                                <p>1/10</p>
                                <a href="#" class="right"><img src="/img/chevron_upward_black.svg"></a>
                            </div>
                        </div>
                        <div class="table-area jou">
                            <table id="result1_list">
                                <thead>
                                    <tr>
                                        <th>編集</th>
                                        <th>解除</th>
                                        <th>ID</th>
                                        <th>供給地点特定番号</th>
                                        <th>譲渡元マイページID</th>
                                        <th>譲渡後マイページ ID</th>
                                        <th>譲渡後顧客アドレス</th>
                                        <th>譲渡後顧客プラン</th>
                                        <th>譲渡日</th>
                                        <th>顧客への請求後</th>
                                        <th>顧客への請求前</th>
                                        <th>支払い種別</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><a href="#"><img src="/img/perm_identity_black.svg"></a></td>
                                        <td><a href="#"><img src="/img/link_black_24dp.svg"></a></td>
                                        <td>8</td>
                                        <td>0000000000000000000000</td>
                                        <td>MC00000000</td>
                                        <td>MC11111111</td>
                                        <td>sample</td>
                                        <td>sample</td>
                                        <td>2020/02/01	</td>
                                        <td>202001</td>
                                        <td>202001</td>
                                        <td>0</td>
                                    </tr>
                                    
                                </tbody>

                            </table>
                        </div>
                    </div>


        </div>



<!-- <main>
<div class="container">
  <div class="col s12">
  <!-- マイページID紐付変更 -- >
  <div class="section">マイページIDに紐付く供給地点特定番号を別のマイページIDに譲渡する。</div>
    <div class="row content header">

      <form id="submit_section" action="{{ route('change_transfer_store') }}" method="post">
        <input type="hidden" name="cid" id="cid" value="0" />{{-- cidを書き換えて 0 : 新規 / 1 ～ 該当IDに更新 --}}
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        
        <div class="row" style="margin-bottom: 0px;">
          <div class="col s12">
            <h5><i class="material-icons left">transfer_within_a_station</i><span  id="assignment_heading">新規登録</span></h5>
          </div>
        </div>

        <div class="row" style="margin-bottom: 0px;">
          <div class="input-field col s8 m8">
          <i class="material-icons prefix">place</i>
            <input type="text"  placeholder="ここにひもづけをしたい供給地点特定番号を入れてください" id="supplypoint_code"  name="supplypoint_code" class="validate" value="{{ old('supplypoint_code') ? old('supplypoint_code') : '' }}">
            <label for="supplypoint_code">供給地点特定番号</label>
            <div id="supplypoint_code_err" class="error red-text" style="float: left;">{{$errors->first('supplypoint_code')}}</div>
          </div>
                  
          <div class="input-field col s8 m5">
          <i class="material-icons prefix">account_circle</i>
            <input type="text"  id="assignment_before_customer_code" name="assignment_before_customer_code" class="validate" placeholder="移行元 マイページIDを入れてください" value="{{ old('assignment_before_customer_code') }}">
            <label for="assignment_before_customer_code">マイページID 移行元(9~10文字)</label>
            <div id="assignment_before_customer_code_err" class="error red-text" style="float: left;">{{$errors->first('assignment_before_customer_code')}}</div>
          </div>        

          <div class="input-field col s8 m5">
            <i class="material-icons prefix">arrow_right_alt</i>
            <input type="text"  id="assignment_after_customer_code" name="assignment_after_customer_code"class="validate" placeholder="移行先 マイページIDを入れてください" value="{{ old('assignment_after_customer_code') }}">
            <label for="assignment_after_customer_code">マイページID 移行先(9~10文字)</label>
            <div id="assignment_after_customer_code_err" class="error red-text" style="float: left;">{{$errors->first('assignment_after_customer_code')}}</div>
          </div>

          <div class="input-field col s7 m3">
            <i class="material-icons prefix">calendar_today</i>
            <input type="text"  placeholder="日付(例:2019/01/01)" id="assignment_date" name="assignment_date" class="validate datepicker no-autoinit"' value="{{ old('assignment_date') ? old('assignment_date') : $forms['date'] }}">
            <label for="assignment_date">譲渡日 </label>
            <div id="assignment_date_err" class="error red-text" style="float: left;">{{$errors->first('assignment_date')}}</div>
          </div>
        </div>
        
        <div class="row" style="margin-bottom: 0px;">
          <div class="input-field col s6 m4">
            <i class="material-icons prefix">keyboard_tab</i>
            <input type="text"  placeholder="年月(例:201901)" id="before_customer_billing_end" name="before_customer_billing_end" class="validate  no-autoinit"' value="{{ old('before_customer_billing_end') ? old('before_customer_billing_end') : '' }}">
            <label for="before_customer_billing_end">(変更前顧客) 最終請求年月 </label>
            <div id="before_customer_billing_end_err" class="error red-text" style="float: left;">{{$errors->first('before_customer_billing_end')}}</div>
          </div>

          <div class="input-field col s6 m4">
            <i class="material-icons prefix">subdirectory_arrow_right</i>
            <input type="text"  placeholder="年月(例:201902)" id="after_customer_billing_start" name="after_customer_billing_start" class="validate  no-autoinit"' value="{{ old('after_customer_billing_start') ? old('after_customer_billing_start') : '' }}">
            <label for="after_customer_billing_start">(変更後顧客) 初回請求年月 </label>
            <div id="after_customer_billing_start_err" class="error red-text" style="float: left;">{{$errors->first('after_customer_billing_start')}}</div>
          </div>

        </div>

        <button id="btn_entry" type="submit" class="waves-effect waves-light btn col s10 offset-s1 m6 offset-m3  btn-large orange darken-1 tooltipped" data-html="true" data-position="down" data-tooltip="譲渡変更情報を登録/変更します。">
          <i class="material-icons right">save</i><span>登録</span>
        </button>
      </form>

    </div>  
  </div>


  <div id="test3" class="col s12">
    <p>&nbsp;</p>
  </div>
    
  <!-- results_area -- >
  <div id="result1" class="row z-depth-1" style="display:none ">
    <div class="col s12 m5">
      <h5>譲渡リスト：(1件 のうち 1～1件)</h5>
    </div>

    <div class="col s12 m5" id="result1_pagination">
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

    <div class="input-field col s4 offset-s1 m2 ">
      <select id="display_number" name="display_number">
        <option value="2">2</option>
        <option value="5" selected>5</option>
        <option value="10">10</option>
      </select>
      <label>表示件数</label>
    </div>

    <div class="col s12" style="width:100%;height:100%;overflow-x:scroll;margin:0;padding:8">
        <table id="result1_list" style="white-space: nowrap;" >
          <thead>
            <tr>
              <th>編集</th>
              <th>解除</th>
              <th>ID</th>
              <th>供給地点特定番号</th>
              <th>譲渡元マイページID</th>
              <th>譲渡後マイページID</th>
              <th>譲渡後顧客アドレス</th>
              <th>譲渡後顧客プラン</th>
              <th>譲渡日</th>
              <th>顧客への請求後</th>
              <th>顧客への請求前</th>
              <th>支払い種別</th>
            </tr>
          </thead>

          <tbody>
            <tr>
              <td><i class="material-icons left">create</i></td>
              <td><i class="material-icons left">link</i></td>
              <td>1</td>
              <td>0300111001183222104031</td>
              <td>MC00000042</td>
              <td>MC00000043</td>
              <td>東京都豊島区南池袋2丁目49番地7号</td>
              <td>ファミリープランB</td>
              <td>2019/1/1</td>
              <td>201901</td>
              <td>201901</td>
              <td>1:口座振替</td>
            </tr>
          </tbody>
        </table>
    </div>
  </div>
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
<script src="{{asset('js/admin_change_transfer.js') }}"></script>
<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
