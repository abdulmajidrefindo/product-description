<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array|max:5',
            'products.*.name' => 'required|string',
            'products.*.categories' => 'array|max:3',
            'products.*.categories.*.name' => 'required|string',
        ]);

        foreach ($validated['products'] as $productData) {
            $product = Product::create(['name' => $productData['name']]);

            if (isset($productData['categories'])) {
                foreach ($productData['categories'] as $categoryData) {
                    $product->categories()->create(['name' => $categoryData['name']]);
                }
            }
        }

        return response()->json(['message' => 'Products saved successfully']);
    }
}
