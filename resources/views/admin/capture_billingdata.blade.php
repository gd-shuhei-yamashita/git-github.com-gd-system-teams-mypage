{{-- 請求データ取込画面 --}}
@extends('layout.t_common')

@section('title','請求データ取込')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 1)
@section("cate2", 1)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')

        <div class="l-main">
            <h2>請求データ取込<div class="h2-border"></div>
            </h2>

            <div class="input-field-text">
                <a>請求データ.csvを 請求データ(Billing)テーブルに取り込みます。（既存データは更新されます）</a>
            </div>


            <div class="form-area data">
                <form id="submit_section" action="{{ route('capture_billingdata') }}" method="post">  

                    <div class="data-field" id="upload_section">
                        <label><input id="fake_input_file" readonly type="text" value=""  onClick="$('#file').click();"></label>
						<input type="file" id="file" style="display:none;" onchange="$('#fake_input_file').val($(this).val())"  name="btn_upload_section" accept='.csv'>
                        <button type="" name="" onClick="$('#file').click();">請求データcsv選択<img src="/img/file_upload_black_24dp.svg"></button>

                    </div>

                    <div class="input-field register">
                        <button type="submit" name="">登録する<img src="/img/arrow_right_black.svg"></button>
                    </div>
                </form>
            </div>
			
			      <div class="progress" id="progress_line" style="display:none;">
        <div class="indeterminate"></div>
      </div>
      <div class="row" id="result1" style="display:none">
        <div class="col s12">
          <h5>請求データ.csv の取り込み結果</h5>
        </div>
        <div class="col s12" style="width:100%;height:100%;overflow-x:scroll;margin:0;padding:8">
          <table id="result1_list" style="white-space: nowrap;">
            <thead>
              <tr>
                <th>Line</th>
                <th>供給地点特定番号</th>
                <th>マイページID</th>
                <th>請求番号</th>
                <th>内訳コード</th>
                <th>利用開始年月日</th>
                <th>利用終了年月日</th>
                <th>請求年月</th>
                <th>請求額</th>
                <th>消費税相当額</th>
                <th>支払方法</th>
                <th>力率</th>
                <th>検針月日</th>
                <th>次回検針予定日</th>
                <th>当月指示数</th>
                <th>前月指示数</th>
                <th>計器乗率</th>
                <th>差引</th>
                <th>当月お支払い予定日</th>
                <th>利用年月</th>
                <th>備考</th>                
              </tr>
            </thead>
          
            <tbody>
              <tr>
                <td><i class="material-icons">done</i></td>
                <td>0300111001183222404031</td>
                <td>MC00000042</td>
                <td>DENKIT0000027201812</td>
                <td>DENKIT0000027201812030011100118322240403120181220181126</td>
                <td>2018/11/26</td>
                <td>2018/12/21</td>
                <td>2019/01</td>
                <td>2388</td>
                <td>176</td>
                <td>2</td>
                <td></td>
                <td>2018/12/22</td>
                <td>2019/01/25</td>
                <td>2076.4</td>
                <td>2000.1</td>
                <td></td>
                <td>76.3</td>
                <td>ご契約のクレジットカード会社に準拠</td>
                <td>2018/12</td>
                <td></td>             
              </tr>
            </tbody>
          </table>
        </div>
      </div>


        </div>

<!--<main>
<div class="container">
  <div class="col s12">
  <!-- 請求データ取込 -- >
    <div class="section">請求データ.csvを 請求データ(Billing)テーブルに取り込みます。（既存データは更新されます）</div>
    <div class="row content header">
      <form id="submit_section" action="{{ route('capture_billingdata') }}" method="post">    
        <div class="row">
          <div class="col s12">
            <div class="file-field input-field" id="upload_section">
              <div class="btn col s4 tooltipped" data-html="true" data-position="right" data-tooltip="csvデータを選択してアップロードしてください。">
                <span>csv選択<i class="material-icons right">file_upload</i></span>
                <input type="file" name="btn_upload_section" accept='.csv'/>
              </div>
              <div class="file-path-wrapper">
                <input class="file-path validate" type="text">
                <div class="col s8 error red-text" id="btn_upload_section_err"></div>
              </div>
            </div>
          </div>
        </div>
        <button id="btn_entry" type="submit" class="waves-effect waves-light btn col s4 btn-large orange darken-1 tooltipped" data-html="true" data-position="right" data-tooltip="アップロードしたデータで上書きします。">
          <i class="material-icons right">save</i>登録
        </button>
      </form>

      <div class="progress" id="progress_line" style="display:none;">
        <div class="indeterminate"></div>
      </div>
      <div class="row" id="result1" style="display:none">
        <div class="col s12">
          <h5>請求データ.csv の取り込み結果</h5>
        </div>
        <div class="col s12" style="width:100%;height:100%;overflow-x:scroll;margin:0;padding:8">
          <table id="result1_list" style="white-space: nowrap;">
            <thead>
              <tr>
                <th>Line</th>
                <th>供給地点特定番号</th>
                <th>マイページID</th>
                <th>請求番号</th>
                <th>内訳コード</th>
                <th>利用開始年月日</th>
                <th>利用終了年月日</th>
                <th>請求年月</th>
                <th>請求額</th>
                <th>消費税相当額</th>
                <th>支払方法</th>
                <th>力率</th>
                <th>検針月日</th>
                <th>次回検針予定日</th>
                <th>当月指示数</th>
                <th>前月指示数</th>
                <th>計器乗率</th>
                <th>差引</th>
                <th>当月お支払い予定日</th>
                <th>利用年月</th>
                <th>備考</th>                
              </tr>
            </thead>
          
            <tbody>
              <tr>
                <td><i class="material-icons">done</i></td>
                <td>0300111001183222404031</td>
                <td>MC00000042</td>
                <td>DENKIT0000027201812</td>
                <td>DENKIT0000027201812030011100118322240403120181220181126</td>
                <td>2018/11/26</td>
                <td>2018/12/21</td>
                <td>2019/01</td>
                <td>2388</td>
                <td>176</td>
                <td>2</td>
                <td></td>
                <td>2018/12/22</td>
                <td>2019/01/25</td>
                <td>2076.4</td>
                <td>2000.1</td>
                <td></td>
                <td>76.3</td>
                <td>ご契約のクレジットカード会社に準拠</td>
                <td>2018/12</td>
                <td></td>             
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>  
  </div>

</div>
</main>-->
@include('layout.t_copyright2')
@yield('copyright2')
@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="{{asset('js/admin_capture_billingdata.js') }}"></script>
<script src="{{asset('js/entry.js') }}"></script>
<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
