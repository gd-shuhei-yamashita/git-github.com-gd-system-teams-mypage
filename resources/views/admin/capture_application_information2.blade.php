{{-- 申込情報取込(契約データ)画面 --}}
@extends('layout.t_common')

@section('title','申込情報取込(契約データ)')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 1)
@section("cate2", 4)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')

        <div class="l-main">
            <h2>申込情報取込（契約）<div class="h2-border"></div>
            </h2>

            <div class="input-field-text">
                <a>契約データ.csvを 契約データ(Contract)テーブルに取り込みます（既存データは更新されます）</a>
            </div>


            <div class="form-area data">
                <form id="submit_section" action="{{ route('capture_application_information2') }}" method="post">

                    <div class="data-field" id="upload_section">
                        <label><input id="fake_input_file" readonly type="text" value=""  onClick="$('#file').click();"></label>
						<input type="file" id="file" style="display:none;" onchange="$('#fake_input_file').val($(this).val())"  name="btn_upload_section" accept='.csv'>
                        <button type="" name="" onClick="$('#file').click();">契約データcsv選択<img src="/img/file_upload_black_24dp.svg"></button>

                    </div>

                    <div class="input-field register">
                        <button type="submit" name="" >登録する<img src="/img/arrow_right_black.svg"></button>
                    </div>
                </form>
            </div>

        </div>
      <div class="progress" id="progress_line" style="display:none;">
        <div class="indeterminate"></div>
      </div>
      <div class="row" id="result1" style="display:none">
        <div class="col s12">
          <h5>契約データ.csv 取り込み結果</h5>
        </div>
        <div class="col s12" style="width:100%;height:100%;overflow-x:scroll;margin:0;padding:8">
          <table id="result1_list" style="white-space: nowrap;">
            <thead>
              <tr>
                <th>Line</th>
                <th>マイページID</th>
                <th>供給地点特定番号</th>
                <th>契約名義</th>
                <th>住所</th>
                <th>プラン名</th>
                <th>店舗名</th>
                <th>備考</th>
              </tr>
            </thead>

            <tbody>
              <tr>
                <td><i class="material-icons">done</i></td>
                <td>MC00000042</td>
                <td>0300111001183222404031</td>
                <td>芝谷佳祐</td>
                <td>東京都中野区東中野２丁目２２－２３トゥールビヨン４０３</td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            </tbody>
          </table>
        </div>
        
      </div>




<!-- <main>
<div class="container">
  <div class="col s12">
  <!-- 申込情報取込 -- >
    <div class="section">契約データ.csvを 契約データ(Contract)テーブルに取り込みます（既存データは更新されます）</div>
    <div class="row content header">
      <form id="submit_section" action="{{ route('capture_application_information2') }}" method="post">    
        <div class="row">

          <div class="col s12">
            <div class="file-field input-field" id="upload_section">
              <div class="btn col s4 tooltipped" data-html="true" data-position="right" data-tooltip="csvデータを選択してアップロードしてください。。">
                <span>契約データcsv選択<i class="material-icons right">file_upload</i></span>
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

      


    </div>  
  </div>

</div>
</main>-->
@include('layout.t_copyright2')
@yield('copyright2')
@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="{{asset('js/admin_capture_application_inforrmation2.js') }}"></script>
<script src="{{asset('js/entry.js') }}"></script>
<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
