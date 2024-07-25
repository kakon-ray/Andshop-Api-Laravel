<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
      'thumbnail' => $request->thumbnail,
      'images' => $request->images,
      'vendor_id' => $request->vendor_id,
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
      'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
      'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
      'vendor_id' => 'required',
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
      $slug = Str::slug($request->name . '-' . hexdec(uniqid()));

      if ($request->thumbnail) {
        $file = $request->file('thumbnail');
        $filename = $slug . '.' . $file->getClientOriginalExtension();

        $img = Image::make($file);
        $img->resize(320, 240)->save(public_path('uploads/' . $filename));

        $host = $_SERVER['HTTP_HOST'];
        $image = "http://" . $host . "/uploads/" . $filename;
        $thumbnail = $image;

        //http://127.0.0.1:8000/uploads/kakon-ray.jpg

      }

      //multiple images uploads
      $images = array();
      if ($request->images) {
        foreach ($request->file('images') as $key => $image2) {
          $imageName = hexdec(uniqid()) . '.' . $image2->getClientOriginalExtension();

          $img = Image::make($image2);
          $img->resize(320, 240)->save(public_path('uploads/' . $imageName));

          $host = $_SERVER['HTTP_HOST'];
          $imageLink = "http://" . $host . "/uploads/" . $imageName;

          array_push($images, $imageLink);
        }
      }

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
        'images' => json_encode($images),
        'vendor_id' => $request->vendor_id,

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
        'msg' => 'Internal Server Error',
        'err_msg' => $err->getMessage()
      ]);
    }
  }


  public function specific_product_show(Request $request)
  {
    $product = Product::where('vendor_id', $request->vendor_id)->get();

    if ($product->count() != 0) {
      return response()->json([
        'success' => true,
        'myproduct' => $product,
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
      'thumbnail' => $request->thumbnail,
      'images' => $request->images,
      'vendor_id' => $request->vendor_id,
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
      'thumbnail' => 'sometimes',
      'thumbnail.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
      'images' => 'sometimes',
      'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
      'vendor_id' => 'required',
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


    $product = Product::find($request->id);

    try {

      DB::beginTransaction();

      // single thumbnil image upload
      $slug = Str::slug($request->name . '-' . hexdec(uniqid()));

      if ($request->thumbnail) {
        $file = $request->file('thumbnail');
        $filename = $slug . '.' . $file->getClientOriginalExtension();

        $img = Image::make($file);
        $img->resize(320, 240)->save(public_path('uploads/' . $filename));

        $host = $_SERVER['HTTP_HOST'];
        $image = "http://" . $host . "/uploads/" . $filename;
        $thumbnail = $image;

        //http://127.0.0.1:8000/uploads/kakon-ray.jpg

      } else {
        $thumbnail = $request->old_thumbnail;
      }

      //multiple images uploads
      $images = array();
      if ($request->images) {
        foreach ($request->file('images') as $key => $image2) {
          $imageName = hexdec(uniqid()) . '.' . $image2->getClientOriginalExtension();

          $img = Image::make($image2);
          $img->resize(320, 240)->save(public_path('uploads/' . $imageName));

          $host = $_SERVER['HTTP_HOST'];
          $imageLink = "http://" . $host . "/uploads/" . $imageName;

          array_push($images, $imageLink);
        }
      } else {
        $images = $request->old_images;
      }


      $product->category_id =  $request->category;
      $product->subcategory_id =  $request->subcategory;
      $product->name =  $request->name;
      $product->code =  $request->code;
      $product->tags =  $request->tags;
      $product->purchase_price =  $request->purchase_price;
      $product->selling_price =  $request->selling_price;
      $product->discount_price =  $request->discount_price;
      $product->stock_quantity =  $request->stock_quantity;
      $product->description =  $request->description;
      $product->thumbnail =  $thumbnail;
      $product->images =  json_encode($images);
      $product->vendor_id =  $request->vendor_id;
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
}
