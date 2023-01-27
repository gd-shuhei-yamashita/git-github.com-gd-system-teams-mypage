{{-- 契約解約申し込み画面 --}}
@extends('layout.t_common')

@section('title','解約・引っ越しの手続き')

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
    <h2>解約・引っ越しの手続き<div class="h2-border"></div></h2>
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
        <h3>※以下ご確認ください</h3>
          <h4>ガスの契約がある方<br class="nopc">&emsp;もしくは&emsp;<br class="nopc">引っ越し手続き希望の方</h4>
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
      <form action="{{ route('contract_close_confirm') }}" method="post" id="mail_form" name="Form1">
        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
        <span class="p-country-name" style="display:none;">Japan</span>
        <dl>
          <dt><span class="txt_red">必須</span>解約するサービス</dt>
          <dd class="required">
            <div id="error_service">
              <label><input name="service" type="radio" id="service_electric" value="electric" {{ old('service') === 'electric' ? 'checked' : '' }}/>
                <span class="radio_label">電気のみ</span>
              </label>
              <label><input name="service" type="radio" id="service_gas" value="gas" {{ old('service') === 'gas' ? 'checked' : '' }}/>
                <span class="radio_label">ガスのみ</span>
              </label>
              <label><input name="service" type="radio" id="service_electric_gas" value="electric_gas" {{ old('service') === 'electric_gas' ? 'checked' : '' }}/>
                <span class="radio_label">電気＋ガス</span>
              </label>
            </div>
            @if($errors->has('service'))
            <label id="service-error" class="error" for="service">{{$errors->first('service')}}</label>
            @endif
          </dd>

          <dt><span class="txt_red">必須</span>解約理由</dt>
          <dd>
            <div id="error_reason">
              <label class="reason_radio"> <input name="reason" type="radio" id="reason_moving" value="moving" {{ old('reason') === 'moving' ? 'checked' : '' }}/>
                <span class="radio_label">引っ越し</span>
              </label>
              <label class="reason_radio"> <input name="reason" type="radio" id="reason_price" value="price" {{ old('reason') === 'price' ? 'checked' : '' }}/>
                <span class="radio_label">料金</span>
              </label>
              <label class="reason_radio"> <input name="reason" type="radio" id="reason_customer" value="customer" {{ old('reason') === 'customer' ? 'checked' : '' }}/>
                <span class="radio_label">カスタマの対応に不満</span>
              </label>
              <label class="reason_radio"> <input name="reason" type="radio" id="reason_mypage" value="mypage" {{ old('reason') === 'mypage' ? 'checked' : '' }}/>
                <span class="radio_label">マイページに不満</span>
              </label>
              <label class="reason_radio"> <input name="reason" type="radio" id="reason_solicit" value="solicit" {{ old('reason') === 'solicit' ? 'checked' : '' }}/>
                <span class="radio_label">他社の勧誘</span>
              </label>
              <label class="reason_radio"> <input name="reason" type="radio" id="reason_other" value="other" {{ old('reason') === 'other' ? 'checked' : '' }}/>
                  <span class="radio_label">その他</span>
                </label>
            </div>
            @if($errors->has('reason'))
            <label id="meter-error" class="error" for="reason">{{$errors->first('reason')}}</label>
            @endif
          </dd>

          <dt><span class="txt_red">必須</span>メーターの撤去の有無
            <div class="balloon_base" onclick="showBalloon()">
              <i class="fa-regular fa-circle-question"></i>
              <p class="balloon1" id="makeImg">
                <span class="title">※メーターの撤去例</span>
                <span class="txt">
                  ・自宅の建て壊し<br>
                  ・マンションオーナーによる部分的な解体　など
                </span>
                <span class="close">×　閉じる</span>
              </p>
            </div>
          </dt>
          <dd class="required">
            <div id="error_meter">
              <label for="meter_yes">
                <input name="meter" type="radio" id="meter_yes" value="1" {{ old('meter') === '1' ? 'checked' : '' }}/>
                <span class="radio_label">有り</span></label>
                <label for="meter_no">
                <input name="meter" type="radio" id="meter_no" value="0" {{ old('meter') === '0' || empty(old('meter')) ? 'checked' : '' }}/>
                <span class="radio_label">無し</span>
              </label>
            </div>
            @if($errors->has('meter'))
            <label id="meter-error" class="error" for="meter">{{$errors->first('meter')}}</label>
            @endif
            <p class="note">※メーター等の撤去、ガスの解約の場合のみ有りを選択してください。</p>
            <p class="note" id="note_meter_yes"></p>
          </dd>

          <dt><span class="txt_red">必須</span>引っ越し先での利用</dt>
          <dd>
            <div id="error_moving">
              <label>
                <input name="moving" type="radio" id="moving" value="1" {{ old('moving') === '1' || empty(old('moving')) ? 'checked' : '' }} />
                <span class="radio_label">引っ越し先でも使用する</span>
              </label>
              <label>
                <input name="moving" type="radio" id="not_moving" value="0" {{ old('moving') === '0' ? 'checked' : '' }} />
                <span class="radio_label">引っ越し先では使用しない</span>
              </label>
            </div>
            @if($errors->has('moving'))
            <label id="meter-error" class="error" for="moving">{{$errors->first('moving')}}</label>
            @endif
          </dd>

          <dt class="moving_info"><span class="txt_red">必須</span>引っ越し先の情報</dt>
          <dd class="pear moving_info">
            <dl>
              <dt>※引っ越し先での<br class="nosp">利用開始日</dt>
              <dd>
                <div class="short">
                  <div id="error3">
                    <select id="year1" name="start_year"></select>
                    @if($errors->has('start_year'))
                    <label id="year1-error" class="error" for="year1">{{$errors->first('start_year')}}</label>
                    @endif
                  </div>
                  <label class="datetxt">年</label>
                  <div id="error3">
                    <select id="month1" name="start_month"></select>
                    @if($errors->has('start_month'))
                    <label id="month1-error" class="error" for="month1">{{$errors->first('start_month')}}</label>
                    @endif
                  </div>
                  <label class="datetxt">月</label>
                  <div id="error3">
                    <select id="day1" name="start_day"></select>
                    @if($errors->has('start_day'))
                    <label id="day1-error" class="error" for="day1">{{$errors->first('start_day')}}</label>
                    @endif
                  </div>
                  <label class="datetxt">日</label>
                </div>
              </dd>
              <dt>※引っ越し先住所</dt>
              <dd class="wide h-adr">
                <span class="p-country-name" style="display:none;">Japan</span>

                〒<input type="text" id="" class="p-postal-code" name="new_postal" value="{{old('new_postal')}}" maxlength="7" placeholder="ハイフンなし半角数字" />
                @if($errors->has('new_postal'))
                <label id="new_postal-error" class="error" for="new_postal">{{$errors->first('new_postal')}}</label>
                @endif
                <br>
                <input type="text" id="" class="p-region p-locality p-street-address p-extended-address" name="new_add" value="{{old('new_add')}}" placeholder="住所" />
                @if($errors->has('new_add'))
                <label id="new_add-error" class="error" for="new_add">{{$errors->first('new_add')}}</label>
                @endif
                <br>
                <input type="text" id="" name="new_build" value="{{old('new_build')}}" placeholder="建物名・部屋番号" />
              </dd>
            </dl>
          </dd>

          <dt>解約する契約プラン</dt>
          <dd class="fnt_col">
            @if(count($contracts) == 1)
            <input type="hidden" name="plan" id="plan" value="{{ reset($contracts)['supplypoint_code'] }}">
            <span id="plan_name"></span>
            <input type="hidden" name="plan_name" value="">
            @elseif(count($contracts) > 1)
            <span id="plan_name">※複数契約があるため、カスタマーセンターへお電話にて解約手続きをお願いいたします。</span>
            @endif
            @if($errors->has('plan'))
            <label id="plan-error" class="error" for="plan">{{$errors->first('plan')}}</label>
            @endif
            <!-- <div>
              <select name="plan" id="plan">
                <option value>選択する</option>
                @foreach($contracts as $contract)
                <option value="{{$contract['supplypoint_code']}}" {{ old('plan') === $contract['supplypoint_code'] ? 'selected' : '' }}>{{ $contract['plan'] }}</option>
                @endforeach
              </select>
              @if($errors->has('plan'))
              <label id="plan-error" class="error" for="plan">{{$errors->first('plan')}}</label>
              @endif
            <input type="hidden" name="plan_name" value="">
            </div> -->
          </dd>

          <dt>ご契約中の住所</dt>
          <dd class="fnt_col">
            <input type="hidden" name="add" value="">
            <span id="add"></span><br>
          </dd>
          <dt class="electric_last_day"><span class="txt_red">必須</span>電気の最終利用日</dt>
          <dd class="required electric_last_day" id="hidden10">
            <div class="short">
              <div id="error5">
                <select id="year2" name="electric_last_year"></select>
                @if($errors->has('electric_last_year'))
                <label id="year2-error" class="error" for="year2">{{$errors->first('electric_last_year')}}</label>
                @endif
              </div>
              <label class="datetxt">年</label>
              <div id="error5">
                <select id="month2" name="electric_last_month"></select>
                @if($errors->has('electric_last_month'))
                <label id="month2-error" class="error" for="month2">{{$errors->first('electric_last_month')}}</label>
                @endif
              </div>
              <label class="datetxt">月</label>
              <div id="error5">
                <select id="day2" name="electric_last_day"></select>
                @if($errors->has('electric_last_day'))
                <label id="day2-error" class="error" for="day2">{{$errors->first('electric_last_day')}}</label>
                @endif
              </div>
              <label class="datetxt">日</label>
            </div>
            <p class="note">※本日から5営業日以降の日程を選択してください。</p>
          </dd>
          <dt class="gas_last_day"><span class="txt_red">必須</span>ガスの最終利用日</dt>
          <dd class="required gas_last_day">
            <div class="short">
              <div id="error5">
                <select id="year3" name="gas_last_year"></select>
                @if($errors->has('gas_last_year'))
                <label id="year3-error" class="error" for="year3">{{$errors->first('gas_last_year')}}</label>
                @endif
              </div>
              <label class="datetxt">年</label>
              <div id="error5">
                <select id="month3" name="gas_last_month"></select>
                @if($errors->has('gas_last_month'))
                <label id="month3-error" class="error" for="month3">{{$errors->first('gas_last_month')}}</label>
                @endif
              </div>
              <label class="datetxt">月</label>
              <div id="error5">
                <select id="day3" name="gas_last_day"></select>
                @if($errors->has('gas_last_day'))
                <label id="day3-error" class="error" for="day3">{{$errors->first('gas_last_day')}}</label>
                @endif
              </div>
              <label class="datetxt">日</label>
            </div>
            <p class="note">※本日から10営業日以降の日程を選択してください。</p>
          </dd>

          <dt>供給地点番号</dt>
          <dd class="fnt_col">
            <input type="hidden" name="supplypoint_code" value=""><span id="supplypoint_code"></span>
          </dd>
          <dt>お客様番号</dt>
          <dd class="fnt_col">
            <input type="hidden" name="customer_num" value="{{$user['customer_code']}}"><span id="customer_num">{{$user['customer_code']}}</span>
          </dd>
          <dt>契約名義</dt>
          <dd class="fnt_col">
            <input type="hidden" name="name" value=""><span id="name"></span>
          </dd>
          <dt>ご連絡先電話番号</dt>
          <dd class="fnt_col">
            <input type="hidden" name="phone" value="{{$user['phone']}}"><span id="phone">{{$user['phone']}}</span>
          </dd>

          <dt>
            <span class="txt_red">必須</span>
            解約後の請求書送付先
            <div class="balloon_base" onclick="showBalloon_invoice()">
              <i class="fa-regular fa-circle-question"></i>
              <p class="balloon2" id="makeImg2">
                <span class="title">※解約後のご請求について</span>
                <span class="txt">
                  最終のご請求は、最終ご利用月より最大2ヶ月後になります。
                </span>
                <span class="close">×　閉じる</span>
              </p>
            </div>
          </dt>
          <dd class="wide h-adr">
            <span class="p-country-name" style="display:none;">Japan</span>

            <span class="postal_code">〒</span><input type="text" class="p-postal-code" name="postal_send" value="{{old('postal_send')}}" maxlength="7" placeholder="ハイフンなし半角数字" />
            @if($errors->has('postal_send'))
            <label id="postal_send-error" class="error" for="postal_send">{{$errors->first('postal_send')}}</label>
            @endif
            <br>
            <input type="text" class="p-region p-locality p-street-address p-extended-address" name="add_send" value="{{old('add_send')}}" placeholder="住所" />
            @if($errors->has('add_send'))
            <label id="add_send-error" class="error" for="add_send">{{$errors->first('add_send')}}</label>
            @endif
            <br>
            <input type="text" name="build_send" value="{{old('build_send')}}" placeholder="建物名・部屋番号" />
          </dd>

          <dt class="bganother">手続き後のご連絡先<br>
            <p class="note">※ご契約電話番号と異なる場合のみ入力
          </dt>
          <dd class="bganother">
            <input type="tel" id="phone" name="tel" value="{{old('tel')}}" placeholder="ハイフン無し半角数字" />
            @if($errors->has('tel'))
            <label id="phone-error" class="error active" for="phone" style="">{{$errors->first('tel')}}</label>
            @endif
          </dd>

          <dt class="bganother line_btm line_btm_none"><span class="txt_red">必須</span>申し込み完了通知を<br class="nosp">受信するE-mail</dt>
          </dt>
          <dd class="required bganother line_btm">
            <input type="email" id="mail_address" name="mail" value="{{old('mail')}}" placeholder="メールアドレス入力※全角不可" />
            @if($errors->has('mail'))
            <label id="mail_address-error" class="error" for="mail_address">{{$errors->first('mail')}}</label>
            @endif
            <p class="note">※メールの受信設定をお願いいたします。</p>
          </dd>

          <div class="caution">
            <p>
              <span>▼補足事項</span><br>
              最終ご利用日の指定日によって、最終請求月、請求金額の確定日などのずれが生じる場合がございます。<br>
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
            <div id="error7">
              <label for="Checkbox2">
                <input id="Checkbox2" name="purapori" type="checkbox" value="1" {{ old('purapori') === '1' ? 'checked' : '' }}/>
                <span class="check_label">プライバシーポリシーに同意する</span></label>
            </div>
            @if($errors->has('purapori'))
            <label id="purapori-error" class="error" for="purapori">{{$errors->first('purapori')}}</label>
            @endif
          </div>

          <p id="form_submit">
            <button type="submit">入力内容を確認する</button>
            <!-- <input type="button" id="form_submit_button" value="入力内容を確認する" /> -->
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
<script src="{{asset('js/ageRestriction.js') }}"></script>
<script src="{{asset('js/contract_close_form.js') }}"></script>
{{-- ライブラリ読み込み --}}
<script src="https://kit.fontawesome.com/d6027630b2.js" crossorigin="anonymous"></script>
<script src="https://yubinbango.github.io/yubinbango/yubinbango.js" charset="UTF-8"></script>
<script>
  const contracts = @json($contracts);
  // 引っ越し開始日
  var start_year = {{ !empty(old('start_year')) ? old('start_year') : '0' }};
  var start_month = {{ !empty(old('start_month')) ? old('start_month') : '0' }};
  var start_day = {{ !empty(old('start_day')) ? old('start_day') : '0' }};
  // 電気最終日
  var electric_last_year = {{ !empty(old('electric_last_year')) ? old('electric_last_year') : '0' }};
  var electric_last_month = {{ !empty(old('electric_last_month')) ? old('electric_last_month') : '0' }};
  var electric_last_day = {{ !empty(old('electric_last_day')) ? old('electric_last_day') : '0' }};
  // ガス最終日
  var gas_last_year = {{ !empty(old('gas_last_year')) ? old('gas_last_year') : '0' }};
  var gas_last_month = {{ !empty(old('gas_last_month')) ? old('gas_last_month') : '0' }};
  var gas_last_day = {{ !empty(old('gas_last_day')) ? old('gas_last_day') : '0' }};
</script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
