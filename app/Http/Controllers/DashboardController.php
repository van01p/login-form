<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $sellers = User::where('role', 'seller')
                ->withSum('salesItems as total_revenue', 'subtotal')
                ->withSum('salesItems as total_units', 'quantity')
                ->orderBy('name')
                ->get();

            $products = Product::with(['category', 'user'])->latest()->get();

            return view('dashboard.admin', [
                'sellers'  => $sellers,
                'products' => $products,
            ]);
        }

        return view('dashboard.seller', $this->sellerData($user));
    }

    public function showSeller(User $seller)
    {
        abort_unless($seller->role === 'seller', 404);

        return view('dashboard.seller_detail', array_merge(
            $this->sellerData($seller),
            ['seller' => $seller]
        ));
    }

    private function sellerData(User $seller): array
    {
        $products = Product::with('category')
            ->where('user_id', $seller->id)
            ->orderByDesc('sold')
            ->get();

        $items = OrderItem::with('order.user')
            ->where('seller_id', $seller->id)
            ->latest()
            ->get();

        $grouped = $items->groupBy('product_name')
            ->map(fn ($group) => $group->sum('quantity'))
            ->sortDesc();

        return [
            'products'     => $products,
            'totalOrders'  => $items->pluck('order_id')->unique()->count(),
            'totalUnits'   => $items->sum('quantity'),
            'totalRevenue' => $items->sum('subtotal'),
            'recentItems'  => $items->take(15),
            'chartLabels'  => $grouped->keys()->values(),
            'chartValues'  => $grouped->values(),
        ];
    }
}