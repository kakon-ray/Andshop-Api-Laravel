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
            $userInfo->status = true;
            $userInfo->save();

            return response()->json([
                'success' => true,
                'user' => 'Vendor Requested Accepted',
            ]);
        }
    }

    public function user_request_personal_info_cancel(Request $request)
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
            $userInfo->status = false;
            $userInfo->save();

            return response()->json([
                'success' => true,
                'user' => 'Vendor Requested Cancel',
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
  public function product_cancel(Request $request)
  {
       $product = Product::find($request->id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'user' => 'Do not find any Product',
            ]);
        }

        $product->status = 'Cancel';
        $product->save();

        return response()->json([
            'success' => true,
            'user' => 'Product Cancel',
        ]);
  }

}
