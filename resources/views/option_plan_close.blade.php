{{-- オプション解約申し込み画面 --}}
@extends('layout.t_common')

@section('title','オプション解約申し込み')

@section('pageCss')
<link href="{{asset('css/reset.css') }}" rel="stylesheet" type="text/css">
<link href="{{asset('css/style.css') }}" rel="stylesheet" type="text/css">

@endsection

{{-- 標準ヘッダ --}}
@include('layout.t_head')

{{-- body_header --}}
@include('layout.t_bodyheader2')

{{-- body_contents --}}
@section('content')

        <div class="l-main">
            <h2>オプション解約申し込み<div class=" h2-border"></div></h2>
            <div class="input-field">
            @if(empty($data))
            <p>ご指定のオプション契約情報がありません。</p>
            @elseif(!empty($data['complete']))
            <p>オプション解約のお申し込みを受け付けました。</p>
            @else
            <form class="close_reason" name="option_close" action="{{ route('option_close_confirm') }}" method="post">
                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                <input type="hidden" name="option_contract_id" value="{{ $data['option_contract_id'] }}" />
                ご契約中オプション　解約申し込み<br>
                <br>
                オプション名<br>
                {{ $data['name'] }}　ご契約期間：{{ $data['contract_date'] }}～{{ $data['close_date'] }}<br>
                <br>
                @foreach($data['close_reason_list'] as $key => $close_reason)
                <label class="close_reason_item" for="close_reason_{{ $key }}"><input id="close_reason_{{ $key }}" type="radio" name="close_reason" value="{{ $key }}">{{ $close_reason }}</label>
                @endforeach
                <br>
                <span id="" class="help-block red-text" style="font-weight: bolder;">{{$errors->first('close_reason')}}</span>
                <textarea class="option_close_textarea" name="close_reason_other" rows="4" cols="40"></textarea>
                <br>
                <span id="" class="help-block red-text" style="font-weight: bolder;">{{$errors->first('close_reason_other')}}</span>
                <span id="" class="help-block red-text" style="font-weight: bolder;">{{$errors->first('option_contract_id')}}</span>
                <div class="option_close_submit">
                    <input class="option_close_btn" type="submit" value="解約を申し込む">
                </div>
            </form>
            
            @endif
            </div>

@include('layout.t_copyright2')
@yield('copyright2')
        </div>





@endsection

{{-- pageJs section は、t_footerに入ります。 --}}
@section('pageJs')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>

<script src="{{asset('js/style.js') }}"></script>
@endsection

{{-- t_footer ログイン後のものはナビゲーション周りで相違あるため別のフッタ－を用いる --}}
@include('layout.t_footer_login')
