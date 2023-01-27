{{-- お知らせ登録画面 --}}
@extends('layout.t_common')

@section('title','お知らせ登録')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<!--<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">-->

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 2)
@section("cate2", 8)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')
<style>
  .radio-field1 {
    padding: 30px 0;
    margin-right: 40px;
    width: 380px;
  }
  .radio-field1 label {
    margin: 10px 0;
    font-weight: bold;
    font-size: 1.4rem;
  }
  .radio-field {
    padding: 30px 0;
    width: 380px;
  }
  .radio-field label {
    margin: 10px 0;
    font-weight: bold;
    font-size: 1.4rem;
  }
  .radio {
    padding: 20px 0;
  }
</style>
        <div class="l-main">
          <h2>お知らせ登録<div class="h2-admin-border"></div></h2>

          <div class="input-field-text">
            <p id="notice_heading">新規登録</p>
          </div>

          <div class="form-area info">
              <form id="submit_section" action="{{ route('regist_notice_store') }}" method="post">
                <input type="hidden" name="cid" id="cid" value="0" />{{-- cidを書き換えて 0 : 新規 / 1 ～ 該当IDに更新 --}}
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                <div class="input-area">
                  <div class="input-field input-field1">
                    <label>日付（未来日付なら当日公開）</label>
                    <input placeholder="2021/**/**" id="notice_date" name="notice_date" type="text" class="validate no-autoinit"' value="{{ old('notice_date') ? old('notice_date') : $forms['date'] }}">
                  </div>
                  <div id="notice_date_err" class="error red-text" style="float: left;">{{$errors->first('notice_date')}}</div>

                  <div class="input-field">
                    <label>URL（任意）</label>
                    <input placeholder="https://www.example.com/"  id="url" name="url" type="text" class="validate" value="{{old('url')}}">
                  </div>
                  <div id="url_err" class="error red-text" style="float: left;">{{$errors->first('url')}}</div>
                </div>
                <div class="input-area">
                  <div class="radio-field1">
                    <label>メール送信有無</label>
                    <div class="radio">
                      <input name="send_mail" type="radio"  value="0" {{ old('send_mail') === '0' || empty(old('send_mail')) ? 'checked' : '' }}/>無し
                      <input name="send_mail" type="radio"  value="1" {{ old('send_mail') === '1' ? 'checked' : '' }}/>有り
                    </div>
                    <div id="url_err" class="error red-text" style="float: left;">{{$errors->first('send_mail')}}</div>
                    <label>※メール送信タイミング</label>
                    未来日付、当日日付（AM11:00までに登録）を指定<br>：指定日のAM11:00に送信<br>
                    当日日付（AM11:00以降の登録）、過去日付を指定<br>：即時送信<br>
                  </div>
                  <div class="radio-field">
                    <label>送信対象</label>
                    <div class="radio">
                      <input name="notice_relation" type="radio" value="0" {{ old('notice_relation') === '0'  || empty(old('notice_relation')) ? 'checked' : '' }}/>全体
                      <input name="notice_relation" type="radio" value="1" {{ old('notice_relation') === '1' ? 'checked' : '' }}/>一部
                    </div>
                    <div id="url_err" class="error red-text" style="float: left;">{{$errors->first('notice_relation')}}</div>
                  </div>
                </div>
                <div class="data-field" id="upload_section" style="display: none;">
                  <label><input id="fake_input_file" readonly type="text" value=""  onClick="$('#file').click();"></label>
                  <input type="file" id="file" style="display:none;" onchange="$('#fake_input_file').val($(this).val())"  name="btn_upload_section" accept='.csv'>
                  <button type="" name="" onClick="$('#file').click(); return false;">公開対象csv選択<img src="/img/file_upload_black_24dp.svg"></button>
                  <input type="hidden" id="file_data" name="file_data">
                  <div id="url_err" class="error red-text" style="float: left;">{{$errors->first('btn_upload_section')}}</div>
                </div>
                <div class="input-field text">
                  <label>お知らせ内容</label>
                  <textarea id="notice_comment" name="notice_comment" placeholder="お知らせ内容を入れてください">{{old('notice_comment')}}</textarea>
                </div>
                <div id="notice_comment_err" class="error red-text" style="float: left;">{{$errors->first('notice_comment')}}</div>

                <div class="input-field register">
                  <button id="btn_entry" type="submit" name="">登録する<img src="/img/arrow_right_black.svg"></button>
                </div>
              </form>
            </div>
            @php
              $res = session('res');
            @endphp
            @if(!empty($res))
            <div class="row content header">
              <div class="row" id="csv_result" style="">
                <div class="col s12">
                  <h5>
                    @if($res["status"] == 200)
                    <i class="material-icons left red-text">report_problem</i>
                    @else
                    <i class="material-icons left green-text">done</i>
                    @endif
                    取り込み結果 (TOTAL:{{$res["results"][0]}} INSERT:{{$res["results"][1]}}/UPDATE:{{$res["results"][2]}}/NG:{{$res["results"][3]}})</h5>
                </div>
                <div class="col s12" style="width:100%;height:100%;overflow-x:scroll;margin:0;padding:8">
                  <table id="csv_result_list" style="white-space: nowrap;">
                    <thead>
                      <tr>
                        <th>Line</th>
                        <th>マイページID</th>
                        <th>備考</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($res["sheet_data"] as $line)
                      <tr>
                        <td>{{$line[0]}}</td>
                        <td>{{$line[1]}}</td>
                        <td>{{$line[2]}}</td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            @endif
            <div class="input-field-text box2">
              <div class="h2-border info-border2"></div>
                <p>お知らせ一覧</p>
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
              </div>

            <div class="table-area">
              <table id="result1_list">
                <thead>
                  <tr>
                    <th>編集</th>
                    <th>削除</th>
                    <th>ID</th>
                    <th>日付</th>
                    <th>内容</th>
                    <th>URL</th>
                    <th>メールの有無</th>
                    <th>送信対象</th>
                  </tr>
                </thead>
                <tbody>
                  @for($i = 0; $i < 6; $i++)
                  <tr>
                      <td><a href="#"><img src="/img/edit_black_24dp.svg"></a></td>
                      <td><a href="#"><img src="/img/delete_black_24dp.svg"></a></td>
                      <td>**</td>
                      <td>**</td>
                      <td>テストテストテストテスト</td>
                      <td>https://www.example.com/</td>
                      <td>**</td>
                      <td>**</td>
                  </tr>
                  @endfor
                </tbody>
              </table>
            </div>

        </div>

@include('layout.t_copyright2')
@yield('copyright2')
@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="{{asset('js/admin_regist_notice.js') }}"></script>
<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
