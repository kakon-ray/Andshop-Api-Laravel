<?php

namespace App\Http\Controllers;


use App\Customs\Services\EmailVerificationService;
use App\Models\Admin;
use App\Models\EmailVerification;
use App\Models\User;
use App\Models\UserBasic;
use App\Models\UserbasicTemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RegController extends Controller
{

    public function __construct(private EmailVerificationService $service)
    {
    }


    function regisign_upster(Request $request)
    {

        $user_exist_userbasic = UserBasic::where('email', $request->email)->count();

        if ($user_exist_userbasic) {
            return response()->json([
                'success' => false,
                'msg' => 'User already exists',
            ]);
            
        } else {

            try {

                $user = UserBasic::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'expired_at' => now()->addMinutes(15),
                    'password' => Hash::make($request->password),
                ]);
            } catch (\Exception $err) {
                $user = null;
            }

            if ($user != null) {
                return response()->json([
                    'success' => true,
                    'msg' => 'Registation Completed'
                ]);
            } else {
                return response()->json([
                    'msg' => 'Internal Server Error',
                    'success' => false,
                    'err_msg' => $err->getMessage()
                ]);
            }
        }
    }
}
