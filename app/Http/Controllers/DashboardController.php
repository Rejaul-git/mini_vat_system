<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\ReturnItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_purchases' => Purchase::count(),
            'total_sales' => Sale::count(),
            'total_returns' => ReturnItem::count(),
        ];

        $recent_purchases = Purchase::with('items.product')
            ->latest('date')
            ->take(5)
            ->get();

        $recent_sales = Sale::with('items.product')
            ->latest('date')
            ->take(5)
            ->get();

        // Get low stock products (stock < 10)
        $low_stock_products = Product::all()->filter(function($product) {
            return $product->available_stock < 10;
        })->sortBy('available_stock');

        return view('dashboard', compact(
            'stats',
            'recent_purchases',
            'recent_sales',
            'low_stock_products'
        ));
    }
}