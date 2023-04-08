<?php

namespace App\Http\Traits;


trait ResponseTrait
{
    public function responseData($data_key ,$data ,$token ,$error ,$message ,$status_code)
    {
        return response()->json([
            $data_key => $data,
            'token' => $token,
            'error' => $error,
            'message' => $message
        ] ,$status_code);
    }

    public function responseError($message ,$status_code)
    {
        return response()->json([
            'error' => 'true',
            'message' => $message
        ] ,$status_code);
    }
    public function responseSuccess($message ,$status_code)
    {
        return response()->json([
            'error' => 'false',
            'message' => $message
        ] ,$status_code);
    }

}