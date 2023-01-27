{{-- 契約解約申し込み確認画面 --}}
@extends('layout.t_common')

@section('title','お申し込み内容の確認')

@section('pageCss')
<link href="{{asset('css/form.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')

<div class="main">
<div class="l-main" id="contract_close">
            <h2>解約・引っ越し手続き<div class="h2-border"></div>
            </h2>

            <div class="flow">
                <h3>電気・ガスの解約の流れ</h3>
                <div class="flow_box fl">
                    <div class="bgred num1 fl">
                        <p class="num">①</p>
                        <p>解約フォームに<br class="nosp">入力・送信</p>
                    </div>
                    <div class="bgred num2 fl nosp">
                        <p class="num">②</p>
                        <div>
                            <div class="fl">
                                <img src="img/ex.png">
                                <p class="bld">以下に該当の方は、5営業日以内にカスタマーセンターより折り返しご連絡いたします。</p>
                            </div>
                            <p class="blk">
                                ・電気の解約や引っ越しの方で、入力内容に確認事項がある場合。<br>
                                ・ガスの解約や引っ越しの手続きの方。<br>
                                <span>※電気の手続きで確認事項が無い場合はそのまま③へ進みます</span>
                            </p>
                        </div>
                    </div>
                    <div class="bgred num2 nopc">
                        <p class="num">②</p>
                        <div>
                            <div class="fl">
                                <img src="img/ex.png">
                                <p class="bld red_thin">以下に該当の方は、<span>5営業日以内</span>にカスタマーセンターより折り返しご連絡いたします。</p>
                            </div>
                            <div class="kome">
                                <p class="blk">・</p>
                                <p class="blk">
                                    電気の解約や引っ越しの方で、入力内容に確認事項がある場合。
                                </p>
                            </div>
                            <div class="kome">
                                <p class="blk">・</p>
                                <p class="blk">
                                    ガスの解約や引っ越しの手続きの方。
                                </p>
                            </div>
                            <div class="kome">
                                <p class="blk"><span>※</span></p>
                                <p class="blk">
                                    <span>電気の手続きで確認事項が無い場合はそのまま③へ進みます</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bggray num3 fl">
                        <p class="num">③</p>
                        <p>解約手続き<br class="nosp">
                            受付完了</p>
                    </div>
                </div>
            </div>

            <div class="caution_red">
                <p class="txt_red">最終ご請求月は、<br class="nopc">解約月の最大2ヶ月後になります。</p>
                <p>他社サービスをお申し込みいただくと自動的に解約されます。</p>
                <div class="kome">
                    <p>※</p>
                    <p>引っ越し先でもご利用中のサービスを継続する場合は、お引っ越し（移転）のお手続きをさせていただきます。</p>
                </div>
                <div class="kome">
                    <p>※</p>
                    <p>最終ご利用日が本日より5営業日以内をご希望の場合、カスタマーセンターへご連絡の上ご解約手続きをお願いいたします。</p>
                </div>
            </div>

            <div class="center">
                <h3>ガスのみ・電気＋ガス<br class="nopc">のご解約・引っ越しの方</h3>
                <p class="txt_red red_thin">
                    ガスの解約には原則立ち合いが必要となります。<br>
                    お立ち合いの日程を調整させていただくため、<span>本手続きのみでは完了いたしません</span>のでご注意ください。<br>
                    本手続き完了後、カスタマーセンターより<span>5営業日以内</span>に折り返しご連絡いたします。
                </p>
                <p>
                    ※ガスの解約にはご契約者様又は代理人様にて現地のお立ち合いと日程等を確認させていただきます。
                </p>
            </div>

            <div class="caution">
                <h3 id="caution">注意事項</h3>
                <p>・解約、引っ越し申し込み完了後、ご入力のE-mail宛に解約手続き受付完了の通知が自動送信されます。</p>
                <p>・ご入力のE-mailに誤りがありましたら自動送信メールが送信されません。お間違いのないようお願いいたします。</p>
            </div>

            <div class="form_area">
                <h3>解約・引っ越しフォーム</h3>
            </div>

            <form action="{{ route('contract_close_thanks') }}" method="post" id="mail_form" name="Form1" class="confilm">
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                <span class="p-country-name" style="display:none;">Japan</span>
                <dl>
                    <dt><span class="txt_red">必須</span>解約するサービス</dt>
                    <dd class="required">
                        <input name="service" type="hidden" value="{{ $data['service'] }}" />
                        <label>
                            @if($data['service'] == 'electric')
                            電気のみ
                            @elseif($data['service'] == 'gas')
                            ガスのみ
                            @elseif($data['service'] == 'electric_gas')
                            電気＋ガス
                            @endif
                        </label>
                    </dd>

                    <dt><span class="txt_red">必須</span>解約理由</dt>
                    <dd class="required">
                        <input name="reason" type="hidden" value="{{ $data['reason'] }}" />
                        <label>
                            @if($data['reason'] == 'moving')
                            引っ越し
                            @elseif($data['reason'] == 'price')
                            料金
                            @elseif($data['reason'] == 'customer')
                            カスタマの対応に不満
                            @elseif($data['reason'] == 'mypage')
                            マイページに不満
                            @elseif($data['reason'] == 'solicit')
                            他社の勧誘
                            @elseif($data['reason'] == 'other')
                            その他
                            @endif
                        </label>
                    </dd>

                    <dt id="hidden1"><span class="txt_red">必須</span>引っ越し先での利用</dt>
                    <dd id="hidden2">
                        <input name="moving" type="hidden" value="{{ $data['moving'] }}" />
                        <label>
                            @if($data['moving'] == '1')
                            引っ越し先でも使用する
                            @elseif($data['moving'] == '0')
                            引っ越し先では使用しない
                            @endif
                        </label>
                    </dd>
                    @if(!empty($data['moving']) && $data['moving'] == '1')
                    <dt id="hidden3"><span class="txt_red">必須</span>引っ越し先の情報</dt>
                    <dd id="hidden4" class="pear">
                        <dl>
                            <dt class="space_none">※引っ越し先での<br>利用開始日</dt>
                            <dd class="short">
                                <input name="start_year" type="hidden" value="{{ $data['start_year'] }}" />
                                <input name="start_month" type="hidden" value="{{ $data['start_month'] }}" />
                                <input name="start_day" type="hidden" value="{{ $data['start_day'] }}" />
                                {{ $data['start_year'] }}年{{ $data['start_month'] }}月{{ $data['start_day'] }}日
                            </dd>
                            <dt class="space_none">※引っ越し先住所</dt>
                            <dd class="wide">
                                <input type="hidden" name="new_postal"　value="{{ $data['new_postal'] }}" />
                                <input type="hidden" name="new_add" value="{{ $data['new_add'] }}" />
                                <input type="hidden" name="new_build" value="{{ $data['new_build'] }}" />
                                〒{{ $data['new_postal'] }}<br>
                                {{ $data['new_add'] }}<br>
                                {{ $data['new_build'] }}
                            </dd>
                        </dl>
                    </dd>
                    @endif

                    <dt>契約プラン名</dt>
                    <dd class="">
                        <input name="plan_name" type="hidden" value="{{ $data['plan_name'] }}" />
                        {{ $data['plan_name'] }}
                    </dd>
                    <dt>ご契約中の住所</dt>
                    <dd class="">
                        <input name="add" type="hidden" value="{{ $data['add'] }}" />
                        {{ $data['add'] }}
                    </dd>
                    @if($data['service'] == 'electric' || $data['service'] == 'electric_gas')
                    <dt id="hidden9"><span class="txt_red">必須</span>電気の最終利用日</dt>
                    <dd class="required" id="hidden10">
                        <input name="electric_last_year" type="hidden" value="{{ $data['electric_last_year'] }}" />
                        <input name="electric_last_month" type="hidden" value="{{ $data['electric_last_month'] }}" />
                        <input name="electric_last_day" type="hidden" value="{{ $data['electric_last_day'] }}" />
                        {{ $data['electric_last_year'] }}年{{ $data['electric_last_month'] }}月{{ $data['electric_last_day'] }}日
                        <p class="note">※本日から5営業日以降の日程を選択してください。</p>
                    </dd>
                    @endif
                    @if($data['service'] == 'gas' || $data['service'] == 'electric_gas')
                    <dt id="hidden11"><span class="txt_red">必須</span>ガスの最終利用日</dt>
                    <dd class="required short" id="hidden12">
                        <input name="gas_last_year" type="hidden" value="{{ $data['gas_last_year'] }}" />
                        <input name="gas_last_month" type="hidden" value="{{ $data['gas_last_month'] }}" />
                        <input name="gas_last_day" type="hidden" value="{{ $data['gas_last_day'] }}" />
                        {{ $data['gas_last_year'] }}年{{ $data['gas_last_month'] }}月{{ $data['gas_last_day'] }}日
                        <p class="note">※本日から10営業日以降の日程を選択してください。</p>
                    </dd>
                    @endif

                    <dt><span class="txt_red">必須</span>メータの解体立ち合いの有無</dt>
                    <dd class="required">
                        <input name="meter" type="hidden" value="{{ $data['meter'] }}" />
                        <label>
                            @if($data['meter'] == '1')
                            有り
                            @elseif($data['meter'] == '0')
                            無し
                            @endif
                        </label>
                        <p class="note">※メーター等の撤去、ガスの解約の場合のみ有りを選択してください。</p>

                    </dd>

                    <dt>供給地点番号</dt>
                    <dd class="">
                        <input type="hidden" name="supplypoint_code" value="{{ $data['supplypoint_code'] }}">
                        {{ $data['supplypoint_code'] }}
                    </dd>
                    <dt>お客様番号</dt>
                    <dd class="">
                        <input type="hidden" name="customer_num" value="{{ $data['customer_num'] }}">
                        {{ $data['customer_num'] }}
                    </dd>
                    <dt>契約名義</dt>
                    <dd class="">
                        <input type="hidden" name="name" value="{{ $data['name'] }}">
                        {{ $data['name'] }}
                    </dd>
                    <dt>ご連絡先電話番号</dt>
                    <dd class="">
                        <input type="hidden" name="phone" value="{{ $data['phone'] }}">
                        {{ $data['phone'] }}
                    </dd>

                    <dt id="hidden5">
                        ・解約後の住所変更<br>
                        ・引っ越しによる住所変更
                        <br><br>
                        などによるご契約時と異なる住所への請求書送付を希望される方のみ入力
                    </dt>
                    <dd class="wide" id="hidden6">
                        <input type="hidden" name="postal_send" value="{{ $data['postal_send'] }}" />
                        <input type="hidden" name="add_send" value="{{ $data['add_send'] }}" />
                        <input type="hidden" name="build_send" value="{{ $data['build_send'] }}" />
                        〒{{ $data['postal_send'] }}<br>
                        {{ $data['add_send'] }}<br>
                        {{ $data['build_send'] }}
                    </dd>

                    <dt class="bganother" id="hidden7">手続き後のご連絡先<br>
                        <p class="note">※ご契約電話番号と異なる場合のみ入力</p>
                    </dt>
                    <dd class="bganother" id="hidden8">
                        <input type="hidden" name="tel" value="{{ $data['tel'] }}" />
                        {{ $data['tel'] }}
                    </dd>

                    <dt class="bganother line_btm line_btm_none"><span class="txt_red">必須</span>申し込み完了通知を<br>受信するE-mail</dt>
                    <dd class="required bganother line_btm">
                        <input type="hidden" name="mail" value="{{ $data['mail'] }}" />
                        {{ $data['mail'] }}
                        <p class="note">※メールの受信設定をお願いいたします。</p>
                    </dd>

                    <div class="caution">
                        <p>
                        <span>▼補足事項</span><br>
                        最終ご利用日の指定日によって、最終請求月、請求金額の確定日などのずれ生じる場合がございます。<br>
                        最終ご利用日の指定日によって、最終のお支払い方法がコンビニ請求になる場合がございます。<br>
                        </p>
                        <p class="commission">
                        <span>▼解約事務手数料について</span><br>
                        当社より本サービスをご契約をいただいた場合、原則解約事務手数料は発生いたしません。<br>
                        ※「NEXTでんきベーシックプラン」をご利用中のお客様もしくは電力供給元がHTBエナジー社のプランをご利用中のお客様が更新月以外での解約をされる場合は、最終請求に解約事務手数料（3,850円）を合算させていただきます。
                        </p>
                    </div>

                    <p class="info">
                        当社の個人情報の取り扱いについては、<a class="txt_red" href="https://grandata.jp/privacy/"
                        target="_blank">プライバシーポリシー</a>の定めのとおりとします。<br>
                        プライバシーポリシーの内容をご確認いただき、ご同意いただける場合は「プライバシーポリシーに同意する」にチェックを付け、
                        入力内容をご確認のうえ、次の画面へ進んでください。
                    </p>

                    <div class="caution center">
                        <input  name="purapori" type="checkbox" value="1" checked/>
                        <span class="check_label">プライバシーポリシーに同意する</span></label>
                    </div>

                    <p id="form_submit">
                        <input type="button" value="戻る" onclick="history.back(-1)">
                        <button type="submit" name="submit">送信する</button>
                    </p>
            </form>


        </div>
        @include('layout.t_copyright2')
        @yield('copyright2')
    </div>
</div>
@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>

<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
