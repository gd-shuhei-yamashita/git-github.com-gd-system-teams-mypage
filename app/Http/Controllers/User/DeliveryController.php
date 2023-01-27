<?php

namespace App\Http\Controllers\User;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use App\Models\Services\Mobile;


class DeliveryController extends LoginedController
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function wifi(Request $request)
    {
        $Mobile = Mobile::getInstance();
        if ($Mobile->hasContract()) {
            $contract = $Mobile->getContract();
            $date = $contract['delivery_date'];
            $time = $contract['delivery_time'];
            $request->session()->put('wifi_delivery_date', $date);
            $request->session()->put('wifi_delivery_time', $time);
        } else {
            return redirect()->route('home');
        }

        return view('renewal.delivery.wifi', [
            'date' => $date,
            'time' => $time
        ]);
    }


}
