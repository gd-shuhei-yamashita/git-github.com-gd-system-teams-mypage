{{-- 請求金額・使用量の確認画面 --}}
@extends('layout.t_common')

@section('title','請求金額・使用量の確認')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@section("cate1", 3)
@section("cate2", 0)
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')

<div class="l-main" name="#">
    @if (session('user_login.role') < 5)
    <div class="section red-text">【注意】管理者権限でユーザー画面を閲覧しております</div>
    @endif
    <h2>使用量・請求金額
        <div class=" h2-border"></div>
    </h2>
    @if(session('supplypoint_code_undefined_flg'))
        <div class="section data_request">【データ取得中】反映まで今暫くおまちください。</div>
    @endif
    <div class="use-area">
        <div class="use-plan">
            <div class="use1">
                <a>ご利用サービスを選択</a>
                <div class="cp_ipselect cp_sl02" id="service">
                    <select name="service" class="">
                        <option value="">---------</option>
                        <option value="電気">電気</option>
                        <option value="ガス">ガス</option>
                        <option value="wifi">WiMAX</option>
                        <option value="オプション">オプション</option>
                    </select>
                </div>
            </div>
            <div class="use4">
                <a>住所を選択</a>
                <div class="cp_ipselect cp_sl02" id="address">
                    <select name="address" class="">
                        <option value="">---------</option>
                        <option value="選択肢1">選択肢1</option>
                        <option value="選択肢2">選択肢2</option>
                        <option value="選択肢3">選択肢3</option>
                    </select>
                </div>
            </div>
            <div class="use4">
                <a>ご利用プランを選択</a>
                <div class="cp_ipselect cp_sl02" id="supplypoint_code">
                    <select name="supplypoint_code" class="">
                        <option value="">---------</option>
                        <option value="選択肢1">選択肢1</option>
                        <option value="選択肢2">選択肢2</option>
                        <option value="選択肢3">選択肢3</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="plan-btn">
            <button type="button" id="graph_display">表示する</button>
        </div>
    </div>

    <div class="use-area">
        <div class="use5">
            <div class="result-ym">
                <a>ご利用期間</a>
                <div class="cp_ipselect cp_sl02" id="billing_date">
                    <select name="billing_date" class="">
                        <option value="">---------</option>
                        <option value="選択肢1">選択肢1</option>
                        <option value="選択肢2">選択肢2</option>
                        <option value="選択肢3">選択肢3</option>
                    </select>
                </div>
            </div>

            <div class="result-user">
                <div class="use-result">
                    <div class="usettl result-name">利用者名</div>
                    <div class="result-name" id="get_contract_name">（自動反映)</div>
                    <div class="" id="plan_status"></div>
                </div>
            </div>
        </div>

        <div class="graph-area">
            <div class="graph_status">
                <div class="tab">
                    <input id="time" type="radio" name="tab_graph">
                    <label class="tab_graph" for="time">時間</label>
                    <input id="day" type="radio" name="tab_graph">
                    <label class="tab_graph" for="day">日</label>
                    <input id="month" type="radio" name="tab_graph" checked="checked">
                    <label class="tab_graph" for="month">月</label>
                </div>
            </div>
            <div class="calendar-button">
                <button>＜</button>
                <p>2022年</p>
                <button>＞</button>
            </div>
            <div class="summary">
                <p class="term">2022年5月</p>
                <p class="report"><span>ご利用金額:</span>12,000円</p>
                <p class="report"><span>利用量：</span>500kwh</p>
            </div>

            <div class="unit_status">
                <div class="tab">
                    <input id="price" type="radio" name="tab_unit" checked="checked">
                    <label class="tab_unit" for="price">金額で表示</label>
                    <input id="to_use" type="radio" name="tab_unit">
                    <label class="tab_unit" for="to_use">使用量で表示</label>
                </div>
            </div>
            <canvas id="myBarChart" style="height: 380px;"></canvas>
        </div>

        <div class="use-area use2"  id="result1">
            <div class="h2-border use-border2"></div>
            <p>請求金額一覧</p>

            <div class="table-area">
                <table id="result1_list">
                    <thead>
                        <tr>
                            <th>請求年月</th>
                            <th>合計金額</th>
                            <th>利用期間</th>
                            <th>詳細</th>
                        </tr>
                    </thead>
                    <tbody>
                    @for ($i = 0; $i < 5; $i++)
                        <tr>
                            <td class="date">----年-月分</td>
                            <td class="en">--,---円</td>
                            <td class="date">-月-日～-月-日</td>
                            <td><button type="button" onclick="location.href='detail.html'" disabled>確認</button></td>
                        </tr>
                    @endfor
                    </tbody>
                </table>
            </div>

            <div class="link-btn link-btn-detail">
                <a>請求一覧を一括出力</a>
                <button  id="btn_download_csv">保存(CSV形式)
                    <img src="img/file_download_black.svg">
                </button>
            </div>
        </div>

        <div class="use-area use3">
            <div class="h2-border use-border3"></div>
            <p>利用期間を指定して一括出力</p>
            <div class="cp_ipselect cp_sl02 short" id="original_billing_date">
                <select name="original_billing_date"  class="">
                    <option value="">年</option>
                    <option value="選択肢1">選択肢1</option>
                    <option value="選択肢2">選択肢2</option>
                    <option value="選択肢3">選択肢3</option>
                </select>
            </div>
            <div class="cp_ipselect cp_sl02 short" id="original_billing_month">
                <select name="original_billing_month"  class="">
                    <option value="">月</option>
                    <option value="選択肢1">選択肢1</option>
                    <option value="選択肢2">選択肢2</option>
                    <option value="選択肢3">選択肢3</option>
                </select>
            </div>

            <div class="link-btn link-btn-detail">
                <button  id="btn_original_download_csv" type="button" >保存(CSV形式)
                    <img src="img/file_download_black.svg">
                </button>
            </div>
        </div>
    </div>

@include('layout.t_copyright2')
@yield('copyright2')
</div>





@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>

<script src="{{asset('js/confirm_usagedata.js') }}"></script>
<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
