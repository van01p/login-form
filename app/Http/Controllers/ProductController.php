<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Customer: tampilan belanja, dikelompokkan per toko (seller)
        if ($user->role === 'customer') {
            $bySeller = Product::with(['category', 'user'])
                ->latest()
                ->get()
                ->groupBy('user_id');

            return view('shop.index', ['bySeller' => $bySeller]);
        }

        // Admin & seller: tabel kelola produk
        $query = Product::with(['category', 'user'])->latest();

        if ($user->role === 'seller') {
            $query->where('user_id', $user->id);
        }

        return view('products.index', ['products' => $query->get()]);
    }

    public function create()
    {
        return view('products.create', ['categories' => Category::all()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        $data['user_id'] = $request->user()->id;

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(Request $request, Product $product)
    {
        abort_unless($product->isManagedBy($request->user()), 403);

        return view('products.edit', [
            'product'    => $product,
            'categories' => Category::all(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        abort_unless($product->isManagedBy($request->user()), 403);

        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui');
    }

    public function destroy(Request $request, Product $product)
    {
        abort_unless($product->isManagedBy($request->user()), 403);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'stock'       => 'required|integer|min:0',
            'image'       => 'nullable|image|max:2048',
        ]);
    }
}