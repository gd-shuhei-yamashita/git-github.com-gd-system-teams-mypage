{{--  --}}
<div class="use-plan">
    <div class="use1">
        <a>ご利用サービスを選択</a>
        <div class="cp_ipselect cp_sl02" id="service">
            <select name="service" class="js-select-service">
                @php
                $labels = [
                    'electric' => '電気',
                    'gas'      => 'ガス',
                    'mobile'   => 'WiMAX',
                    'option'   => 'オプション',
                ];
                @endphp
                @forelse ($serviceTypeList as $serviceType)
                <option value="{{$serviceType}}">{{$labels[$serviceType]}}</option>
                @empty
                <option value="">---------</option>
                @endforelse
            </select>
        </div>
    </div>
    <div class="use4">
        <a>住所を選択</a>
        <div class="cp_ipselect cp_sl02" id="address">
            <select name="address" class="js-select-address">
                @forelse ($addressList as $address)
                <option value="{{$address}}">{{$address}}</option>
                @empty
                <option value="">---------</option>
                @endforelse
            </select>
        </div>
    </div>
    <div class="use4">
        <a>ご利用プランを選択</a>
        <div class="cp_ipselect cp_sl02" id="supplypoint_code">
            <select name="supplypoint_code" class="js-select-plan">
                @forelse ($contractList as $contract)
                <option value="{{$contract['supplypoint_code']}}">{{$contract['plan_name']}}</option>
                @empty
                <option value="">---------</option>
                @endforelse
            </select>
        </div>
    </div>
</div>
<div class="plan-btn">
    <button type="button" class="js-display-button">表示する</button>
</div>
