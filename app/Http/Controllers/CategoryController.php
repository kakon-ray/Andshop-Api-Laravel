<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    public function __construct()
    {
      \Config::set('auth.defaults.guard', 'userbasic');
    }


    public function category_store(Request $request)
    {
        $slug = Str::slug($request->category_name, '-');
        $exist_category = Category::where('category_slug', $slug)->count();

        if ($exist_category) {
            return response()->json([
                'success' => false,
                'msg' => 'Already have this category'
            ]);
        } else {
            try {
                $product = Category::create([
                    'category_name' => $request->category_name,
                    'category_slug' => $slug,

                ]);
            } catch (\Exception $err) {
                $product = null;
            }

            if ($product != null) {
                return response()->json([
                    'success' => true,
                    'msg' => 'Category Created'
                ]);
            } else {
                return response()->json([
                    'error' => 'Internal Server Error',
                    'err_msg' => $err->getMessage()
                ], 500);
            }
        }
    }

    public function category_show(Request $request)
    {
        $all_category = Category::all();

        if ($all_category->count() != 0) {
            return response()->json([
                'success' => true,
                'category' => $all_category,
            ]);
        } else {
            return response()->json([
                'msg' => 'No Product',
            ]);
        }
    }


    public function category_edit(Request $request)
    {
        $category = Category::find($request->id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'msg' => 'Do not find any Category'
            ]);
        } else {
            $slug = Str::slug($request->category_name, '-');
            $exist_category = Category::where('category_slug', $slug)->count();

            if ($exist_category) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Already have this category'
                ]);
            } else {

                try {
                    $category->category_name =  $request->category_name;
                    $category->category_slug =  $slug;
                    $category->save();
                } catch (\Exception $err) {
                    $category = null;
                }

                if ($category != null) {
                    return response()->json([
                        'success' => true,
                        'msg' => 'Category Updated'
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



    public function category_delete(Request $request)
    {
        $category = Category::find($request->id);

        if (is_null($category)) {
            return response()->json([
                'success' => false,
                'msg' => 'Do not find any Category'
            ]);
        } else {

            try {

                $category->delete();

            } catch (\Exception $err) {
                $category = null;
            }

            if ($category != null) {
                return response()->json([
                    'success' => true,
                    'msg' => 'Delete this Category'
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
