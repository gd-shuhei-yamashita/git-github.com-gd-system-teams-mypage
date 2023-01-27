{{$data['name']}}　様<br>
いつも（株）グランデータのサービスをご愛顧賜り<br>
誠にありがとうございます。<br>
ご契約プランについて以下の手続きを承りました。<br>
<br>
※このメールは送信専用です。<br>
ご返信いただきました場合に回答できませんのでご了承ください。<br>
<br>
――――――――――――――――――――<br>
解約するサービス：
@if($data['service'] == 'electric')
電気
@elseif($data['service'] == 'gas')
ガス
@elseif($data['service'] == 'electric_gas')
電気＋ガス
@endif
<br>
解約理由：
@if($data['reason'] == 'moving')
引っ越し
@elseif($data['reason'] == 'price')
料金
@elseif($data['reason'] == 'customer')
カスタマの対応に不満
@elseif($data['reason'] == 'mypage')
マイページに不満
@elseif($data['reason'] == 'solicit')
他社の勧誘
@elseif($data['reason'] == 'other')
その他
@endif
<br>
解体・立ち合いの有無：
@if($data['meter'] == '1')
有り
@elseif($data['meter'] == '0')
無し
@endif
<br>
引っ越しを選択した方：<br>
@if($data['moving'] == '1')
引っ越し先での利用：引っ越し先でも使用する<br>
使用する場合のご利用開始日：{{ $data['start_year'] }}年{{ $data['start_month'] }}月{{ $data['start_day'] }}日<br>
引っ越し先住所：<br>
〒{{ $data['new_postal'] }}<br>
{{ $data['new_add'] }}<br>
{{ $data['new_build'] }}<br>
@elseif($data['moving'] == '0')
引っ越し先での利用：引っ越し先では使用しない<br>
@endif
電気の最終利用日：
@if($data['service'] == 'electric' || $data['service'] == 'electric_gas')
{{ $data['electric_last_year'] }}年{{ $data['electric_last_month'] }}月{{ $data['electric_last_day'] }}日
@endif
<br>
ガスの最終利用日：
@if($data['service'] == 'gas' || $data['service'] == 'electric_gas')
{{ $data['gas_last_year'] }}年{{ $data['gas_last_month'] }}月{{ $data['gas_last_day'] }}日
@endif
<br>
供給地点番号：{{ $data['supplypoint_code'] }}<br>
お客様番号：{{ $data['customer_num'] }}<br>
契約名義：　{{ $data['name'] }}様<br>
ご連絡先電話番号：{{ $data['phone'] }}<br>
契約プラン名：{{ $data['plan_name'] }}<br>
ご契約中の住所：{{ $data['add'] }}<br>
解約後の住所変更・請求書の送付先：<br>
〒{{ $data['postal_send'] }}<br>
{{ $data['add_send'] }}<br>
{{ $data['build_send'] }}<br>
手続き後のご連絡先：{{ $data['tel'] }}<br>
申し込み完了通知を受信するE-mail：{{ $data['mail'] }}<br>
<br>
※最終ご請求月は、解約月の最大２ヵ月後になります。<br>
――――――――――――――――――――<br>
<br>
最短4営業日にて解約処理をおこなわせていただきます。<br>
被せてメールにて解約手続き完了のお知らせを通知いたします。<br>
<br>
また、以下に該当の方は、5営業日以内にカスタマーセンターより折り返しご連絡いたします。<br>
・電気の解約や引っ越しの方で、入力内容に確認事項がある場合。<br>
・ガスの解約や引っ越しの手続きの方。<br>
<br>
完了まで今しばらくお待ちいただけますよう<br>
よろしくお願い申し上げます。<br>
<br>
――――――――――――――――――――――――――――――――――――――――<br>
株式会社グランデータ<br>
〒171-0022 東京都豊島区南池袋二丁目9番9号<br>
URL：https://grandata-service.jp/<br>
カスタマーセンター：0570-070-336<br>
――――――――――――――――――――――――――――――――――――――――<br>
