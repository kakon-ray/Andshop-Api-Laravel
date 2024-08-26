<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\UserBasic;


class VendorManagement extends Controller
{
    public function __construct()
    {
        \Config::set('auth.defaults.guard', 'admin');
    }

    public function user_request_personal_info_accepted(Request $request)
    {
        $userInfo = UserBasic::find($request->id);

        if (!$userInfo) {
            return response()->json([
                'success' => false,
                'user' => 'Do not find any user',
            ]);
        }

        $check_Vendor_request = UserBasic::where('id', $request->id)->where('role', 'vendor')->count();

        if ($check_Vendor_request) {
            $userInfo->status = $request->value;


            if ($request->value) {

                $userInfo->role = "Vendor";
                $userInfo->save();

                return response()->json([
                    'success' => true,
                    'msg' => 'Vendor Requested Accepted',
                ]);
            } else {

                $userInfo->role = "User";
                $userInfo->save();

                return response()->json([
                    'success' => true,
                    'msg' => 'Vendor Requested Cancel',
                ]);
            }
        }
    }

    public function vendor_manage(Request $request)
    {
        $users = UserBasic::where('role','Vendor')->get();
        if ($users) {
            return response()->json([
                'success' => true,
                'users' => $users,
            ]);
        }else{
            return response()->json([
                'success' => true,
                'users' => [],
            ]);
        }
    }



    // vendor product management

    public function product_manage(Request $request)
    {
        $product = Product::all();

        if ($product->count() != 0) {
            return response()->json([
                'success' => true,
                'products' => $product,
            ]);
        } else {
            return response()->json([
                'msg' => 'No Product',
            ]);
        }
    }

    public function product_approved(Request $request)
    {
        $product = Product::find($request->id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'user' => 'Do not find any Product',
            ]);
        }

        $product->status = $request->value;
        $product->save();

        return response()->json([
            'success' => true,
            'user' => 'Product Approved',
        ]);
    }
    public function product_delete(Request $request)
    {
        $product = Product::find($request->id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'msg' => 'Do not find any Product',
            ]);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'msg' => 'Product Deleted',
        ]);
    }
}
