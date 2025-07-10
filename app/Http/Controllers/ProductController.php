<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('categories')->get();
        
        return response()->json($products);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array|max:5',
            'products.*.name' => 'required|string',
            'products.*.categories' => 'array|max:3',
            'products.*.categories.*.name' => 'required|string',
            'products.*.categories.*.image' => 'nullable|file|mimes:jpg,jpeg,png'
        ]);
        
        foreach ($request->products as $productData) {
            $product = Product::create(['name' => $productData['name']]);
            
            if (isset($productData['categories'])) {
                foreach ($productData['categories'] as $index => $categoryData) {
                    $imagePath = null;
                    
                    if (isset($categoryData['image']) && $categoryData['image'] instanceof \Illuminate\Http\UploadedFile) {
                        $imagePath = $categoryData['image']->store('uploads', 'public');
                    }
                    
                    $product->categories()->create([
                        'name' => $categoryData['name'],
                        'image_path' => $imagePath
                    ]);
                }
            }
        }
        
        return response()->json(['message' => 'Berhasil disimpan']);
    }
    
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->categories()->delete();
        $product->delete();
        
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
    
}
