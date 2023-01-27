<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Facades\GetInvoice;

use App\Brand;

use App\Consts\SupplierConsts;

use App\Http\Controllers\ContractNoticeController;

class ContractInformationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // マイページの契約取得
        $contracts = GetInvoice::get_supplypoint_list(session('user_now.customer_code'));

        // サンキューレター出力対象取得
        $contract_notice = new ContractNoticeController();
        $check_contract = $contract_notice->check_thankyou_letter_exists(session('user_now.customer_code'));
        $supplypoints = [];
        if (!empty($check_contract)) {
            foreach ($check_contract as $value) {
                $supplypoints[] = $value->power_customer_location_number;
            }
        }

        $url_data = array();

        foreach ($contracts as $key => $contract) {
            // サンキューレター出力対象のみ約款情報を表示
            if(in_array($contract["supplypoint_code"], $supplypoints)) {
                $data = $this->get_url($contract);
                array_push($url_data, $data);
            }
        }

        return view('contract_information', [ "url_data" => $url_data ]);
    }


    public function get_url($contract)
    {

        $url_data = array();
        if (!empty($contract['brand_id'])) {
            $brand = Brand::where('id', $contract['brand_id'])->whereNull('deleted_at')->first();
        }

        if (!empty($brand)) {
            $explanation_url = $brand->explanation_url;
            if ($contract['pps_type'] == SupplierConsts::GRANDATA_ELECTRIC_INDEX || $contract['pps_type'] == SupplierConsts::HTB_ENERGY_ELECTRIC_INDEX) {
                $url_data['denki_name'] = $brand->name_printed;
            } elseif ($contract['pps_type'] == SupplierConsts::GRANDATA_GAS_INDEX || $contract['pps_type'] == SupplierConsts::SAISAN_GAS_INDEX || $contract['pps_type'] == SupplierConsts::FAMILY_NET_JAPAN_GAS_INDEX) {
                $url_data['gas_name'] = $brand->name_printed;
            }
        }

        if ($contract['pps_type'] == SupplierConsts::GRANDATA_ELECTRIC_INDEX) {
            if (empty($explanation_url)) {
                $url_data['denki_jusetu'] = 'https://grandata-service.jp/wp/wp-content/uploads/2022/06/B02_06.pdf';
            } else {
                $url_data['denki_jusetu'] = $explanation_url;
            }
            $url_data['denki_yakkan'] = 'https://grandata-service.jp/wp/wp-content/uploads/2022/03/terms_220330.pdf';
        } elseif ($contract['pps_type'] == SupplierConsts::HTB_ENERGY_ELECTRIC_INDEX) {
            if (empty($explanation_url)) {
                $url_data['denki_jusetu'] = 'https://grandata-service.jp/wp/wp-content/uploads/2022/06/B08-07.pdf';
            } else {
                $url_data['denki_jusetu'] = $explanation_url;
            }
            $url_data['denki_yakkan'] = 'https://grandata-service.jp/wp/wp-content/uploads/2021/02/terms/terms_2-1.pdf';
            $url_data['denki_shubetsu'] = 'https://grandata-service.jp/wp/wp-content/uploads/2021/02/terms/terms_3-1.pdf';
        } elseif ($contract['pps_type'] == SupplierConsts::SAISAN_GAS_INDEX) {
            if(empty($explanation_url)){
                $url_data['gas_jusetu'] = 'https://grandata-service.jp/wp/wp-content/uploads/2022/06/B09-08.pdf';
            } else {
                $url_data['gas_jusetu'] = $explanation_url;
            }
            $url_data['gas_yakkan'] = 'https://grandata-service.jp/wp/wp-content/uploads/2021/02/terms/terms_4-3.pdf';
        } elseif ($contract['pps_type'] == SupplierConsts::FAMILY_NET_JAPAN_GAS_INDEX) {
            if (substr($contract["supplypoint_code"], 0, 3) == '002') { // 大阪ガスエリア
                if (empty($explanation_url)) {
                    $url_data['gas_jusetu'] = 'https://grandata-service.jp/wp/wp-content/uploads/2022/06/B10-05.pdf';
                } else {
                    $url_data['gas_jusetu'] = $explanation_url;
                }
                $url_data['gas_yakkan'] = 'https://grandata-service.jp/wp/wp-content/uploads/2021/02/terms/terms_4-2.pdf';
                $url_data['gas_sasshi'] = 'https://my.ebook5.net/tokyo-ea/grd_toshigasbook_og/';
            } else {
                if (empty($explanation_url)) {
                    $url_data['gas_jusetu'] = 'https://grandata-service.jp/wp/wp-content/uploads/2022/06/B07-08.pdf';
                } else {
                    $url_data['gas_jusetu'] = $explanation_url;
                }
                $url_data['gas_yakkan'] = 'https://grandata-service.jp/wp/wp-content/uploads/2021/02/terms/terms_4-1.pdf';
                $url_data['gas_sasshi'] = 'https://my.ebook5.net/tokyo-ea/grd_toshigasbook_tg/';
            }
        } elseif ($contract['pps_type'] == SupplierConsts::GRANDATA_GAS_INDEX) {
            if (substr($contract["supplypoint_code"], 0, 3) == '700') { // 西部ガスエリア
                if (empty($explanation_url)) {
                    $url_data['gas_jusetu'] = 'https://grandata-service.jp/wp/wp-content/uploads/2022/06/B43-02.pdf';
                } else {
                    $url_data['gas_jusetu'] = $explanation_url;
                }
                $url_data['gas_yakkan'] = 'https://grandata-service.jp/wp/wp-content/uploads/2022/01/terms/gd_gusyakkan_tokyo_211216.pdf';
            } elseif (substr($contract["supplypoint_code"], 0, 3) == '004') { // 東邦ガスエリア
                if (empty($explanation_url)) {
                    $url_data['gas_jusetu'] = 'https://grandata-service.jp/wp/wp-content/uploads/2022/06/B43-02.pdf';
                } else {
                    $url_data['gas_jusetu'] = $explanation_url;
                }
                $url_data['gas_yakkan'] = 'https://grandata-service.jp/wp/wp-content/uploads/2022/03/terms/gd_gas_terms_0331.pdf';
            } else {
                if (empty($explanation_url)) {
                    $url_data['gas_jusetu'] = 'https://grandata-service.jp/wp/wp-content/uploads/2022/06/B43-02.pdf';
                } else {
                    $url_data['gas_jusetu'] = $explanation_url;
                }
                $url_data['gas_yakkan'] = 'https://grandata-service.jp/wp/wp-content/uploads/2022/06/gd_gas_terms_0606.pdf';
            }
            $url_data['gas_sasshi'] = 'https://grandata-service.jp/wp/wp-content/uploads/2021/10/terms/s_OL210922.pdf';
        }

        return $url_data;
    }
}
