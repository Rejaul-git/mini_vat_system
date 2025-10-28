<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Only admins can manage purchases.');
            }
            return $next($request);
        })->except(['index', 'show']);
    }

    /**
     * Display a listing of purchases.
     */
    public function index(Request $request)
    {
        $query = Purchase::with('items.product');

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Search by supplier
        if ($request->filled('search')) {
            $query->where('supplier_name', 'like', "%{$request->search}%");
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $purchases = $query->paginate(15);

        return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new purchase.
     */
    public function create()
    {
        $products = Product::all();
        return view('purchases.create', compact('products'));
    }

    /**
     * Store a newly created purchase.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|before_or_equal:today',
            'supplier_name' => 'required|string|max:100',
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
            // Create purchase
            $purchase = Purchase::create([
                'date' => $request->date,
                'supplier_name' => $request->supplier_name,
                'note' => $request->note,
            ]);

            // Create purchase items
            foreach ($request->items as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'vat_rate' => $item['vat_rate'],
                ]);
            }

            DB::commit();

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create purchase: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified purchase.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load('items.product');
        return view('purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified purchase.
     */
    public function edit(Purchase $purchase)
    {
        $purchase->load('items.product');
        $products = Product::all();
        return view('purchases.edit', compact('purchase', 'products'));
    }

    /**
     * Update the specified purchase.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|before_or_equal:today',
            'supplier_name' => 'required|string|max:100',
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
            // Update purchase
            $purchase->update([
                'date' => $request->date,
                'supplier_name' => $request->supplier_name,
                'note' => $request->note,
            ]);

            // Delete old items and create new ones
            $purchase->items()->delete();
            foreach ($request->items as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'vat_rate' => $item['vat_rate'],
                ]);
            }

            DB::commit();

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Purchase updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update purchase: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified purchase.
     */
    public function destroy(Purchase $purchase)
    {
        try {
            $purchase->delete();
            return redirect()->route('purchases.index')
                ->with('success', 'Purchase deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('purchases.index')
                ->with('error', 'Failed to delete purchase.');
        }
    }
}