<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;

class AuthController extends Controller
{
    /**
     * Registration Req
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);
        if ($validator->fails()) {  
            return response()->json([
                "status" => false,
                "errors" => $validator->errors()
            ], 400);  
        }

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => Carbon::now(),
                'password' => bcrypt($request->password)
            ]);
      
            return response()->json([
                'success' => true,
                'message' => 'Registration successfull. Please login to proceed further.',
                'data' => []
            ], 200);
        } catch(\Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'data' => []
            ], 500);
        }
    }
  
    /**
     * Login Req
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {  
            return response()->json([
                "status" => false,
                "errors" => $validator->errors()
            ], 400);  
        }

        try {
            $data = [
                'email' => $request->email,
                'password' => $request->password
            ];
    
            if (auth()->attempt($data)) {
                $token = auth()->user()->createToken('LaravelAngular')->accessToken;
                return response()->json([
                    'success' => true,
                    'message' => 'Login Successfull',
                    'token' => $token,
                    'data' => auth()->user()
                ], 200);
            } else {
                return response()->json(['success' => false,
                'message' => 'Login Details Not Found',
                'data' => []
            ], 200);
            }
        } catch (\Exception $ex) {
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error',
                'data' => []
            ], 500);
        }
    }
 
    public function userInfo() 
    {
 
     $user = auth()->user();
      
     return response()->json(['user' => $user], 200);
 
    }
}
