<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name=”robots” content=”noindex”>
    <link href="{{public_path('css/renewal/pdf.css')}}" rel="stylesheet">
    <title>ご使用量のお知らせ | グランデータマイページ</title>
</head>
<body>
    <header class="header">
        <div class="header__left">
            {{ $detail['power_zip_code'] ? '〒'.$detail['power_zip_code']:  '' }}<br>
            {{ $detail['address'] }}<br>
            {{ $detail['contract_name'] }} 様
        </div>
        <div class="header__right">
            【お問い合わせ先】<br>
            カスタマーセンター：0570-070-336<br>
            【受付時間】10:00～18:00<br>
            ※年末年始は、非営業日<br>
            <br>
            【還付先】<br>
            株式会社グランデータ<br>
            〒171-0022 東京都豊島区南池袋二丁目9番9号<br>
        </div>
    </header>
    <section class="body">
        <h1 class="title">ご使用量のお知らせ</h1>
        <div class="text">
            平素より、弊社サービスをご利用いただきまして誠にありがとうございます。<br>
            ご使用量(ガス)を下記の通りご案内させていただきます。<br>
        </div>
        <div class="suppypointCode">
            <p class="suppypointCode__head">供給地点特定番号</p>
            <p class="suppypointCode__body">{{ $detail['supplypoint_code'] }}</p>
        </div>

        <div class="detail">
            <table class="table">
                <tr >
                    <th style="width: 163px;">申込名義人</th>
                    <td class="left">{{ $detail['contract_name'] }}</td>
                </tr>
                <tr>
                    <th>ご使用場所住所</th>
                    <td class="left">{{ $detail['address'] }}</td>
                </tr>
            </table>

                <table class="table table-slim">
                <tbody>
                    <tr>
                        <th colspan="4">利用対象月</th>
                        <td colspan="6">{{ substr($detail['usage_date'], 0, 4) }}年{{ substr($detail['usage_date'], 4, 2) }}月分</td>
                    </tr>
                    <tr>
                        <th colspan="4">ご使用期間</th>
                        <td colspan="6">
                        @php
                            $startDate = explode('-', $detail['start_date']);
                            $endDate = explode('-', $detail['end_date']);
                        @endphp
                        {{ $startDate[1] }}月{{ $startDate[2] }}日 ～ {{ $endDate[1] }}月{{ $endDate[2] }}日
                        </td>
                    </tr>
                    <tr>
                        <th colspan="4">検針月日</th>
                        <td colspan="6">
                        @php
                            $date = explode('-', $detail['metering_date']);
                        @endphp
                        {{ $date[1] }}月{{ $date[2] }}日
                        </td>
                    </tr>
                    <tr>
                        <th colspan="4">ご使用量</th>
                        <td colspan="6">{{ $detail['usage'] }} {!! $detail['usage_unit_html'] !!}</td>
                    </tr>
                    <tr>
                        <th colspan="4">請求予定金額<div class="fs12">(消費税相当額含む)</div></th>
                        <td colspan="6" class="right">{{ number_format($detail['billing_amount']) }}円</td>
                    </tr>
                    <tr>
                        <th class="w30">上<br>記<br>料<br>金<br>内<br>訳</th>
                        @php
                            $count = count($billing_itemize);
                            if ($count < 20) {
                                for ($i = 1; $i <= (20 - $count); $i++) {
                                    array_push($billing_itemize, ['itemize_name' => '', 'itemize_bill' => '']);
                                }
                            }
                        @endphp
                        <td colspan="5" class="itemize_name left">
                        @foreach($billing_itemize as $key => $item)
                            <p class="item">{{ empty($item['itemize_name']) ? '　' : $item['itemize_name'] }}</p>
                        @endforeach
                        </td>
                        <td colspan="4" class="itemize_value right">
                        @foreach($billing_itemize as $key => $item)
                            <p class="item">
                            @if ($item['itemize_bill'])
                                @php
                                    $bill = explode('.', $item['itemize_bill']);
                                    if ($bill[0]) echo number_format($bill[0]). '円';
                                    if ($bill[1] && $bill[1] != '00' && $bill[1] != '0') echo $bill[1]. '銭';
                                @endphp
                            @else
                                {{ '　' }}
                            @endif
                            </p>
                        @endforeach
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table class="table table-slim table-tall">
                    <tr>
                        <th>プラン名</th>
                        <td {!! mb_strlen( $detail['plan']) > 12 ? ' class="fs12"' : '' !!}>{{ $detail['plan'] }}</td>
                    </tr>
                    <tr>
                        <th>{{'　'}}{{-- ご契約 --}}</th>
                        <td>{{-- {{ $detail['contract_capacity'] }} --}}</td>
                    </tr>
                    <tr>
                        <th>{{'　'}}{{-- 力率 --}}</th>
                        <td>{{-- {{ isset($detail['power_percentage']) && $detail['power_percentage'] ? $detail['power_percentage'].'%' : '' }} --}}</td>
                    </tr>
                    <tr class="border_none">
                        <th>当月指示数</th>
                        <td class="right">{{ $detail['main_indicator'] }}</td>
                    </tr>
                    <tr class="border_none">
                        <th>前月指示数</th>
                        <td class="right">{{ $detail['main_indicator_last_month'] }}</td>
                    </tr>
                    <tr class="border_none">
                        <th>差引</th>
                        <td class="right">{{ $detail['difference'] }}</td>
                    </tr>
                    <tr class="border_none">
                        <th>{{'　'}}{{-- 計器乗率 --}}</th>
                        <td class="right">{{-- {{ $detail['meter_multiply'] }} --}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 1px; border-right: none;"></td>
                        <td style="padding: 1px; border-left: none;"></td>
                    </tr>
                    <tr>
                        <th class="">振替予定日</th>
                        <td {!! mb_strlen( $detail['payment_date']) > 12 ? ' class="fs12"' : '' !!}>{{ $detail['payment_date'] }}</td>
                    </tr>
                    <tr>
                        <th class="">次回検針予定日</th>
                        <td>
                        @php
                            if($detail['next_metering_date']) {
                                $date = explode('-', $detail['next_metering_date']);
                                echo $date[0] .'年'. $date[1] .'月' .$date[2].'日';
                            }
                        @endphp
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none;"></td>
                        <td style="border: none;"></td>
                    </tr>
                    <tr>
                        <td class="fs14">グランデータお客様ID</td>
                        <td>{{ $user->customer_code }}</td>
                    </tr>
                    <tr>
                        <td>　</th>
                        <td>　</td>
                    </tr>
                    <tr>
                        <td class="left textarea fs14" colspan="2">
                            <span>
                                ※メーター交換などにより、指示数の差引がガス使用量と一致しない場合がございます。
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none;"></td>
                        <td style="border: none;"></td>
                    </tr>
                    <tr class="fs12">
                        <td>小売ガス事業者名称<br>登録番号</td>
                        <td>株式会社グランデータ<br>A0087</td>
                    </tr>
                </table>
                <p style="clear: left;"></p>
        </div>
    </section>
</body>
</html>