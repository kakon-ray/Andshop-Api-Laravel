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
                    'msg' => 'Login Success',
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
        $arrayRequest = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'gender' => $request->gender,
            'address' => $request->email,
            'date_of_birth' => $request->email,
            'image' => $request->image,
        ];

        $arrayValidate  = [
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'regex:/^(?:\+?88|0088)?01[3-9]\d{8}$/',
            ],
            'email' => 'required|string|email|max:255|unique:users',
            'gender' => 'required',
            'address' => 'required|string|max:500',
            'date_of_birth' => 'required',
            'image.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $response = Validator::make($arrayRequest, $arrayValidate);

        if ($response->fails()) {
            $msg = '';
            foreach ($response->getMessageBag()->toArray() as $item) {
                $msg = $item;
            };

            return response()->json([
                'success' => false,
                'msg' => $msg[0]
            ]);
        }


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
            } else {
                $image = $userInfo->image;
            }

            $userInfo->name = $request->name;
            $userInfo->phone = $request->phone;
            $userInfo->email = $request->email;
            $userInfo->gender = $request->gender;
            $userInfo->address = $request->address;
            $userInfo->date_of_birth = $request->date_of_birth;
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

    public function user_request_personal_info_submit(Request $request)
    {

        $arrayRequest = [
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'id_card_front' => $request->id_card_front,
            'id_card_back' => $request->id_card_back,
            'role' => $request->role,

        ];

        $arrayValidate  = [
            'name' => 'required|string|max:255',
            'phone' => [
                'required',
                'regex:/^(?:\+?88|0088)?01[3-9]\d{8}$/',
            ],
            'email' => 'required|string|email|max:255|unique:users',
            'id_card_front.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'id_card_back.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'role' => 'required',
        ];

        $response = Validator::make($arrayRequest, $arrayValidate);

        if ($response->fails()) {
            $msg = '';
            foreach ($response->getMessageBag()->toArray() as $item) {
                $msg = $item;
            };

            return response()->json([
                'success' => false,
                'msg' => $msg[0]
            ]);
        }


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

            if ($request->id_card_front) {
                $file = $request->file('id_card_front');
                $filename = $slug . '.' . $file->getClientOriginalExtension();

                $img = Image::make($file);
                $img->resize(320, 240)->save(public_path('uploads/' . $filename));

                $host = $_SERVER['HTTP_HOST'];
                $id_card_front = "http://" . $host . "/uploads/" . $filename;
                $id_card_front = $id_card_front;
            } else {
                $id_card_front = $userInfo->id_card_front;
            }

            if ($request->id_card_back) {
                $file = $request->file('id_card_back');
                $filename = $slug . '.' . $file->getClientOriginalExtension();

                $img = Image::make($file);
                $img->resize(320, 240)->save(public_path('uploads/' . $filename));

                $host = $_SERVER['HTTP_HOST'];
                $id_card_back = "http://" . $host . "/uploads/" . $filename;
                $id_card_back = $id_card_back;
            } else {
                $id_card_back = $userInfo->id_card_back;
            }

            $userInfo->name = $request->name;
            $userInfo->phone = $request->phone;
            $userInfo->email = $request->email;
            $userInfo->id_card_front = $id_card_front;
            $userInfo->id_card_back = $id_card_back;
            $userInfo->role = $request->role;
            $userInfo->save();
            DB::commit();
        } catch (\Exception $err) {
            $userInfo = null;
            return response()->json([
                'error' => 'Internal Server Error',
                'err_msg' => $err->getMessage()
            ]);
        }

        if ($userInfo != null) {
            return response()->json([
                'success' => true,
                'msg' => 'Thanks for your request I will contact you',
                'user' => $userInfo
            ]);
        } 
    }


}
