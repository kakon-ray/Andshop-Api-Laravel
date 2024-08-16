<?php

namespace App\Http\Controllers;

use App\Models\WishList;
use Illuminate\Http\Request;
use App\Models\CartList;
use Illuminate\Support\Facades\Validator;

class WishListController extends Controller
{
    
    public function store(Request $request)
    {
        $wishlist = WishList::where('product_id', $request->product_id)->where('user_id',$request->user_id)->count();

        if ($wishlist) {
            return response()->json([
                'success' => false,
                'msg' => 'Already have this wishlist'
            ]);
        } else {
            try {

                $addwishlist = WishList::create([
                    'user_id' => $request->user_id,
                    'product_id' => $request->product_id,
                    'name' => $request->name,
                    'price' => $request->price,
                    'quantity' => $request->quantity,
                    'image' => $request->image
                ]);


                if ($addwishlist != null) {
                    return response()->json([
                        'success' => true,
                        'msg' => 'Add to wishlist',
                        'wishlist' => $addwishlist->only([
                            'id',
                            'user_id',
                            'product_id',
                            'name',
                            'price',
                            'quantity',
                            'image'
                        ])
                    ]);
                }
            } catch (\Exception $err) {
                return response()->json([
                    'error' => 'Internal Server Error',
                    'err_msg' => $err->getMessage()
                ]);
            }
        }
    }


    public function show_cartlist(Request $request)
    {

        $wishlist = WishList::where('user_id', $request->user_id)->get();

        if ($wishlist->isEmpty()) {
            return response()->json([
                'success' => false,
                'msg' => 'No items in the wishlist'
            ]);
        }

        return response()->json([
            'success' => true,
            'wishlist' => $wishlist->map(function ($wishlist) {
                return $wishlist->only([
                    'id',
                    'user_id',
                    'product_id',
                    'name',
                    'price',
                    'quantity',
                    'image'
                ]);
            })
        ]);
    }

    public function destroy(Request $request)
    {
        try {

            $wishlist = WishList::find($request->id);

            if (!$wishlist) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Cart item not found'
                ], 404);
            }

            $wishlist->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Wishlist item deleted successfully',
                'id' => $request->id
            ]);
        } catch (\Exception $err) {
            return response()->json([
                'success' => false,
                'error' => 'Internal Server Error',
                'err_msg' => $err->getMessage()
            ], 500);
        }
    }
}
