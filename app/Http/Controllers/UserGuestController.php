<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class UserGuestController extends Controller
{
    public function get_all_product(Request $request)
    {
        // Retrieve the page number from the request (default is 1)
        $page = $request->input('page', 1);
        // Retrieve the number of products per page (default is 6)
        $perPage = $request->input('per_page', 6);

        $products = Product::select('id', 'category_id', 'subcategory_id', 'name', 'status', 'code', 'tags', 'selling_price', 'discount_price', 'description', 'thumbnail', 'images')
        ->where('status', '!=', '0')    
        ->paginate($perPage, ['*'], 'page', $page);

        // Decode the JSON 'images' field for each product
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
}
