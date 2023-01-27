<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * 管理画面上でDBをトグル切り替え
 */
class AdminToggleDBController extends Controller
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
        Log::debug("AdminToggleDBController : index");
        // セッションの値を全て取得
        // $data = Session::all();
        // Log::debug($data);
        if ( $request->session()->get('db_accesspoint_now', '0') == 1 ) {
            $request->session()->put('db_accesspoint_now', '2');
        } else {
            $request->session()->put('db_accesspoint_now', '1');
        }
        return redirect( url()->previous() )->with('status', 'Database切り替えを実施しました');
        // return redirect('/home')->with('status', 'Database切り替えを実施しました');
    }

}
