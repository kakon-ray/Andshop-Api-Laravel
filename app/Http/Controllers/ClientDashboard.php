<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\UserBasic;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;


class ClientDashboard extends Controller
{

    public function __construct()
    {
        \Config::set('auth.defaults.guard', 'userbasic');
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

                $userData = [
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

    public function user_show(Request $request)
    {
        $user = UserBasic::where('id', $request->id)->first();
        if ($user) {
            return response()->json([
                'success' => true,
                'user' => $user,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'user' => 'Do not find any user',
            ]);
        }
    }

    public function user_update(Request $request)
    {

        $userInfo = UserBasic::find($request->id);

        if (!$userInfo) {
            return response()->json([
                'success' => false,
                'user' => 'Do not find any user',
            ]);
        }

        try {

            DB::beginTransaction();

            $slug = Str::slug($request->name . '-' . hexdec(uniqid()));

            if ($request->image) {
                $file = $request->file('image');
                $filename = $slug . '.' . $file->getClientOriginalExtension();

                $img = Image::make($file);
                $img->resize(320, 240)->save(public_path('uploads/' . $filename));

                $host = $_SERVER['HTTP_HOST'];
                $image = "http://" . $host . "/uploads/" . $filename;
                $image = $image;
            }else{
                $image = $userInfo->image;
            }

            $userInfo->name = $request->name;
            $userInfo->phone = $request->phone;
            $userInfo->email = $request->email;
            $userInfo->gender = $request->gender;
            $userInfo->address = $request->address;
            $userInfo->image = $image;
            $userInfo->save();
            DB::commit();
        } catch (\Exception $err) {
            $userInfo = null;
        }

        if ($userInfo != null) {
            return response()->json([
                'success' => true,
                'msg' => 'User Updated',
                'user' => $userInfo
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => 'Internal Server Error'
            ]);
        }
    }
}
