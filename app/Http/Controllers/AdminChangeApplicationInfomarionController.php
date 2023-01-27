<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * 申込情報表示、変更画面 (用いない予定)
 * <div class="section">・申込情報を表示する<br/>
 * ・表示する情報は申込情報として日次で登録された情報<br/>
 * ・情報変更はマリーの改修が発生するため今回スコープ外<br/>
 */
class AdminChangeApplicationInfomarionController extends Controller
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
        // ユーザー情報を取得
        $users = $request->session()->get('users', array());
        Log::debug($users);
        return view('admin/change_application_infomarion');
    }
}
