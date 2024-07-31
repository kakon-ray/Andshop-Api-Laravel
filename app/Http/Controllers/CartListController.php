<?php

namespace App\Http\Controllers;

use App\Models\CartList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartListController extends Controller
{


    public function store(Request $request)
    {
        $cartlist = CartList::where('product_id', $request->product_id)->count();

        if ($cartlist) {
            return response()->json([
                'success' => false,
                'msg' => 'Already have this cartlist'
            ]);
        } else {
            try {

                $addcartlist = CartList::create([
                    'user_id' => $request->user_id,
                    'product_id' => $request->product_id,
                    'name' => $request->name,
                    'price' => $request->price,
                    'quantity' => $request->quantity,
                    'image' => $request->image
                ]);


                if ($addcartlist != null) {
                    return response()->json([
                        'success' => true,
                        'msg' => 'Add to Cartlist',
                        'cartlist' => $addcartlist->only([
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

    public function update_cartquantity(Request $request)
    {

        $arrayRequest = [
            'quantity' => $request->quantity,
        ];

        $arrayValidate  = [
            'quantity' => 'required|integer|min:1',
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


        try {
            // Find the cart item by ID
            $cartlist = CartList::find($request->id);

            if (!$cartlist) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Cart item not found',
                ], 404);
            }

            // Update the cart item
            $cartlist->price = $request->price;
            $cartlist->quantity = $request->quantity;
            $cartlist->save();

            return response()->json([
                'success' => true,
                'msg' => 'Cart item updated successfully',
                'cartlist' => $cartlist->only([
                    'id',
                    'user_id',
                    'product_id',
                    'name',
                    'price',
                    'quantity',
                    'image'
                ])
            ]);
        } catch (\Exception $err) {
            return response()->json([
                'success' => false,
                'msg' => 'Internal Server Error',
                'err_msg' => $err->getMessage()
            ], 500);
        }
    }


    public function show_cartlist(Request $request)
    {

        $cartlist = CartList::where('user_id', $request->user_id)->get();

        if ($cartlist->isEmpty()) {
            return response()->json([
                'success' => false,
                'msg' => 'No items in the cart'
            ]);
        }

        return response()->json([
            'success' => true,
            'cartlist' => $cartlist->map(function ($cartList) {
                return $cartList->only([
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

            $cartList = CartList::find($request->id);

            if (!$cartList) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Cart item not found'
                ], 404);
            }


            $cartList->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Cart item deleted successfully',
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
