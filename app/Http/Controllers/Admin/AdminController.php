<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\CustomController;

/**
 * 管理画面用ベースコントローラ
 */
class AdminController extends CustomController
{

    protected $adminCustomerCode;
    protected $adminUserId;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->adminUserId = Session::get('user_login.id', 0);
        $this->adminCustomerCode = Session::get('user_login.customer_code');
    }

}
