<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function store(Request $request, Product $product)
    {
        if ($product->categories()->count() >= 3) {
            return response()->json(['message' => 'Maksimum kategori tercapai'], 400);
        }

        $validated = $request->validate([
            'name' => 'required|string',
        ]);

        $category = $product->categories()->create($validated);
        return response()->json($category);
    }

    public function upload(Request $request, Category $category)
    {
        $validated = $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png',
        ]);

        $path = $request->file('image')->store('uploads', 'public');
        $category->update(['image_path' => $path]);

        return response()->json(['message' => 'Image uploaded']);
    }

    public function deleteImage(Category $category)
    {
        if ($category->image_path && Storage::disk('public')->exists($category->image_path)) {
            Storage::disk('public')->delete($category->image_path);
        }

        $category->update(['image_path' => null]);
        return response()->json(['message' => 'Image deleted']);
    }
}
