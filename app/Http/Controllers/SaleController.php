<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Only admins can manage sales.');
            }
            return $next($request);
        })->except(['index', 'show']);
    }

    /**
     * Display a listing of sales.
     */
    public function index(Request $request)
    {
        $query = Sale::with('items.product');

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Search by customer
        if ($request->filled('search')) {
            $query->where('customer_name', 'like', "%{$request->search}%");
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $sales = $query->paginate(15);

        return view('sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new sale.
     */
    public function create()
    {
        $products = Product::all();
        return view('sales.create', compact('products'));
    }

    /**
     * Store a newly created sale.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|before_or_equal:today',
            'customer_name' => 'required|string|max:100',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0.01',
            'items.*.vat_rate' => 'required|numeric|min:0|max:100',
        ], [
            'items.required' => 'Please add at least one item.',
            'items.*.qty.min' => 'Quantity must be greater than 0.',
            'items.*.unit_price.min' => 'Unit price must be greater than 0.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Create sale
            $sale = Sale::create([
                'date' => $request->date,
                'customer_name' => $request->customer_name,
                'note' => $request->note,
            ]);

            // Create sale items
            foreach ($request->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'vat_rate' => $item['vat_rate'],
                ]);
            }

            DB::commit();

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Sale created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create sale: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified sale.
     */
    public function show(Sale $sale)
    {
        $sale->load('items.product', 'items.returns');
        return view('sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified sale.
     */
    public function edit(Sale $sale)
    {
        $sale->load('items.product');
        $products = Product::all();
        return view('sales.edit', compact('sale', 'products'));
    }

    /**
     * Update the specified sale.
     */
    public function update(Request $request, Sale $sale)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|before_or_equal:today',
            'customer_name' => 'required|string|max:100',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0.01',
            'items.*.vat_rate' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Check if any items have returns
            $hasReturns = $sale->items()->whereHas('returns')->exists();
            if ($hasReturns) {
                return redirect()->back()
                    ->with('error', 'Cannot edit sale with returned items. Please delete returns first.');
            }

            // Update sale
            $sale->update([
                'date' => $request->date,
                'customer_name' => $request->customer_name,
                'note' => $request->note,
            ]);

            // Delete old items and create new ones
            $sale->items()->delete();
            foreach ($request->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'vat_rate' => $item['vat_rate'],
                ]);
            }

            DB::commit();

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Sale updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update sale: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified sale.
     */
    public function destroy(Sale $sale)
    {
        try {
            // Check if any items have returns
            $hasReturns = $sale->items()->whereHas('returns')->exists();
            if ($hasReturns) {
                return redirect()->back()
                    ->with('error', 'Cannot delete sale with returned items.');
            }

            $sale->delete();
            return redirect()->route('sales.index')
                ->with('success', 'Sale deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('sales.index')
                ->with('error', 'Failed to delete sale.');
        }
    }
}