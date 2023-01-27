<!doctype html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link href="{{public_path('css/thankyou_letter.css')}}" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="page">
        <div class="head_caution">
            <p>
                この書面は、お申込内容を記載した<br>
                重要な書面のため、書面の内容を確認<br>
                して、大切に保管してください。
            </p>
        </div>
        <div class="head_address">
            〒{{ $contract->invoice_zip }}<br>
            {{ $contract->invoice_address1 }}<br>
            {{ $contract->invoice_address2 }}<br>
            <br>
            {{ $contract->invoice_name }} 様<br>
        </div>
        <div class="head_customer">
            【お問い合わせ先】<br>
            @if(!empty($brand))
            {{ $brand[App\Consts\BrandConsts::CUSTOMER_NAME_INDEX]}}　{{ $brand[App\Consts\BrandConsts::PHONE_INDEX]}}<br>
            受付時間：{{ $brand[App\Consts\BrandConsts::RECEPTION_TIME_INDEX]}}　定休日：{{ $brand[App\Consts\BrandConsts::HOLIDAY_INDEX]}}<br>
            @endif
            <br>
            <div class="qr">
                <img alt="qr_code" width="80" height="80" src="{{public_path('img/mypage_qr.png')}}"><br>
            </div>
            マイページの二次元コードはこちら
        </div>
        <div class="body">
            <div class="title">契約のお知らせ</div>
            <div class="text">
                この度はお申込みいただき、誠にありがとうございます｡<br>
                以下の内容にてご契約をさせていただきますので、申込内容をご確認いただきますようお願い申し上げます。<br>
                <div class="text_r">以上</div>
            </div>
            <br>
            <table border="1">
                <tr>
                    <th colspan="14">契約者様</th>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">お客様番号</td>
                    <td colspan="11">{{ $contract->customer_id }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">契約者名</td>
                    <td colspan="11">{{ $contract->contract_name }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">契約住所</td>
                    <td colspan="11">{{ $contract->contract_address }}</td>
                </tr>
            </table>
            <table border="1">
                <tr>
                    <th colspan="14">ログイン</th>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">マイページID</td>
                    <td colspan="4">{{ $contract->customer_code }}</td>
                    <td class="gray_column" colspan="3">初期パスワード</td>
                    <td colspan="4">{{ $contract->login_password }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">メールアドレス</td>
                    <td colspan="11">{{ $contract->mail_address }}</td>
                </tr>
            </table>
            <br>
            <br>
            <table border="1">
                <tr>
                    <th colspan="14">事業者項目</th>
                </tr>
                @if(!empty($add_supplier))
                <tr>
                    <td class="gray_column" colspan="3">事業者区分</td>
                    <td colspan="11">サービス提供元</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">名称</td>
                    <td colspan="11">{{ $add_supplier[App\Consts\SupplierConsts::NAME_INDEX] }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">郵便番号</td>
                    <td colspan="11">〒{{ $add_supplier[App\Consts\SupplierConsts::ZIP_CODE_INDEX] }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">所在地</td>
                    <td colspan="11">{{ $add_supplier[App\Consts\SupplierConsts::ADDRESS_INDEX] }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">電話番号</td>
                    <td colspan="11">{{ $add_supplier[App\Consts\SupplierConsts::PHONE_INDEX] }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">受付時間</td>
                    <td colspan="11">{{ $add_supplier[App\Consts\SupplierConsts::RECEPTION_TIME_INDEX] }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">プライバシーポリシー</td>
                    <td colspan="11">{{ $add_supplier[App\Consts\SupplierConsts::PRIVACY_POLICY_INDEX] }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3"></td>
                    <td colspan="11"></td>
                </tr>
                @endif
                <tr>
                    <td class="gray_column" colspan="3">事業者区分</td>
                    <td colspan="11">{{ App\Consts\SupplierConsts::SUPPLIER_TYPE_LIST[$supplier[App\Consts\SupplierConsts::SUPPLIER_TYPE_INDEX]] }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">名称</td>
                    <td colspan="11">{{ $supplier[App\Consts\SupplierConsts::NAME_INDEX] }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">郵便番号</td>
                    <td colspan="11">〒{{ $supplier[App\Consts\SupplierConsts::ZIP_CODE_INDEX] }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">所在地</td>
                    <td colspan="11">{{ $supplier[App\Consts\SupplierConsts::ADDRESS_INDEX] }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">電話番号</td>
                    <td colspan="11">{{ $supplier[App\Consts\SupplierConsts::PHONE_INDEX] }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">受付時間</td>
                    <td colspan="11">{{ $supplier[App\Consts\SupplierConsts::RECEPTION_TIME_INDEX] }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">プライバシーポリシー</td>
                    <td colspan="11">{{ $supplier[App\Consts\SupplierConsts::PRIVACY_POLICY_INDEX] }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3"></td>
                    <td colspan="11"></td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3"></td>
                    <td colspan="11">以上</td>
                </tr>
                @for($i = 0; $i < 8; $i++)
                <tr>
                    <td class="gray_column" colspan="3"></td>
                    <td colspan="11"></td>
                </tr>
                @endfor
            </table>
            <table border="1">
                <tr>
                    <th colspan="14">契約締結販売店</th>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">販売店</td>
                    <td colspan="4">{{ $contract->shop_name }}</td>
                    <td class="gray_column" colspan="3">担当者</td>
                    <td colspan="4">{{ $contract->staff_name }}</td>
                </tr>
            </table>
            <div class="page_break"></div>
            <table border="1">
                <tr>
                    <th colspan="14">契約内容</th>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">申込日</td>
                    <td colspan="4">{{ $contract->apply_date }}</td>
                    <td class="gray_column" colspan="3">供給地点番号</td>
                    <td colspan="4">{{ $contract->power_customer_location_number }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">供給開始予定日</td>
                    <td colspan="4">{{ $contract->switching_scheduled_date }}</td>
                    <td class="gray_column" colspan="3">初回請求月</td>
                    <td colspan="4">{{ $contract->after_2month }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">使用場所名義</td>
                    <td colspan="11">{{ $contract->power_customer_name }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">使用場所住所</td>
                    <td colspan="11">〒{{ $contract->power_zip }} {{ $contract->power_address }}</td>
                </tr>
            </table>
            <table border="1">
                <tr>
                    <th colspan="14">プラン</th>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">契約プラン</td>
                    <td colspan="11">{{ $contract->power_plan_name }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="3">契約期間</td>
                    <td colspan="4">{{ $contract->contract_months }}</td>
                    <td class="gray_column" colspan="3">解約事務手数料</td>
                    <td colspan="4">{{ number_format($contract->cancel_fee) }}円（税込）</td>
                </tr>
            </table>
            <table border="1">
                <tr>
                    <th colspan="20">支払</th>
                </tr>
                <tr>
                    <td class="gray_column" colspan="5">支払方法</td>
                    <td colspan="10">{{ App\Consts\PaymentOrderedConsts::PAYMENT_TYPE_THANKYOU_LETTER_LIST[empty($contract->payment_type) ? '0' : $contract->payment_type] }}</td>
                    <td class="gray_column" colspan="2">請求締日</td>
                    <td colspan="3">{{ $contract->billing_closing_date }}</td>
                </tr>
                <tr>
                    <td class="gray_column" colspan="5">支払日</td>
                    <td colspan="15">{{ App\Consts\PaymentOrderedConsts::PAYMENT_TYPE_MSG_LIST[empty($contract->payment_type) ? '0' : $contract->payment_type] }}</td>
                </tr>
            </table>
            <table border="1">
                <tr>
                    <th colspan="4">料金</th>
                </tr>
                @php
                    $billing_rows = 0;
                @endphp
                @if(!empty($contract->detail_language_1_basic_a))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_1_basic_a }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_basic }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_basic }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->basic_price, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @if(!empty($contract->detail_language_1))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_1 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_1 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_1 }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->current_type1_price, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @if(!empty($contract->detail_language_1_basic_b))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_1_basic_b }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_basic_b }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_basic_b }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->basic_price_b, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @if(!empty($contract->detail_language_2))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_2 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_2 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_2 }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->current_type2_price, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @if(!empty($contract->detail_language_1_basic_c))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_1_basic_c }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_basic_c }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_basic_c }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->basic_price_c, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @if(!empty($contract->detail_language_3))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_3 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_3 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_3 }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->current_type3_price, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @if(!empty($contract->detail_language_1_basic_d))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_1_basic_d }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_basic_d }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_basic_d }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->basic_price_d, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @if(!empty($contract->detail_language_4))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_4 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_4 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_4 }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->current_type4_price, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @if(!empty($contract->detail_language_1_basic_e))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_1_basic_e }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_basic_e }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_basic_e }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->basic_price_e, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @if(!empty($contract->detail_language_5))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_5 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_5 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_5 }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->current_type5_price, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @if(!empty($contract->detail_language_1_basic_f))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_1_basic_f }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_basic_f }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_basic_f }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->basic_price_f, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @if(!empty($contract->detail_language_6))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_6 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_6 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_6 }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->current_type6_price, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @if(!empty($contract->detail_language_1_basic_g))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_1_basic_g }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_basic_g }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_basic_g }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->basic_price_g, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @if(!empty($contract->detail_language_7))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_7 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_7 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_7 }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->current_type7_price, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @if(!empty($contract->detail_language_1_basic_h))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_1_basic_h }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_basic_h }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_basic_h }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->basic_price_h, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @if(!empty($contract->detail_language_8))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $contract->detail_language_8 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->detail_language2_8 }}</td>
                    <td class="text_r" colspan="1">{{ $contract->unit_8 }}</td>
                    <td class="text_r" colspan="1">{{ number_format($contract->current_type8_price, 2, '円', ',') }}銭（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @foreach($options as $option)
                @if(in_array($option->option_plan_id, App\Consts\HalueneOptionPlanConsts::TL_RYOKIN_DISPLAY_LIST, true))
                <tr>
                    <td class="gray_column text_r" colspan="1">{{ $option->option_name }}</td>
                    <td class="text_r" colspan="1"></td>
                    <td class="text_r" colspan="1">{{ App\Consts\HalueneOptionPlanConsts::PAYMENT_TYPE_LIST[$option->option_payment_type] }}</td>
                    <td class="text_r" colspan="1">{{ number_format($option->option_price) }}円（税込）</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @endif
                @endforeach
                <tr>
                    <td class="gray_column text_r" colspan="1"></td>
                    <td class="text_r" colspan="1"></td>
                    <td class="text_r" colspan="1"></td>
                    <td class="text_r" colspan="1">以上</td>
                    @php
                        $billing_rows++;
                    @endphp
                </tr>
                @for($i = $billing_rows; $i < 22; $i++)
                <tr>
                    <td class="gray_column text_r" colspan="1"></td>
                    <td class="text_r" colspan="1"></td>
                    <td class="text_r" colspan="1"></td>
                    <td class="text_r" colspan="1"></td>
                </tr>
                @endfor
            </table>
            <table border="1">
                <tr>
                    <th colspan="4">付帯サービス・オプションサービス</th>
                </tr>
                @php
                    $rows = 1;
                @endphp
                @foreach($options as $index => $option)
                @if(in_array($option->option_plan_id, App\Consts\HalueneOptionPlanConsts::TL_FUTAI_DISPLAY_LIST, true))
                <tr>
                    <td class="gray_column" colspan="1">契約サービス名</td>
                    <td colspan="3">{{ $option->option_name }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                <!-- つながる修理サポート（Z） -->
                @if($option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_S || $option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_S2)
                @if(!empty($option->cp_id_key))
                <tr>
                    <td class="gray_column" colspan="1">つながる修理サポートID</td>
                    <td colspan="3">{{ $option->cp_id_key }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @endif
                <!-- つながる修理サポート（Z） -->
                @if($option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_Z && !empty($option->cp_id_key_z))
                <tr>
                    <td class="gray_column" colspan="1">つながる修理サポートID</td>
                    <td colspan="3">{{ $option->cp_id_key_z }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                <!-- つながる修理サポート（M） -->
                @if($option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_TSUNAGARU_SYURI_SUPPORT_M && !empty($option->cp_id_key_m))
                <tr>
                    <td class="gray_column" colspan="1">つながる修理サポートID</td>
                    <td colspan="3">{{ $option->cp_id_key_m }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                <!-- 家電修理サポート -->
                @if($option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_KADEN_SYURI_SUPPORT && !empty($option->cp_id_key_z))
                <tr>
                    <td class="gray_column" colspan="1">修理サポートID</td>
                    <td colspan="3">{{ $option->cp_id_key_z }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                <!-- モバイル修理サポート、モバイル修理サポートプラス -->
                @if(($option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_MOBILE_SYURI_SUPPORT || $option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_MOBILE_SYURI_SUPPORT_PULS) && !empty($option->cp_id_key_m))
                <tr>
                    <td class="gray_column" colspan="1">修理サポートID</td>
                    <td colspan="3">{{ $option->cp_id_key_m }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                <!-- お財布サポートbyえらべる倶楽部 -->
                @if($option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_OSAIFU_SUPPORT_ERABERUCLUB && !empty($option->serial))
                <tr>
                    <td class="gray_column" colspan="1">シリアル番号</td>
                    <td colspan="3">{{ $option->serial }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                <!-- ABEMAプレミアム -->
                @if($option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_ABEMA_PREMIUM)
                @if(!empty($option->coupon_id))
                <tr>
                    <td class="gray_column" colspan="1">ログインID</td>
                    <td colspan="3">{{ $option->coupon_id }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->coupon_code))
                <tr>
                    <td class="gray_column" colspan="1">クーポンコード</td>
                    <td colspan="3">{{ $option->coupon_code }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @endif
                <!-- music.jp -->
                @if($option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_MUSICJP_DOUBGA_HIMAWARIDENKIB_COURSE || $option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_MUSICJP_MULTI_HIMAWARIDENKIB_COURSE)
                @if(!empty($option->music_jp_id))
                <tr>
                    <td class="gray_column" colspan="1">ログインID</td>
                    <td colspan="3">{{ $option->music_jp_id }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->music_jp_password))
                <tr>
                    <td class="gray_column" colspan="1">パスワード</td>
                    <td colspan="3">{{ $option->music_jp_password }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @endif
                <!-- music.jp動画コース -->
                @if($option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_MUSICJP_DOUBGA_COURSE)
                @if(!empty($option->music_movie_account_id))
                <tr>
                    <td class="gray_column" colspan="1">ログインID</td>
                    <td colspan="3">{{ $option->music_movie_account_id }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->music_movie_account_password))
                <tr>
                    <td class="gray_column" colspan="1">パスワード</td>
                    <td colspan="3">{{ $option->music_movie_account_password }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @endif
                <!-- music.jp漫画コース -->
                @if($option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_MUSICJP_MANGA_COURSE)
                @if(!empty($option->music_comic_account_id))
                <tr>
                    <td class="gray_column" colspan="1">ログインID</td>
                    <td colspan="3">{{ $option->music_comic_account_id }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->music_comic_account_password))
                <tr>
                    <td class="gray_column" colspan="1">パスワード</td>
                    <td colspan="3">{{ $option->music_comic_account_password }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @endif
                <!-- ペットハート -->
                @if($option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_PET_HEART || $option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_PET_HEART_HEART_PREMIUM)
                @if(!empty($option->servise_id12))
                <tr>
                    <td class="gray_column" colspan="1">ログインID</td>
                    <td colspan="3">{{ $option->servise_id12 }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->password12))
                <tr>
                    <td class="gray_column" colspan="1">パスワード</td>
                    <td colspan="3">{{ $option->password12 }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @endif
                <!-- スマートシネマ -->
                @if($option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_SMART_CINEMA_UNEXT_LITE_PLAN ||
                $option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_MONTHLY_PLAN ||
                $option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_OLD ||
                $option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_NEW)
                @if(!empty($option->cp_smart_cinema_id_key))
                <tr>
                    <td class="gray_column" colspan="1">スマートシネマ_ID</td>
                    <td colspan="3">{{ $option->cp_smart_cinema_id_key }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->cp_smart_cinema_password_key))
                <tr>
                    <td class="gray_column" colspan="1">パスワード</td>
                    <td colspan="3">{{ $option->cp_smart_cinema_password_key }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->cp_smart_cinema_gift_code))
                <tr>
                    <td class="gray_column" colspan="1">スマートシネマ_ギフトコード</td>
                    <td colspan="3">{{ $option->cp_smart_cinema_gift_code }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->start_date_text5))
                <tr>
                    <td class="gray_column" colspan="1">サービス利用開始日</td>
                    <td colspan="3">{{ $option->start_date_text5 }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @endif
                @if(!empty($option->start_date_text))
                <tr>
                    <td class="gray_column" colspan="1">サービス利用開始日</td>
                    <td colspan="3">{{ $option->start_date_text }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->immunity_text))
                <tr>
                    <td class="gray_column" colspan="1">サービス免責期間</td>
                    <td colspan="3">{{ $option->immunity_text }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->url))
                <tr>
                    <td class="gray_column" colspan="1">サービスURL</td>
                    <td colspan="3">{{ $option->url }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->supplier))
                <tr>
                    <td class="gray_column" colspan="1">サービス提供会社</td>
                    <td colspan="3">{{ $option->supplier }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                <!-- スマートシネマ -->
                @if($option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_SMART_CINEMA_UNEXT_LITE_PLAN ||
                $option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_MONTHLY_PLAN ||
                $option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_OLD ||
                $option->option_plan_id == App\Consts\HalueneOptionPlanConsts::ID_UNEXT_SMART_CINEMA_POINT_PLAN_NEW)
                @if(!empty($option->smart_cinema_management_source))
                <tr>
                    <td class="gray_column" colspan="1">スマートシネマ運営元</td>
                    <td colspan="3">{{ $option->smart_cinema_management_source }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @endif
                @if(!empty($option->about_billing_text))
                @if(!is_array($option->about_billing_text))
                <tr>
                    <td class="gray_column" colspan="1">利用料金の請求について</td>
                        <td colspan="3">{{ $option->about_billing_text }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @else
                @foreach($option->about_billing_text as $key => $text)
                <tr>
                    @if($key == 0)
                    <td class="gray_column" colspan="1">利用料金の請求について</td>
                    @else
                    <td class="gray_column" colspan="1"></td>
                    @endif
                    <td colspan="3">{{ $text }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endforeach
                @endif
                @endif
                @if(!empty($option->reception_counter))
                <tr>
                    <td class="gray_column" colspan="1">受付窓口</td>
                    <td colspan="3">{{ $option->reception_counter }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->phone))
                <tr>
                    <td class="gray_column" colspan="1">電話番号</td>
                    <td colspan="3">{{ $option->phone }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->contact_url))
                <tr>
                    <td class="gray_column" colspan="1">問い合わせURL</td>
                    <td colspan="3">{{ $option->contact_url }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->option_reception_time))
                <tr>
                    <td class="gray_column" colspan="1">受付時間</td>
                    <td colspan="3">{{ $option->option_reception_time }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->holiday))
                <tr>
                    <td class="gray_column" colspan="1">定休日</td>
                    <td colspan="3">{{ $option->holiday }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if(!empty($option->agreement_file_title))
                <tr>
                    <td class="gray_column" colspan="1">利用規約表示名</td>
                    <td colspan="3">{{ $option->agreement_file_title }}</td>
                </tr>
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @if($index !== array_key_last($options))
                <tr>
                    <td class="gray_column" colspan="1"></td>
                    <td colspan="3"></td>
                </tr>
                @endif
                @include('layout.t_thankyou_letter_breakpage', ['rows' => ++$rows])
                @endif
                @endforeach
                @if($rows > 1)
                <tr>
                    <td class="gray_column" colspan="1"></td>
                    <td colspan="3">以上</td>
                </tr>
                @endif
                @php
                    //空白行数の調整
                    if($rows <= 33) {
                        $max_row = 33;
                        $counts = $rows;
                    } else {
                        $max_row = 70;
                        $counts = ($rows - 33) % 70;
                    }
                @endphp
                @for($i=0; $i < $max_row - $counts; $i++)
                <tr>
                    <td class="gray_column" colspan="1"></td>
                    <td colspan="3"></td>
                </tr>
                @endfor
            </table>
        </div>
    </div>
</body>
</html>