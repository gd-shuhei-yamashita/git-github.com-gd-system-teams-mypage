{{-- 請求データ取得更新用 --}}
<div class="home-use">
    <a id="claim">{{ substr($billingDate, 0, 4) }}年{{ substr($billingDate, 4, 2) }}月請求分</a>
    <div class="other-month">
        <a href="#" id="billing_prev">先月</a>
        <img src="img/code_black.svg">
        <a href="#" id="billing_next">翌月</a>
    </div>
</div>

<table class="table total-table">
    <tr>
        <th>契約サービス<br>合計請求金額（税込）</th>
        <td class="right">
            @if ($totalAmount === 0)
            請求データがありません。請求データは初回請求月の20日に反映いたします。
            @else
            {{ is_numeric($totalAmount) ? number_format($totalAmount) : $totalAmount }}円
            @endif
        </td>
    </tr>
</table>
<div class="home-use">
    <a id="claim">契約中のサービス(請求分内訳)</a>
</div>
@php
$iconList = [
    'electric' => '<i class="service-icon fa-regular fa-lightbulb"></i>',
    'gas'      => '<i class="service-icon fa-solid fa-fire"></i>',
    'mobile'   => '<i class="service-icon fa-solid fa-wifi"></i>',
    'option'   => '<i class="service-icon fa-solid fa-gear"></i>'
]
@endphp
<table class="table service-table">
    @forelse ($contracts as $contract)
    <tr>
        <th>{!! $iconList[$contract['type']] !!}{{ $contract['plan'] }}</th>
        <td>
            <div class="t_flex">
                @php
                $billngMessage = isset($contract['billing_message']) ? $contract['billing_message'] : null;
                $billingAmount = isset($contract['billing_amount']) ? number_format($contract['billing_amount']) : 0;
                $billingCount = isset($contract['billing_count']) ? $contract['billing_amount'] : 0;
                @endphp
                @if ($billngMessage)
                    {{ $billngMessage }}
                @elseif ($billingAmount > 0)
                    {{ $billingAmount }}円
                @elseif ($billingCount > 0)
                    請求データは毎月の20日頃に反映致します。
                @else
                    請求データがありません。請求データは初回請求月の20日に反映いたします。
                @endif
                <div class="link-btn">
                    @if ($billingAmount > 0)
                        <button type="button" onclick="location.href='confirm_usagedata?date={{ $contract['usage_date'] }}&supplypoint_code={{ $contract['supplypoint_code']}}'">
                            詳細
                            <i class="fa-solid fa-arrow-right"></i>
                            {{-- <img src="img/arrow_right_black.svg" alt="→"> --}}
                        </button>
                    @else
                        <button type="button" disabled>
                            詳細
                            <i class="fa-solid fa-arrow-right"></i>
                            {{-- <img src="img/arrow_right_black.svg" alt="→"> --}}
                        </button>
                    @endif
                </div>
            </div>
        </td>
    </tr>
    @empty
    @endforelse
</table>
