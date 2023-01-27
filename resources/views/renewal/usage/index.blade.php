{{-- 請求金額・使用量の確認画面 --}}
@extends('renewal.layout.app')

@section('title', '請求金額・使用量の確認')

@section('pageCss')
<link href="{{asset('css/renewal/common.css') }}" rel="stylesheet">
<link href="{{asset('css/renewal/usage.css') }}" rel="stylesheet">
@endsection

{{-- body_header --}}
@include('renewal.layout.bodyheader')

{{-- body_contents --}}
@section('content')

<div class="l-main">
    <h2>使用量・請求金額</h2>
    @if(session('supplypoint_code_undefined_flg'))
        <div class="section data_request">【データ取得中】反映まで今暫くおまちください。</div>
    @endif
    {{-- Select --}}
    <section class="use-area" id="pulldown">
        @include('renewal.usage.plan', [
            'serviceTypeList' => [],
            'addressList'     => [],
            'contractList'    => []
        ])
    </section>

    <div class="use-area">
        <div class="use5">
            <div class="result-ym">
                <a>ご利用期間</a>
                <div class="cp_ipselect cp_sl02" id="billing_date">
                    <select name="billing_date" class="js-plan-year">
                        <option value="">---------</option>
                        {{-- <option value="選択肢1">選択肢1</option>
                        <option value="選択肢2">選択肢2</option>
                        <option value="選択肢3">選択肢3</option> --}}
                    </select>
                </div>
            </div>

            <div class="result-user">
                <div class="use-result">
                    <div class="usettl result-name">利用者名</div>
                    <div class="result-name js-paln-user">（自動反映)</div>
                    <div class="js-paln-status"></div>
                </div>
            </div>
        </div>

        <div class="graph-area">
            <div class="graph-area-calendar">
                <button type="button" class="calendar_prev disabled js-calendar-prev"></button>
                <p class="calendar_year"><span class="js-calendar-year">----</span>年</p>
                <button type="button" class="calendar_next disabled js-calendar-next"></button>
            </div>
            <div class="graph-area-summary">
                <p class="summary_term"><span class="js-chart-date">----年--月</span></p>
                <p class="summary_report"><span>ご利用金額：</span><span class="js-chart-billing">-----</span>円</p>
                <p class="summary_report"><span>ご利用量：</span><span class="js-chart-usage">-----</span>kWh</p>
            </div>
            <div class="graph-area-switch">
                <button type="button" class="unit_tab js-chart-switch active" data-type="billing">金額で表示</button>
                <button type="button" class="unit_tab js-chart-switch" data-type="usage">使用量で表示</button>
            </div>
            <canvas id="myBarChart" style="height: 380px;"></canvas>
        </div>

        <div class="use-area" id="result1">
            <div class="border"></div>
            <h3>請求金額一覧</h3>

            <div class="table-b-area" id="billing-list">
                @include('renewal.usage.billing_list')
            </div>

            <div class="link-btn link-btn-detail">
                <p>請求一覧を一括出力</p>
                <button type="button" id="btn_download_csv" disabled>
                    保存(CSV形式)
                    <img src="/img/file_download_black.svg">
                </button>
            </div>
        </div>

        <div class="use-area use3">
            <div class="border"></div>
            <h3>利用期間を指定して一括出力</h3>
            <div class="cp_ipselect cp_sl02 short">
                <select name="original_billing_date" class="js-original_billing_year">
                    <option value="">年</option>
                </select>
            </div>
            <div class="cp_ipselect cp_sl02 short">
                <select name="original_billing_month" class="js-original_billing_month">
                    <option value="">月</option>
                    <option value="01">01月</option>
                    <option value="02">02月</option>
                    <option value="03">03月</option>
                    <option value="04">04月</option>
                    <option value="05">05月</option>
                    <option value="06">06月</option>
                    <option value="07">07月</option>
                    <option value="08">08月</option>
                    <option value="09">09月</option>
                    <option value="10">10月</option>
                    <option value="11">11月</option>
                    <option value="12">12月</option>
                </select>
            </div>

            <div class="link-btn link-btn-detail">
                <button type="button" class="js-original_csv_download" disabled>
                    保存(CSV形式)
                    <img src="/img/file_download_black.svg">
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- load js --}}
@section('pageJs')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
<script src="{{asset('js/renewal/common.js') }}"></script>
<script src="{{asset('js/renewal/usage.js') }}"></script>
@endsection

{{-- footer --}}
@section('footer')
@include('renewal.layout.footer_login')
@endsection
