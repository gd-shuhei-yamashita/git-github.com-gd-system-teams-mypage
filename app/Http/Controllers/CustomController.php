<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

class CustomController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * ajax用のレスポンスをカスタマイズ
     * @param mixed $responseData
     * @return json
     */
    public function customAjaxResponse($responseData)
    {
        return response()->json([
            'message' => 'SUCCESS',
            'token'  => csrf_token(),
            'data'   => $responseData
        ]);
    }

    /**
     * ajax用のレスポンスをカスタマイズ
     * @param mixed $responseData
     * @return json
     */
    public function customAjaxError($responseData)
    {
        return response()->json([
            'message' => 'NG',
            'token'  => csrf_token(),
            'data'   => $responseData
        ]);
    }


    /**
     * トークンを再発行
     * @return json
     */
    public function getCsrfToken(Request $request)
    {
        return $this->customAjaxResponse([]);
    }
}
