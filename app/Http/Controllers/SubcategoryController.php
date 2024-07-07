<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubcategoryController extends Controller
{


    public function __construct()
    {
        \Config::set('auth.defaults.guard', 'userbasic');
    }


    public function sub_category_store(Request $request)
    {
        $slug = Str::slug($request->subcategory_name, '-');
        $exist_category = Subcategory::where('subcat_slug', $slug)->count();

        if ($exist_category) {
            return response()->json([
                'success' => false,
                'msg' => 'Already have this category'
            ]);
        } else {
            try {
                $product = Subcategory::create([
                    'category_id' => $request->category_id,
                    'subcategory_name' => $request->subcategory_name,
                    'subcat_slug' => $slug,

                ]);
            } catch (\Exception $err) {
                $product = null;
            }

            if ($product != null) {
                return response()->json([
                    'success' => true,
                    'msg' => 'Subcategory Created'
                ]);
            } else {
                return response()->json([
                    'error' => 'Internal Server Error',
                    'err_msg' => $err->getMessage()
                ], 500);
            }
        }
    }

    public function sub_category_show(Request $request)
    {
        $all_category = Subcategory::all();

        if ($all_category->count() != 0) {
            return response()->json([
                'success' => true,
                'category' => $all_category,
            ]);
        } else {
            return response()->json([
                'msg' => 'No Subcategory found',
            ]);
        }
    }


    public function sub_category_edit(Request $request)
    {
        $subcategory = Subcategory::find($request->id);

        if (!$subcategory) {
            return response()->json([
                'success' => false,
                'msg' => 'Do not find any Sub Category'
            ]);
        } else {
            $slug = Str::slug($request->subcategory_name, '-');
            $exist_category = Subcategory::where('subcat_slug', $slug)->count();

            if ($exist_category) {
                return response()->json([
                    'success' => false,
                    'msg' => 'Already have this sub category'
                ]);
            } else {

                try {
                    $subcategory->category_id =  $request->category_id;
                    $subcategory->subcategory_name =  $request->subcategory_name;
                    $subcategory->subcat_slug =  $slug;
                    $subcategory->save();
                } catch (\Exception $err) {
                    $subcategory = null;
                }

                if ($subcategory != null) {
                    return response()->json([
                        'success' => true,
                        'msg' => 'Subcategory Updated'
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



    public function sub_category_delete(Request $request)
    {
        $subcategory = Subcategory::find($request->id);

        if (is_null($subcategory)) {
            return response()->json([
                'success' => false,
                'msg' => 'Do not find any Category'
            ]);
        } else {

            try {

                $subcategory->delete();
            } catch (\Exception $err) {
                $subcategory = null;
            }

            if ($subcategory != null) {
                return response()->json([
                    'success' => true,
                    'msg' => 'Delete this Subcategory'
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
