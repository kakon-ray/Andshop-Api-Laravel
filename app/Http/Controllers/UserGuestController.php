<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserGuestController extends Controller
{
    public function get_all_product(Request $request)
    {

        $page = $request->input('page', 1);

        $perPage = $request->input('per_page', 6);

        $products = Product::select('id', 'category_id', 'subcategory_id', 'name', 'status', 'code', 'tags', 'selling_price', 'discount_price', 'description', 'thumbnail', 'images')
            ->where('status', '!=', '0')
            ->paginate($perPage, ['*'], 'page', $page);


        foreach ($products as $product) {
            $product->images = json_decode($product->images);
        }


        return response()->json([
            'success' => true,
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'total_products' => $products->total(),
            'products' => $products->items(),
            'next_page_url' => $products->nextPageUrl(),
            'prev_page_url' => $products->previousPageUrl(),
        ]);
    }

    public function product_details(Request $request)
    {

        try {
            $product = Product::where('id', $request->id)
                ->select('id', 'category_id', 'subcategory_id', 'name', 'status', 'code', 'tags', 'selling_price', 'discount_price', 'description', 'thumbnail', 'images')
                ->firstOrFail(); 

            return response()->json([
                'success' => true,
                'product' => $product,
                'msg' => "Get Product Details"
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'product' => null,
                'msg' => "Product details not found"
            ], 404); 
        }
    }
}
