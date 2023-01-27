<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\CustomController;

class LoginedController extends CustomController
{

    protected $customerCode;
    protected $userId;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->userId = Session::get('user_now.id', 0);
        $this->customerCode = Session::get('user_now.customer_code');
    }

}
