<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App;
use App\Models\DB\User;
use App\Models\Mallie\CustomerOrdered;
use App\Models\Mallie\HalueneContract;

/**
 * 開発用コントローラ
 */
class DevelopmentController extends AdminController
{
    /**
     * Create Malle data form for test.
     * @param Illuminate\Http\Request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function entry(Request $request)
    {
        $maxRecord = CustomerOrdered::selectRaw('code')->where('code', 'LIKE', 'MC%')->where('code', '!=', 'MC99999999')->orderBy('code', 'desc')->first();
        $nextCode = 'MC'. sprintf('%08d', intval(preg_replace('/[^0-9]/', '', $maxRecord->code)) + 1);
        $plan = DB::connection('mysql_mallie')->table('HaluenePowerPlan')->get();
        return view('renewal.admin.development.entry', ['planlist' => $plan, 'code' => $nextCode]);
    }

    /**
     * Create Malle data form for test.
     * @param Illuminate\Http\Request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function entry_complete(Request $request)
    {
        if (App::environment('product')) {
            return back()->withInput()->with('status', '本番環境では実行できません。');
        }
        try {
            $DB = DB::connection('mysql_mallie');
            $DB->beginTransaction();
            // 契約者登録
            $phone_num = $request->get('mobile_phone');
            $CustomerOrdered = new CustomerOrdered;
            $CustomerOrdered->code = $request->get('code');
            $CustomerOrdered->customer_type = 1;
            $CustomerOrdered->last_name = $request->get('last_name');
            $CustomerOrdered->first_name = $request->get('first_name');
            $CustomerOrdered->last_name_kana = $request->get('last_name_kana');
            $CustomerOrdered->first_name_kana = $request->get('first_name_kana');
            $CustomerOrdered->birthday = $request->get('birth_year').'-'.$request->get('birth_month').'-'.$request->get('birth_date');
            $CustomerOrdered->mail_address = $request->get('mail_address') ? $request->get('mail_address') : '';
            $CustomerOrdered->zip_code = $request->get('zip_code');
            $CustomerOrdered->address_code = str_replace('-', '', $request->get('zip_code')). '00';
            $CustomerOrdered->prefecture = $request->get('prefecture');
            $CustomerOrdered->city = $request->get('city');
            $CustomerOrdered->town = $request->get('town');
            $CustomerOrdered->street_number_choume = $request->get('street_number_choume');
            $CustomerOrdered->street_number_banchi = $request->get('street_number_banchi');
            $CustomerOrdered->building_name = $request->get('building_name');
            $CustomerOrdered->mobile_phone =  substr($phone_num, 0, 3) . '-' . substr($phone_num, 3, 4) . '-' . substr($phone_num, 7, 4);
            $CustomerOrdered->login_id = $request->get('login_id') ? $request->get('login_id') : NULL;
            $CustomerOrdered->login_password = $request->get('login_password') ? $request->get('login_password') : NULL;
            $CustomerOrdered->creater = 'DEVELOPMENT_TEST';
            $CustomerOrdered->updater = 'DEVELOPMENT_TEST';
            $CustomerOrdered->save();
            $id = $DB->getPdo()->lastInsertId();
            // 契約登録
            $maxRecord = HalueneContract::selectRaw('code')->where('code', 'LIKE', 'HCLOCAL%')->orderBy('code', 'desc')->first();
            $nextNumber = sprintf('%04d', intval(preg_replace('/[^0-9]/', '', $maxRecord->code)) + 1);
            $maxRecord = HalueneContract::selectRaw('apply_number')->where('apply_number', 'LIKE', 'DENKIT%')->orderBy('apply_number', 'desc')->first();
            $nextApplyNumber = 'DENKIT'. sprintf('%07d', intval(preg_replace('/[^0-9]/', '', $maxRecord->apply_number)) + 1);
            $plan = DB::connection('mysql_mallie')->table('HaluenePowerPlan')->find($request->get('plan'));
            $HalueneContract = new HalueneContract;
            $HalueneContract->code = 'HCLOCAL'.$nextNumber;
            $HalueneContract->order_id = 'HCODLOCAL'.$nextNumber;
            $HalueneContract->pps_business_number = 'A0476';
            $HalueneContract->apply_number = $nextApplyNumber;
            $HalueneContract->application_type = 1;// 1：他社からの切替 2：新規入居 3：譲渡 4：移転
            $HalueneContract->pre_contract_id = 0;
            $HalueneContract->contract_type = 31;// 10:自社請求 20:SBS債権譲渡 21:SBS請求代行 30:他社債権譲渡 31:他社請求代行
            $HalueneContract->customer_id = $id;
            $HalueneContract->payment_id = 0; // TODO：PaymentOrderedのレコード作成
            $HalueneContract->power_plan_id = $plan->id;
            $HalueneContract->contract_capacity = $plan->contract_capacity;
            $HalueneContract->power_supplier_type = 99;
            $HalueneContract->power_customer_number = $id; // とりあえず
            $HalueneContract->power_customer_location_number = '';
            $HalueneContract->power_location_address_type = $request->get('power_location_address_type');
            if ($request->get('power_location_address_type') == '1') {
                $HalueneContract->power_zip_code = $request->get('zip_code');
                $HalueneContract->power_address_code = str_replace('-', '', $request->get('zip_code')). '00';
                $HalueneContract->power_prefecture = $request->get('prefecture');
                $HalueneContract->power_city = $request->get('city');
                $HalueneContract->power_town = $request->get('town');
                $HalueneContract->power_street_number_choume = $request->get('street_number_choume');
                $HalueneContract->power_street_number_banchi = $request->get('street_number_banchi');
                $HalueneContract->power_building_name = $request->get('building_name');
                $HalueneContract->power_customer_name = $request->get('last_name'). $request->get('first_name');
                $HalueneContract->power_customer_name_kana =$request->get('last_name_kana'). $request->get('first_name_kana');
            } else {
                $HalueneContract->power_zip_code = $request->get('power_zip_code');
                $HalueneContract->power_address_code = str_replace('-', '', $request->get('power_zip_code')). '00';
                $HalueneContract->power_prefecture = $request->get('power_prefecture');
                $HalueneContract->power_city = $request->get('power_city');
                $HalueneContract->power_town = $request->get('power_town');
                $HalueneContract->power_street_number_choume = $request->get('power_street_number_choume');
                $HalueneContract->power_street_number_banchi = $request->get('power_street_number_banchi');
                $HalueneContract->power_building_name = $request->get('power_building_name');
                $HalueneContract->power_customer_name = $request->get('power_customer_name');
                $HalueneContract->power_customer_name_kana = $request->get('power_customer_name_kana');
            }
            $HalueneContract->power_customer_name_hankaku_kana = mb_convert_kana($HalueneContract->power_customer_name_kana, 'Vk');
            $HalueneContract->power_building_type = $request->get('power_building_type');
            $HalueneContract->document_address_type = $request->get('document_address_type');
            if ($request->get('document_address_type') == '1') {
                $HalueneContract->document_zip_code = $request->get('zip_code');
                $HalueneContract->document_address_code = str_replace('-', '', $request->get('zip_code')). '00';
                $HalueneContract->document_prefecture = $request->get('prefecture');
                $HalueneContract->document_city = $request->get('city');
                $HalueneContract->document_town = $request->get('town');
                $HalueneContract->document_street_number_choume = $request->get('street_number_choume');
                $HalueneContract->document_street_number_banchi = $request->get('street_number_banchi');
                $HalueneContract->document_building_name = $request->get('building_name');
                $HalueneContract->document_addressee = $request->get('last_name'). $request->get('first_name');
            }
            $HalueneContract->apply_date = $request->get('apply_date');
            $HalueneContract->contract_date = date('Y-m-d');
            $HalueneContract->needs_electricity_usage_notice = 0;
            $HalueneContract->needs_thank_you_letter = 0;
            $HalueneContract->status = 1;
            $HalueneContract->creater = 'DEVELOPMENT_TEST';
            $HalueneContract->updater = 'DEVELOPMENT_TEST';
            $HalueneContract->save();
            $DB->commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->withInput()->with('status', '登録に失敗しました');
        }
        return back()->withInput()->with('status', '登録しました');
    }
}