<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category:id,name', 'user:id,name'])->latest()->get();

        return response()->json(['data' => $products]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        $data['user_id'] = $request->user()->id;

        $product = Product::create($data);

        return response()->json(['message' => 'Produk dibuat', 'data' => $product], 201);
    }

    public function update(Request $request, Product $product)
    {
        if (!$product->isManagedBy($request->user())) {
            return response()->json(['message' => 'Ini bukan produkmu'], 403);
        }

        $data = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name'        => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'stock'       => 'sometimes|integer|min:0',
        ]);

        $product->update($data);

        return response()->json(['message' => 'Produk diperbarui', 'data' => $product]);
    }

    public function destroy(Request $request, Product $product)
    {
        if (!$product->isManagedBy($request->user())) {
            return response()->json(['message' => 'Ini bukan produkmu'], 403);
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return response()->json(['message' => 'Produk dihapus']);
    }
}