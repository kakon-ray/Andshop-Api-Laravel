<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\UserBasic;
use Illuminate\Support\Facades\Validator;

class ClientDashboard extends Controller
{

    public function __construct()
    {
        \Config::set('auth.defaults.guard','userbasic');
    }
    

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $UserBasic = DB::table('user_basic')->where('email', $request->email)->first();

        if ($UserBasic == true) {
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            if (!$token = auth()->attempt($validator->validated())) {
                return response()->json(['error' => 'Unauthorize']);
            } else {
                $user_basic = UserBasic::find($UserBasic->id);
                $user_basic->remember_token = $token;
                $user_basic->save();

                $userData=[
                    'success' => true,
                    'message' => 'Login Success',
                    'token' => $token,
                    'id' => $UserBasic->id,
                    'name' => $UserBasic->name,
                    'email' => $UserBasic->email,
                    'image' => $UserBasic->image,
                    'token_type' => 'bearer'
                  ];

                  return response()->json($userData);
            }
        } else {
            return response()->json([
                'success' => false,
                'ErrorMessage' => 'Email address not match',
            ]);
        }
    }

    public function user_show(Request $request){
        $user = UserBasic::where('id',$request->id)->first();
        if($user){
            return response()->json([
                'success' => true,
                'user' => $user,
            ]);
        }else{
            return response()->json([
                'success' => false,
                'user' => 'Do not find any user',
            ]);
        }
    }


}
