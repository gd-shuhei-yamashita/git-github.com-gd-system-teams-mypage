{{-- WiMAX配達予定日 --}}
@if (Session::get('wifi_delivery_date') && strtotime(Session::get('wifi_delivery_date')) > strtotime(date('Y-m-d 23:59:59')) )
<table class="home_delivery table">
    <tr>
        <th>
            WiMAX配達予定日
            <p>
                @if (!Session::get('wifi_delivery_date_change_url') || strtotime(Session::get('wifi_delivery_date')) < strtotime('+7 day'))
                {{-- 非表示にする --}}
                {{-- <a class="change_date"><i class="fa-solid fa-angle-right"></i>配達日時を変更</a> --}}
                @else
                <a href="{{ Session::get('wifi_delivery_date_change_url') }}" class="change_date" target="_blank" rel="noreferrer"><i class="fa-solid fa-angle-right"></i>配達日時を変更</a>
                @endif
            </p>
        </th>
        <th class="" style="text-align: right;">
            {{date('Y年m月d日', strtotime(Session::get('wifi_delivery_date')))}}<br>
            {{Session::get('wifi_delivery_time')}}
        </th>
    </tr>
</table>
<div class="warning_box">
    <p class="warning">※配達予定日の7日前まで変更を受付可能です。7日前を過ぎた場合の変更は出来かねますのでお気を付けください。</p>
    <p class="warning">※悪天候・道路交通状況・宅配業者事情による配送遅延が発生する場合がございます。</p>
</div>
@endif