<!doctype html>
<html>
    <meta charset="utf-8">
    <meta name=”robots” content=”noindex”>
    <link href="{{public_path('css/renewal/pdf.css')}}" rel="stylesheet">
    <title>ご使用量のお知らせ | グランデータマイページ</title>
    <style>
    .table {
        page-break-inside: avoid;
    }
    </style>
</head>
<body>
    <header class="header">
        <div class="header__left">
        </div>
        <div class="header__right fs16">
            <div class="fs18 right">発行日 {{$date}}　</div>
            <h2 class="companyName">株式会社グランデータ</h2>
            〒171-0022<br>
            東京都豊島区南池袋二丁目9番9号<br>
            カスタマーセンター：0570-070-336<br>
            【受付時間】10:00～18:00<br>
            ※年末年始は、非営業日
        </div>
    </header>

    <section class="body">
        <h1 class="title">領収書</h1>
        <div class="text left">
            平素より、弊社サービスをご利用いただきまして誠にありがとうございます。<br>
            下記のとおり領収いたしました。
        </div>
        <table class="table">
            <tr>
                <th colspan="3">申込番号</th>
                <th colspan="4">供給地点番号</th>
                <th colspan="3">お客様名</th>
            </tr>
            <tr>
                <td colspan="3">{{ $contract['apply_number'] }}</td>
                <td colspan="4">{{ $contract['supplypoint_code'] }}</td>
                <td colspan="3">{{ $contract['contract_name'] }} 様</td>
            </tr>
        </table>
        <br>
        @foreach ($billings as $billing)
        <table class="table">
            <tr>
                <th colspan="3">ご請求月</th>
                <th colspan="4">ご利用年月</th>
                <th colspan="3">ご請求金額</th>
                <th colspan="3">領収日</th>
            </tr>
            <tr>
                <td colspan="3">{{ substr($billing['billing_date'], 0, 4) }}年{{ substr($billing['billing_date'], 4, 2) }}月</td>
                <td colspan="4">
                    @php
                        $startDate = explode('-', $billing['start_date']);
                        $endDate = explode('-', $billing['end_date']);
                    @endphp
                    {{ $startDate[0] }}年{{ $startDate[1] }}月{{ $startDate[2] }}日
                    ～<br>
                    {{ $endDate[0] }}年{{ $endDate[1] }}月{{ $endDate[2] }}日
                </td>
                <td colspan="3">¥{{ number_format($billing['billing_amount']) }}</td>
                <td colspan="3">？？？</td>
            </tr>
        </table>
        @endforeach
    </div>
</body>
</html>