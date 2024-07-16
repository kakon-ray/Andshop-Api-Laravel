<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\UserBasic;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function __construct()
    {
        \Config::set('auth.defaults.guard','admin');
    }


    function regisign_upster(Request $request)
    {

        $user_exist_userbasic = Admin::where('email', $request->email)->count();
     


        if ($user_exist_userbasic) {
            return response()->json([
                'msg' => 'User already exists',
            ]);
        }else {


            try {

                $user = Admin::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);

                
            } catch (\Exception $err) {
                $user = null;
            }

            if ($user != null) {
                return response()->json(['msg' => 'Registation Completed'], 200);
            } else {
                return response()->json([
                    'msg' => 'Internal Server Error',
                    'err_msg' => $err->getMessage()
                ], 500);
            }
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $UserBasic = DB::table('admins')->where('email', $request->email)->first();

        if ($UserBasic == true) {
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            if (!$token = auth()->attempt($validator->validated())) {
                return response()->json(['error' => 'Unauthorize']);
            } else {
                $user_basic = Admin::find($UserBasic->id);
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
}
