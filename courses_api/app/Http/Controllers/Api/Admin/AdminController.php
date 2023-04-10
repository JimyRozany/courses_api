<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTrait;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;



class AdminController extends Controller
{
    use ResponseTrait;
    /* ---------- Admin Register -------------------- */
    public function adminRegister(Request $request)
    {
        try {
            $validate = $request->validate([
                'user_name'=>'required',
                'email'=>'required|email',
                'password'=>'required',
                'phone'=>'required|numeric',
            ]);
           try {


            $admin = Admin::create([
                'user_name'=>$request->input('user_name'),
                'email'=>$request->input('email'),
                'phone'=>$request->input('phone'),
                'password'=>bcrypt($request->input('password')),
            ]);

            return $this->responseSuccess('registration successfully' ,201);


           } catch (\Throwable $th) {
            return $this->responseError("user already exits" ,422);
           }
        } catch (\Throwable $th) {

            return $this->responseError($th->getMessage() ,400);
        }
        
        // if( $validate ){
        //     try {
        //         $user = User::create([
        //             'user_name'=>$request->input('user_name'),
        //             'email'=>$request->input('email'),
        //             'phone'=>$request->input('phone'),
        //             'premium'=>$request->input('premium'),
        //             'password'=>bcrypt($request->input('password')),
        //         ]);
        //         return $this->responseSuccess('registration successfully' ,201);
        //     } catch (\Exception $ex) {
        //         return $this->responseError($ex->getMessage() ,$ex->getCode());
        //     }
        // }else{
        //     return $this->responseError('registration error' ,417);
        // }
        
    }
    
    /* ---------- Admin Login -------------------- */
     public function adminLogin(Request $request)
     {
         try{
             // validation
             $request->validate([
                 'email' => 'required|email',
                 'password' => 'required',
 
             ]) ;
             $credentials = $request->only(['email' ,'password']);
             // generate toke
             $token = Auth::guard('admin-api')->attempt($credentials);
     
             if(!$token){
                 return $this->responseError('user not found',404);
             }
     
             $admin = Auth::guard('admin-api')->user();
             return $this->responseData('admin' ,$admin ,$token ,false ,'login successfully' ,200);
                 
         }catch(\Exception $ex){
             return $this->responseError($ex->getMessage(),200);
         }
         
     }

    /* ---------- Admin Logout -------------------- */
     public function adminLogout(Request $request)
     {
        $token = $request->input('token');
         
        if($token)
        {
         try {
             JWTAuth::setToken($token)->invalidate();
             return $this->responseSuccess('logged out successfully' ,200);
         } catch (TokenInvalidException $ex) {
             return $this->responseError('invalid Token',498);
         }
        }else{
         return $this->responseError('invalid Token',498);
        }
     }
}
