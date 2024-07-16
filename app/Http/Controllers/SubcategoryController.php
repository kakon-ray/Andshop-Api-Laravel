<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class SubcategoryController extends Controller
{


    public function __construct()
    {
        \Config::set('auth.defaults.guard', 'admin');
    }


    public function sub_category_store(Request $request)
    {
        $arrayRequest = [
            'subcategory_name' => $request->subcategory_name,
            'category' => $request->category,
        ];

        $arrayValidate  = [
            'subcategory_name' => 'required',
            'category' => 'required',
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

        $slug = Str::slug($request->subcategory_name, '-');
        $exist_category = Subcategory::where('subcat_slug', $slug)->count();

        if ($exist_category) {
            return response()->json([
                'success' => false,
                'msg' => 'Already have this category'
            ]);
        } else {
            try {
                $subcategory = Subcategory::create([
                    'category_id' => $request->category,
                    'subcategory_name' => $request->subcategory_name,
                    'subcat_slug' => $slug,

                ]);
            } catch (\Exception $err) {
                $subcategory = null;
            }

            if ($subcategory != null) {
                return response()->json([
                    'success' => true,
                    'msg' => 'Subcategory Created',
                    'subcategory'=>$subcategory
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'msg'=>'Internal Server Error',
                    'err_msg' => $err->getMessage()
                ]);
            }
        }
    }

    public function sub_category_show(Request $request)
    {
        $all_subcategory = Subcategory::all();

        if ($all_subcategory->count() != 0) {
            return response()->json([
                'success' => true,
                'subcategory' => $all_subcategory,
            ]);
        } else {
            return response()->json([
                'msg' => 'No Subcategory found',
            ]);
        }
    }


    public function sub_category_edit(Request $request)
    {


        $arrayRequest = [
            'subcategory_name' => $request->subcategory_name,
            'category_id' => $request->category_id,
        ];

        $arrayValidate  = [
            'subcategory_name' => 'required',
            'category_id' => 'required',
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
                        'msg' => 'Subcategory Updated',
                       'data'=> $subcategory,
                        'id'=> $request->id
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
                    'msg' => 'Delete this Subcategory',
                    'id' => $request->id,
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
