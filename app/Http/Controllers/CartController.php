<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $items = $request->user()
            ->cartItems()
            ->with('product.user')
            ->get();

        $total = $items->sum(fn ($item) => $item->quantity * $item->product->price);

        return view('cart.index', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $user = $request->user();

        if ($product->user_id === $user->id) {
            return back()->withErrors(['cart' => 'Kamu tidak bisa membeli produk sendiri']);
        }

        $data = $request->validate([
            'quantity' => 'nullable|integer|min:1',
        ]);
        $qty = $data['quantity'] ?? 1;

        $item   = CartItem::firstOrNew(['user_id' => $user->id, 'product_id' => $product->id]);
        $newQty = ($item->quantity ?? 0) + $qty;

        if ($newQty > $product->stock) {
            return back()->withErrors(['cart' => "Stok {$product->name} hanya tersisa {$product->stock}"]);
        }

        $item->quantity = $newQty;
        $item->save();

        return back()->with('success', "{$product->name} ditambahkan ke keranjang");
    }

    public function update(Request $request, CartItem $item)
    {
        abort_unless($item->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if ($data['quantity'] > $item->product->stock) {
            return back()->withErrors(['cart' => "Stok {$item->product->name} hanya tersisa {$item->product->stock}"]);
        }

        $item->update(['quantity' => $data['quantity']]);

        return back()->with('success', 'Jumlah barang diperbarui');
    }

    public function remove(Request $request, CartItem $item)
    {
        abort_unless($item->user_id === $request->user()->id, 403);

        $item->delete();

        return back()->with('success', 'Barang dihapus dari keranjang');
    }

    public function checkout(Request $request)
    {
        $user  = $request->user();
        $items = $user->cartItems()->get();

        if ($items->isEmpty()) {
            return back()->withErrors(['cart' => 'Keranjang masih kosong']);
        }

        try {
            DB::transaction(function () use ($user, $items) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'total'   => 0,
                ]);

                $total = 0;

                foreach ($items as $item) {
                    $product = Product::whereKey($item->product_id)->lockForUpdate()->first();

                    if (!$product) {
                        throw new \RuntimeException('Ada produk yang sudah tidak tersedia');
                    }

                    if ($product->stock < $item->quantity) {
                        throw new \RuntimeException("Stok {$product->name} tidak mencukupi (sisa {$product->stock})");
                    }

                    $subtotal = $product->price * $item->quantity;

                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $product->id,
                        'seller_id'    => $product->user_id,
                        'product_name' => $product->name,
                        'price'        => $product->price,
                        'quantity'     => $item->quantity,
                        'subtotal'     => $subtotal,
                    ]);

                    $product->decrement('stock', $item->quantity);
                    $product->increment('sold', $item->quantity);

                    $total += $subtotal;
                }

                $order->update(['total' => $total]);

                CartItem::where('user_id', $user->id)->delete();
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['cart' => $e->getMessage()]);
        }

        $route = $user->role === 'admin' ? 'dashboard' : 'products.index';

        return redirect()->route($route)->with('success', 'Pembelian berhasil! Terima kasih sudah berbelanja.');
    }
}