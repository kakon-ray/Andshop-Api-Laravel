<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class UserGuestController extends Controller
{
    public function get_all_product()
    {
        // Retrieve the products with specific fields
        $products = Product::select('id', 'category_id', 'subcategory_id', 'name', 'status', 'code', 'tags', 'selling_price', 'discount_price', 'description', 'thumbnail', 'images')->get();

        $count = $products->count();

        if ($count > 0) {

            foreach ($products as $item) {
                $item->images = json_decode($item->images);
            }

            return response()->json([
                'success' => true,
                'count' => $count,
                'products' => $products
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No products found',
                'products' => []
            ]);
        }
    }
}
