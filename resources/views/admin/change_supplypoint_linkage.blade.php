{{-- 供給地点特定番号紐付変更画面 --}}
@extends('layout.t_common')

@section('title','供給地点特定番号紐付変更')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<!--<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">-->

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 2)
@section("cate2", 1)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')

        <div class="l-main">
            <h2>供特紐付変更<div class="h2-admin-border"></div>
            </h2>

            <div class="input-field-text">
                <a>
                    請求データ(Billing)、使用率(UsageT)テーブルの、供給地点特定番号を一斉に書き換えます。<br class="spno">
更新履歴を画面で確認可能です。管理画面より直接もとに戻せる手順ではないため、特に作業にご注意ください。<br class="spno">
一時作業領域として、供給地点特定番号に 9999999999999999999999 が利用可能です。
                </a>
            </div>
            <div class="mds">
            <p>新規登録</p>
            </div>

                    <div class="form-area info">
                        <form id="submit_section" action="{{ route('change_supplypoint_linkage_store') }}" method="post">
						<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                            <div class="input-area">
                                <div class="input-field input-field1">
                                    <label>(旧)供給地点特定番号(22文字)</label>
                                    <input name="now_supplypoint_code" id="now_supplypoint_code" type="text" class="validate" value="{{ old('now_supplypoint_code') }}" placeholder=" 例:0000000000000000000000">
									<span class="help-block red-text">{{$errors->first('now_supplypoint_code')}}</span>
                                </div>
                                <div class="input-field">
                                    <label>(新)供給地点特定番号(22文字)</label>
                                    <input name="new_supplypoint_code"  id="new_supplypoint_code" type="text" class="validate" value="{{ old('new_supplypoint_code') }}" placeholder=" 例:0000000000000000000000">
									<span class="help-block red-text">{{$errors->first('new_supplypoint_code')}}</span>
                                </div>
                            </div>

                            <div class="input-field register">
                                <button id="btn_entry" type="submit" name="">登録する<img src="/img/arrow_right_black.svg"></button>
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
                                        <th>ID</th>
                                        <th>旧番号</th>
                                        <th>新番号</th>
                                        <th>契約差分</th>
                                        <th>内訳差分</th>
                                        <th>使用率差分</th>
                                        <th>実施日</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>8</td>
                                        <td>MC00000000</td>
                                        <td>MC11111111</td>
                                        <td>1234567891234567891234</td>
                                        <td>0000000000000000000000000000000000000000<br>
                                            0000000000000000000000000000000000000000</td>
                                        <td>(MC00050434,202012)</td>
                                        <td>2021/09/09</td>
                                    </tr>
                                    
                                </tbody>

                            </table>
                        </div>
                    </div>


        </div>




<!--<main>
<div class="container">
  <div class="col s12">
  <!-- 供給地点特定番号紐付変更 -- >
    <div class="section">請求データ(Billing)、使用率(UsageT)テーブルの、供給地点特定番号を一斉に書き換えます<br/>
    更新履歴を画面で確認可能です。管理画面より直接もとに戻せる手順ではないため、特に作業にご注意ください。<br/>
    一時作業領域として、供給地点特定番号に 9999999999999999999999 が利用可能です。<br/>
    </div>
    <div class="row content header">
      <form id="submit_section" action="{{ route('change_supplypoint_linkage_store') }}" method="post">
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        <div class="row">
          <div class="input-field col s8 m8">
          <i class="material-icons prefix">place</i>
            <input name="now_supplypoint_code" id="now_supplypoint_code" type="text" class="validate" value="{{ old('now_supplypoint_code') }}" placeholder="供給地点特定番号 (22文字 例:0000000000000000000000)">
            <label for="now_supplypoint_code">供給地点特定番号(旧)</label>
            <span class="help-block red-text">{{$errors->first('now_supplypoint_code')}}</span>
          </div>
        </div>
        <div class="row">
          <div class="input-field col s8 m8">
          <i class="material-icons prefix">place</i>
            <input name="new_supplypoint_code"  id="new_supplypoint_code" type="text" class="validate" value="{{ old('new_supplypoint_code') }}" placeholder="供給地点特定番号 (22文字 例:0000000000000000000000)">
            <label for="new_supplypoint_code">供給地点特定番号(新)</label>
            <span class="help-block red-text">{{$errors->first('new_supplypoint_code')}}</span>
          </div>
        </div>
        <button id="btn_entry" type="submit" class="waves-effect waves-light btn col s4 btn-large orange darken-1 tooltipped" data-html="true" data-position="right" data-tooltip="アップロードしたデータで上書きします。">
          <i class="material-icons right">save</i>登録
        </button>
      </form>

    </div>  
  </div>

  <!-- results_area -- >
  <div id="result1" class="row z-depth-1" style="display:none ">
    <div class="col s12 m5">
      <h5>履歴：(1件 のうち 1～1件)</h5>
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
              <th>ID</th>
              <th>旧番号</th>
              <th>新番号</th>
              <th>契約差分</th>
              <th>内訳差分</th>
              <th>使用率差分</th>
              <th>実施日</th>
            </tr>
          </thead>

          <tbody>
            <tr>
              <td>1</td>
              <td>0300111001183222104031</td>
              <td>0300111001183222104031</td>
              <td>aaa<br/>aaaaa<br/>aaaaa</td>
              <td>bbb<br/>bbbbb<br/>bbbbb</td>
              <td>ccc<br/>ccccc<br/>ccccc</td>
              <td>2019/1/1</td>
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
<script src="{{asset('js/admin_change_supplypoint.js') }}"></script>
<script src="{{asset('js/entry.js') }}"></script>
<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
