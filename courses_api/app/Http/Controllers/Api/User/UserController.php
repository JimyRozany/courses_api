<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Traits\ResponseTrait;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;


class UserController extends Controller
{
    use ResponseTrait;

    /* ---------- User Register -------------------- */
    public function userRegister(Request $request)
    {
        try {
            $validate = $request->validate([
                'user_name'=>'required',
                'email'=>'required|email',
                'password'=>'required',
                'phone'=>'required|numeric',
                'premium'=>'required'
            ]);
           try {
            $user = User::create([
                'user_name'=>$request->input('user_name'),
                'email'=>$request->input('email'),
                'phone'=>$request->input('phone'),
                'premium'=>$request->input('premium'),
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
    
    /* ---------- User Login -------------------- */
     public function userLogin(Request $request)
     {
         try{
             // validation
             $request->validate([
                 'email' => 'required|email',
                 'password' => 'required',
 
             ]) ;
             $credentials = $request->only(['email' ,'password']);
             // generate toke
             $token = Auth::guard('user-api')->attempt($credentials);
     
             if(!$token){
                 return $this->responseError('user not found',404);
             }
     
             $user = Auth::guard('user-api')->user();
             return $this->responseData('user' ,$user ,$token ,false ,'login successfully' ,200);
                 
         }catch(\Exception $ex){
             return $this->responseError($ex->getMessage(),200);
         }
         
     }

    /* ---------- User Logout -------------------- */
     public function userLogout(Request $request)
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

    /* ---------- User Update Password & Phone -------------------- */
    public function userUpdate(Request $request)
    {

        $user = auth::user();
        return $user;

        try {
            $request->validate([
                'password'=>'required',
                'phone'=>'required|numeric',
            ]);
            User::where('id' ,$user->id)->update([
                'password' => bcrypt($request->input('password')),
                'phone' => $request->input('phone')
            ]);

            return $this->responseSuccess('updated' ,200);

            
        }catch(\Exception $ex){
            return $this->responseError($ex->getMessage(),400);

        }

    }

    /* ---------- User Delete Account -------------------- */
    public function deleteAccount(Request $request)
    {
        $user_id = auth::user()->id;

        try{
            $user = User::findOrFail($user_id);
            $user->delete();
            return $this->responseSuccess('deleted successe' ,200);
        }catch(\Exception $ex){
            return $this->responseError($ex->getMessage(),$ex->getCode());
        }
    }



}
