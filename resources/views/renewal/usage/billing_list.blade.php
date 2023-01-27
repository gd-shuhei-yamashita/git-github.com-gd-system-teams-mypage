{{--  --}}
<table class="table-b">
    <thead>
        <tr>
            <th>請求年月</th>
            <th>合計金額</th>
            <th>利用期間</th>
            <th class="btn">詳細</th>
            {{-- @if (!isset($billingList) || (isset($downloadable) && $downloadable))
            <th class="btn">明細ダウンロード</th>
            @endif --}}
        </tr>
    </thead>
    <tbody>
    @if (isset($billingList))
    @foreach ($billingList as $billing)
        <tr>
            <td class="date">{{ substr($billing['billing_date'], 0, 4) }}年{{ substr($billing['billing_date'], 4, 2) }}月分</td>
            <td class="price">{{ $billing['billing_amount'] }}円</td>
            <td class="date">
                {{ date('m月d日', strtotime($billing['start_date'])) }}～{{ date('m月d日', strtotime($billing['end_date'])) }}
            </td>
            <td class="btn"><a class="detail-btn" href="{{ route('confirm_usagedata_detail') }}?date={{$billing['usage_date']}}&supplypoint_code={{$billing['supplypoint_code']}}" disabled>確認</a></td>
            {{-- @if (isset($downloadable) && $downloadable)
            <td class="btn"><a class="detail-btn" href="{{ route('specification_pdf', ['supplypoint_code' => $billing['supplypoint_code'], 'date' => $billing['usage_date']]) }}" download="" disabled>DL</a></td>
            @endif --}}
        </tr>
    @endforeach
    @else
        @for ($i = 0; $i < 5; $i++)
            <tr>
                <td class="date">----年-月分</td>
                <td class="en">--,---円</td>
                <td class="date">-月-日～-月-日</td>
                <td class="btn"><a class="detail-btn" disabled>確認</a></td>
                {{-- <td class="btn"><a class="detail-btn" disabled>DL</a></td> --}}
            </tr>
        @endfor
    @endif
    </tbody>
</table>