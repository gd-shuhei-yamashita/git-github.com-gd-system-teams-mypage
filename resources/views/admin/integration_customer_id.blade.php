{{-- マイページID統合画面 --}}
@extends('layout.t_common')

@section('title','マイページID統合')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<!--<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">-->
@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 2)
@section("cate2", 3)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')

        <div class="l-main">
            <h2>マイページID 統合<div class="h2-admin-border"></div>
            </h2>

            <div class="input-field-text">
                <a>
                    マイページIDを統合する、統合を解除する設定画面です。<br>
親と子の関係でひもづけをしたい、譲渡先、譲渡元のマイページIDを入れてください。
                </a>
            </div>
            <div class="mds">
            <p>新規登録</p>
            </div>

                    <div class="form-area info">
                        <form id="submit_section" action="{{route('integration_customer_id_store')}}" method="post">
						<input type="hidden" name="cid" id="cid" value="0" />{{-- cidを書き換えて 0 : 新規 / 1 ～ 該当IDに更新 --}}
						<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                            <div class="input-area">
                                <div class="input-field input-field1">
                                    <label>(親)マイページID</label>
                                    <input placeholder="例:MC00000000" id="parent_customer_code" name="parent_customer_code" type="text" class="validate" value="{{old('parent_customer_code')}}">
									<span class="help-block red-text">{{$errors->first('parent_customer_code')}}</span>
                                </div>
                                <div class="input-field">
                                    <label>(子)マイページID</label>
                                    <input placeholder="例:MC00000000" id="child_customer_code" name="child_customer_code"  type="text" class="validate" value="{{old('child_customer_code')}}">
									<span class="help-block red-text">{{$errors->first('child_customer_code')}}</span>
                                </div>
                            </div>
                            <div class="input-field text">
                                <label>変更詳細</label>
                                <textarea id="change_result" name="change_result" placeholder="例) 2019/1/1に●●様より統合申し込みがありました。" class="materialize-textarea">{{old('change_result')}}</textarea>
								<div id="change_result_err" class="error red-text" style="float: left;">{{$errors->first('change_result')}}</div>
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
                                        <th>編集</th>
                                        <th>解除</th>
                                        <th>ID</th>
                                        <th>親マイページID</th>
                                        <th>子マイページID</th>
                                        <th>変更詳細</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><a href="#"><img src="/img/perm_identity_black.svg"></a></td>
                                        <td><a href="#"><img src="/img/link_black_24dp.svg"></a></td>
                                        <td>8</td>
                                        <td>MC00000000</td>
                                        <td>MC11111111</td>
                                        <td>重複エントリーのため。管理画面で統合。</td>
                                    </tr>
                                    
                                </tbody>

                            </table>
                        </div>
                    </div>


        </div>




<!-- <main>
<div class="container">
  <div class="col s12">
  <!-- マイページID統合 -- >
    <div class="section">マイページIDを統合する、統合を解除する設定画面です。<br/>
    親と子の関係でひもづけをしたい、譲渡先、譲渡元のマイページIDを入れてください。</div>
    <div class="row content header">
      <form id="submit_section" action="{{route('integration_customer_id_store')}}" method="post">
        <input type="hidden" name="cid" id="cid" value="0" />{{-- cidを書き換えて 0 : 新規 / 1 ～ 該当IDに更新 --}}
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        
        <div class="row" style="margin-bottom: 0px;">
          <div class="col s12">
            <h5><i class="material-icons left">call_merge</i><span  id="parent_child_heading">新規登録</span></h5>
          </div>
        </div>

        <div class="row">
          <div class="input-field col  s12 m5">
            <i class="material-icons prefix">account_circle</i>
            <input placeholder="例:MC00000000" id="parent_customer_code" name="parent_customer_code" type="text" class="validate" value="{{old('parent_customer_code')}}">
            <label for="parent_customer_code">(親)マイページID</label>
            <span class="help-block red-text">{{$errors->first('parent_customer_code')}}</span>
          </div>

          <div class="input-field col  s12 m5">
            <i class="material-icons prefix">account_circle</i>
            <input placeholder="例:MC00000000" id="child_customer_code" name="child_customer_code"  type="text" class="validate" value="{{old('child_customer_code')}}">
            <label for="child_customer_code">(子)マイページID</label>
            <span class="help-block red-text">{{$errors->first('child_customer_code')}}</span>
          </div>
          
          <div class="input-field col s12 m10">
            <i class="material-icons prefix">notes</i>
            <textarea id="change_result" name="change_result" placeholder="例) 2019/1/1に●●様より統合申し込みがありました。" class="materialize-textarea">
{{old('change_result')}}</textarea>
            <label for="change_result">変更詳細(複数行入力可)</label>
            <div id="change_result_err" class="error red-text" style="float: left;">{{$errors->first('change_result')}}</div>
          </div>

        </div>
        <button id="btn_entry" type="submit" class="waves-effect waves-light btn col s10 offset-s1 m6 offset-m3  btn-large orange darken-1 tooltipped" data-html="true" data-position="down" data-tooltip="お知らせを登録/変更します。">
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
      <h5>顧客ID統合リスト：(1件 のうち 1～1件)</h5>
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
              <th>親マイページID</th>
              <th>子マイページID</th>
              <th>変更詳細</th>
              <th>譲渡日</th>
            </tr>
          </thead>

          <tbody>
            <tr>
              <td><i class="material-icons left">create</i></td>
              <td><i class="material-icons left">link</i></td>
              <td>4</td>
              <td>MC00000042</td>
              <td>MC00000043</td>
              <td>2019/1/1に統合申し込みがありました。</td>
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
<script src="{{asset('js/admin_integration_customer_id.js') }}"></script>
<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
