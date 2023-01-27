{{-- ホーム画面 --}}
@extends('renewal.layout.app')

@section('title', '［開発用］Mallieエントリー')

{{-- load css --}}
@section('pageCss')
<link href="{{asset('css/renewal/common.css') }}" rel="stylesheet">
{{-- <link href="{{asset('css/renewal/delivery.css') }}" rel="stylesheet"> --}}
@endsection

{{-- body_header --}}
@include('renewal.layout.bodyheader')

{{-- body_contents --}}
@section('content')
<style>
.l-main input {
    border: solid 1px #666;
    outline: none;
    margin: 2px 0;
}
</style>

<form class="l-main" method="post">
    <input type="hidden" name="_token" value="{{csrf_token()}}">
    <h2>契約者情報</h2>

    <table class="table">
        <tr>
            <th>顧客コード</th>
            <td>
                <input type="text" name="code" value="{{$code}}" required>
            </td>
        </tr>
        <tr>
            <th>契約者名</th>
            <td>
                <input type="text" name="last_name" value="山田" required>
                <input type="text" name="first_name" value="太郎" required>
            </td>
        </tr>
        <tr>
            <th>フリガナ</th>
            <td>
                <input type="text" name="last_name_kana" value="ヤマダ" required>
                <input type="text" name="first_name_kana" value="タロウ" required>
            </td>
        </tr>
        <tr>
            <th>生年月日</th>
            <td>
                <select name="birth_year">
                <option value="1950">1950年</option>
                <option value="1960">1960年</option>
                <option value="1970">1970年</option>
                <option value="1980">1980年</option>
                <option value="1990">1990年</option>
                <option value="2000" selected>2000年</option>
                <option value="2010">2010年</option>
                <option value="2020">2020年</option>
                </select>
                <select name="birth_month">
                @for ($m=1;$m<=12;$m++)
                <option value="{{$m}}">{{$m}}月</option>
                @endfor
                </select>
                <select name="birth_date">
                @for ($d=1;$d<=31;$d++)
                <option value="{{$d}}">{{$d}}日</option>
                @endfor
                </select>
            </td>
        </tr>
        <tr>
            <th>メールアドレス</th>
            <td>
                <input type="text" name="mail_address" value="">
            </td>
        </tr>
        <tr>
            <th>住所</th>
            <td>
                〒<input type="text" name="zip_code" value="171-0022" required style="width:100px;"><br>
                <input type="text" name="prefecture" value="東京都" required style="width:80px;" placeholder="都道府県">
                <input type="text" name="city" value="豊島区" required style="width:120px;">
                <input type="text" name="town" value="南池袋" required style="width:120px;">
                <input type="text" name="street_number_choume" value="二丁目" style="width:120px;">
                <input type="text" name="street_number_banchi" value="9番9号" style="width:120px;">
                <input type="text" name="building_name" value="" placeholder="ビル名">
            </td>
        </tr>
        <tr>
            <th>電話番号</th>
            <td>
                <input type="text" name="mobile_phone" value="09012345678">
            </td>
        </tr>
        <tr>
            <th>ログインID<br>※使用しない？</th>
            <td>
                <input type="text" name="login_id" value="">
            </td>
        </tr>
        <tr>
            <th>パスワード</th>
            <td>
                @php
                    $ramdomPassword = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz0123456789"), 0, 8);
                @endphp
                <input type="text" name="login_password" value="{{$ramdomPassword}}">
            </td>
        </tr>
    </table>
    <br><br>
    <h2>契約プラン情報</h2>
    <table class="table">
        <tr>
            <th>申し込み日</th>
            <td>
                <input type="date" name="apply_date" value="{{ date('Y-m-d') }}" required>
            </td>
        </tr>
        <tr>
            <th>プラン</th>
            <td>
                <select name="plan" required>
                @foreach ($planlist as $plan)
                    @if ($plan->id)
                    <option value="{{$plan->id}}">{{$plan->name}}</option>
                    @endif
                @endforeach
                </select>
            </td>
        </tr>
        <tr>
            <th>使用場所</th>
            <td>
                <label><input type="radio" name="power_location_address_type" value="1" checked>契約者と同じ</label><br>
                <label><input type="radio" name="power_location_address_type" value="2">契約者と異なる</label>
            </td>
        </tr>
        <tr>
            <th>名義<br>※使用場所が契約者と異なる場合のみ記入</th>
            <td>
                <input type="text" name="power_customer_name" value="山田太郎">
            </td>
        </tr>
        <tr>
            <th>名義カナ<br>※使用場所が契約者と異なる場合のみ記入</th>
            <td>
                <input type="text" name="power_customer_name_kana" value="ヤマダタロウ">
            </td>
        </tr>
        <tr>
            <th>住所<br>※使用場所が契約者と異なる場合のみ記入</th>
            <td>
                〒<input type="text" name="power_zip_code" value="" style="width:100px;"><br>
                <input type="text" name="power_prefecture" value="" style="width:80px;" placeholder="都道府県">
                <input type="text" name="power_city" value="" style="width:120px;">
                <input type="text" name="power_town" value="" style="width:120px;">
                <input type="text" name="power_street_number_choume" value="" style="width:120px;">
                <input type="text" name="power_street_number_banchi" value="" style="width:120px;">
                <input type="text" name="power_building_name" value="" placeholder="ビル名">
            </td>
        </tr>
        <tr>
            <th>建物区分</th>
            <td>
                <select name="power_building_type">
                <option value="1">戸建住宅(個人)</option>
                <option value="2">集合住宅(個人)</option>
                <option value="3">店舗または事務所(法人・屋号)</option>
                <option value="4">店舗兼住宅(法人・屋号)</option>
                <option value="5">共用部</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>書類送付先</th>
            <td>
                <label><input type="radio" name="document_address_type" value="1" checked>契約者と同じ</label><br>
                <label><input type="radio" name="document_address_type" value="2">契約者と異なる</label>
            </td>
        </tr>
        <tr>
            <th>送付先名<br>※書類送付先が契約者と異なる場合のみ記入</th>
            <td>
                <input type="text" name="document_addressee" value="山田太郎">
            </td>
        </tr>
        <tr>
            <th>住所<br>※書類送付先が契約者と異なる場合のみ記入</th>
            <td>
                〒<input type="text" name="document_zip_code" value="" style="width:100px;"><br>
                <input type="text" name="document_prefecture" value="" style="width:80px;" placeholder="都道府県">
                <input type="text" name="document_city" value="" style="width:120px;">
                <input type="text" name="document_town" value="" style="width:120px;">
                <input type="text" name="document_street_number_choume" value="" style="width:120px;">
                <input type="text" name="document_street_number_banchi" value="" style="width:120px;">
                <input type="text" name="document_building_name" value="" placeholder="ビル名">
            </td>
        </tr>
    </table>
    <div id="form_submit">
    <button class="btn" type="submit">登録する</button>
    </div>

</form>

@endsection

{{-- load js --}}
@section('pageJs')
<script src="{{asset('js/renewal/common.js') }}"></script>
@endsection

{{-- footer --}}
@section('footer')
@include('renewal.layout.footer_login')
@endsection

