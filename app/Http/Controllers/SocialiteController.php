<?php

namespace App\Http\Controllers;

use App\Models\UserBasic;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class SocialiteController extends Controller
{
	public function loginSocial(string $provider)
	{
		return response()->json([
			'redirectUrl' => Socialite::driver($provider)->stateless()->redirect()->getTargetUrl()
		]);
	}

	public function callbackSocial(string $provider)
	{
		date_default_timezone_set('Asia/Dhaka');
		$date = date('Y-m-d', strtotime('-0 day'));
		$time = date('H:i:s', strtotime('-0 day'));

		// retriving users from google/facebook
		$user = Socialite::driver($provider)->stateless()->user();

		// checking if a new user or old user
		$finduser = UserBasic::where($provider . '_id', $user->id)
			->orWhere('email', $user->email)
			->first();

		// if a new user logging him and redirecting back to frontend
		if ($finduser) {
			Auth::login($finduser, true);
			$token = JWTAuth::fromUser($finduser);

			// sending user data
			$userData = [
				'success' => true,
				'token' => $token,
				$provider . '_id' => $finduser[$provider . '_id'],
				'user_name' => $finduser->name,
				'user_email' => $finduser->email,
				'user_image' => $finduser->image,
				'token_type' => 'bearer',
			];

			return view("auth.close_popup", [
				'userData' => $userData
			]);
		} else {
			// creating new user
			try {
				DB::beginTransaction();
				$newUser = UserBasic::create([
					'name' => $user->name,
					'email' => $user->email,
					'password' => encrypt('123456dummy'),
					$provider . '_id' => $user->id,
					'image' => $provider == 'facebook' ? $user->avatar_original . '&access_token=' . $user->token : $user->avatar,
					'last_login' => "not set",
					'date' => $date,
					'time' => $time,
					'status' => 1,
				]);

				// creating jwt token
				$user_basic = UserBasic::find($newUser->id);
				$token = JWTAuth::fromUser($newUser);



				$user_basic->remember_token = $token;
				$user_basic->save();

				// insert user details 
				$user_info = DB::table('user_basic')->where('email', $newUser->email,)->first();


				$UserDetailsData = array(
					'user_basic_id' => $user_info->id,
					'first_name' => "not set",
					'last_name' => "not set",
					'birthday' => "not set",
					'phone_number' => "not set",
					'gender' => "not set",
					'address' => "not set",
					'date' => $date,
					'time' => $time,
					'status' => 1,
				);
				DB::table('user_details')->insert($UserDetailsData);
				DB::commit();

				// sending user data
				$userData = [
					'success' => true,
					'token' => $token,
					$provider . '_id' => $newUser[$provider . '_id'],
					'user_name' => $newUser->name,
					'user_email' => $newUser->email,
					'user_image' => $newUser->image,
					'token_type' => 'bearer',
				];

				Auth::login($newUser);
				return view("auth.close_popup", [
					'userData' => $userData
				]);
			} catch (\Throwable $th) {
				DB::rollBack();
				return view("auth.close_popup", [
					'userData' => 'Something went wrong'
				]);
			}
		}
	}
}
