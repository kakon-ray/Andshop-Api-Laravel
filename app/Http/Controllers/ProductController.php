<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


use Intervention\Image\Facades\Image;

class ProductController extends Controller
{

  public function __construct()
  {
    \Config::set('auth.defaults.guard', 'userbasic');
  }


  public function product_add(Request $request)
  {


    $arrayRequest = [
      'category' => $request->category,
      'subcategory' => $request->subcategory,
      'name' => $request->name,
      'code' => $request->code,
      'tags' => $request->tags,
      'purchase_price' => $request->purchase_price,
      'selling_price' => $request->selling_price,
      'discount_price' => $request->discount_price,
      'stock_quantity' => $request->stock_quantity,
      'description' => $request->description,
    ];

    $arrayValidate  = [
      'category' => 'required',
      'subcategory' => 'required',
      'name' => 'required|string|max:255',
      'code' => 'required',
      'purchase_price' => 'required|numeric|between:0,99999.99',
      'selling_price' => 'required|numeric|between:0,99999.99',
      'discount_price' => 'required|numeric|between:0,9999.99',
      'stock_quantity' => 'required|integer|min:0',
      'description' => 'required|string|max:1000',
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

      DB::beginTransaction();

      // single thumbnil image upload
      $host = $_SERVER['HTTP_HOST'];
      $baseUrl = "http://" . $host . "/images/product/";

      $thumbnail = $baseUrl . $request->thumbnail;

      $imagesWithUrls = array_map(function ($image) use ($baseUrl) {
        return $baseUrl . $image;
      }, $request->images);


      $product = Product::create([
        'category_id' => $request->category,
        'subcategory_id' => $request->subcategory,
        'name' => $request->name,
        'status' => 'Inprogress',
        'code' => $request->code,
        'tags' => $request->tags,
        'purchase_price' => $request->purchase_price,
        'selling_price' => $request->selling_price,
        'discount_price' => $request->discount_price,
        'stock_quantity' => $request->stock_quantity,
        'description' => $request->description,
        'thumbnail' => $thumbnail,
        'images' => json_encode($imagesWithUrls),
        'vendor_id' => $request->vendor_id,

      ]);

      DB::commit();
    } catch (\Exception $err) {
      $product = null;
    }

    if ($product != null) {
      return response()->json([
        'success' => true,
        'msg' => 'Product Submited',
        'product' => $product
      ]);
    } else {
      return response()->json([
        'success' => false,
        'msg' => 'Internal Server Error',
        'err_msg' => $err->getMessage()
      ]);
    }
  }


  public function specific_product_show(Request $request)
  {
    $product = Product::where('vendor_id', $request->vendor_id)->get();
    
    foreach ($product as $item) {
      $item->images = json_decode($item->images);
    }

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


  public function product_edit(Request $request)
  {


    $arrayRequest = [
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
    ];

    $arrayValidate  = [
      'category_id' => 'required',
      'subcategory_id' => 'required',
      'name' => 'required|string|max:255',
      'code' => 'required',
      'purchase_price' => 'required|numeric|between:0,99999.99',
      'selling_price' => 'required|numeric|between:0,99999.99',
      'discount_price' => 'required|numeric|between:0,9999.99',
      'stock_quantity' => 'required|integer|min:0',
      'description' => 'required|string|max:1000',
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

      DB::beginTransaction();

      // single thumbnil image upload
      $host = $_SERVER['HTTP_HOST'];
      $baseUrl = "http://" . $host . "/images/product/";

      $thumbnail = $baseUrl . $request->thumbnail;

      $imagesWithUrls = array_map(function ($image) use ($baseUrl) {
        return $baseUrl . $image;
      }, $request->images);


      $product = Product::where('id',$request->id)->update([
        'category_id' => $request->category_id,
        'subcategory_id' => $request->subcategory_id,
        'name' => $request->name,
        'status' => 'Inprogress',
        'code' => $request->code,
        'tags' => $request->tags,
        'purchase_price' => $request->purchase_price,
        'selling_price' => $request->selling_price,
        'discount_price' => $request->discount_price,
        'stock_quantity' => $request->stock_quantity,
        'description' => $request->description,
        'thumbnail' => $thumbnail,
        'images' => json_encode($imagesWithUrls),
        'vendor_id' => $request->vendor_id,

      ]);

      DB::commit();
    } catch (\Exception $err) {
      $product = null;
    }

    if ($product != null) {
      
      $updateProduct = Product::where('id',$request->id)->first();
      $updateProduct->images = json_decode($updateProduct->images);

      return response()->json([
        'success' => true,
        'msg' => 'Product Updated',
        'product' => $updateProduct
      ]);
    } else {
      return response()->json([
        'success' => false,
        'msg' => 'Internal Server Error',
        'err_msg' => $err->getMessage()
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
          'msg' => 'Delete this product',
          'id' => $product->id
        ]);
      } else {
        return response()->json([
          'error' => 'Internal Server Error',
          'err_msg' => $err->getMessage()
        ], 500);
      }
    }
  }

  // dropzone image upload
  public function store_image(Request $request)
  {
    $name = [];
    $original_name = [];
    foreach ($request->file('file') as $key => $value) {

      $filename = uniqid() . time() . '.' . $value->getClientOriginalExtension();
      $img = Image::make($value);
      $img->resize(500, 500)->save(public_path('/images/product/' . $filename));

      $name[] = $filename;
      $original_name[] = $value->getClientOriginalName();
    }

    return response()->json([
      'name'          => $name,
      'original_name' => $original_name
    ]);
  }

  public function category_show(Request $request)
  {
      $all_category = Category::all();

      if ($all_category->count() != 0) {
          return response()->json([
              'success' => true,
              'categories' => $all_category,
          ]);
      } else {
          return response()->json([
              'msg' => 'No Product',
          ]);
      }
  }

  public function sub_category_show(Request $request)
  {
      $all_subcategory = Subcategory::all();

      if ($all_subcategory->count() != 0) {
          return response()->json([
              'success' => true,
              'subcategories' => $all_subcategory,
          ]);
      } else {
          return response()->json([
              'msg' => 'No Subcategory found',
          ]);
      }
  }
  
}
