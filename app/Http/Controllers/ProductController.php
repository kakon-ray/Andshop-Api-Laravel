<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

  public function __construct()
  {
    \Config::set('auth.defaults.guard', 'userbasic');
  }


  public function product_add(Request $request)
  {

    try {

      DB::beginTransaction();

      $product = Product::create([
        'category_id' => $request->category_id,
        'subcategory_id' => $request->subcategory_id,
        'name' => $request->name,
        'code' => $request->code,
        'tags' => $request->tags,
        'purchase_price' => $request->purchase_price,
        'selling_price' => $request->selling_price,
        'discount_price' => $request->discount_price,
        'stock_quantity' => $request->stock_quantity,
        'description' => $request->description,
        'thumbnail' => $request->thumbnail,
        'images' => $request->images,
        'admin_id' => $request->admin_id,

      ]);

      DB::commit();
    } catch (\Exception $err) {
      $product = null;
    }

    if ($product != null) {
      return response()->json([
        'success' => true,
        'msg' => 'Product Submited'
      ]);
    } else {
      return response()->json([
        'success' => false,
        'msg' => 'Internal Server Error'
      ]);
    }
  }


  public function product_show(Request $request)
  {
    $product = Product::all();

    if ($product->count() != 0) {
        return response()->json([
            'success' => true,
            'product' => $product,
        ]);
    } else {
        return response()->json([
            'msg' => 'No Product',
        ]);
    }
  }


  public function product_edit(Request $request)
  {
    $product = Product::find($request->id);

    try {

      DB::beginTransaction();

      $product->category_id =  $request->category_id;
      $product->subcategory_id =  $request->subcategory_id;
      $product->name =  $request->name;
      $product->code =  $request->code;
      $product->tags =  $request->tags;
      $product->purchase_price =  $request->purchase_price;
      $product->selling_price =  $request->selling_price;
      $product->discount_price =  $request->discount_price;
      $product->stock_quantity =  $request->stock_quantity;
      $product->description =  $request->description;
      $product->thumbnail =  $request->thumbnail;
      $product->images =  $request->images;
      $product->admin_id =  $request->admin_id;
      $product->save();

      DB::commit();

    } catch (\Exception $err) {
      $product = null;
    }

    if ($product != null) {
      return response()->json([
        'success' => true,
        'msg' => 'Product Updated'
      ]);
    } else {
      return response()->json([
        'success' => false,
        'msg' => 'Internal Server Error'
      ]);
    }
  }

  public function delete_product(Request $request)
  {
    $product = Product::find($request->id);

    if (is_null($product)) {
        return response()->json([
            'success' => false,
            'msg' => 'Do not find any product'
        ]);
    } else {

        try {

            $product->delete();

        } catch (\Exception $err) {
            $product = null;
        }

        if ($product != null) {
            return response()->json([
                'success' => true,
                'msg' => 'Delete this product'
            ]);
        } else {
            return response()->json([
                'error' => 'Internal Server Error',
                'err_msg' => $err->getMessage()
            ], 500);
        }
    }
}

}
