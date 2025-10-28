<?php

namespace App\Http\Controllers;

use App\Models\ReturnItem;
use App\Models\SaleItem;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReturnController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAdmin()) {
                abort(403, 'Only admins can manage returns.');
            }
            return $next($request);
        });
    }

    /**
     * Show form to create return.
     */
    public function create(Request $request)
    {
        $saleId = $request->get('sale_id');
        $sale = null;
        
        if ($saleId) {
            $sale = Sale::with('items.product', 'items.returns')->findOrFail($saleId);
        }

        return view('returns.create', compact('sale'));
    }

    /**
     * Store a newly created return.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sale_item_id' => 'required|exists:sale_items,id',
            'qty' => 'required|numeric|min:0.01',
            'date' => 'required|date|before_or_equal:today',
            'reason' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $saleItem = SaleItem::with('returns')->findOrFail($request->sale_item_id);
        
        // Check if return quantity is valid
        $totalReturned = $saleItem->returns->sum('qty');
        $remainingQty = $saleItem->qty - $totalReturned;

        if ($request->qty > $remainingQty) {
            return redirect()->back()
                ->with('error', "Cannot return {$request->qty} units. Only {$remainingQty} units available for return.")
                ->withInput();
        }

        // Check if return date is after sale date
        if ($request->date < $saleItem->sale->date) {
            return redirect()->back()
                ->with('error', 'Return date cannot be before sale date.')
                ->withInput();
        }

        ReturnItem::create($request->all());

        return redirect()->route('sales.show', $saleItem->sale_id)
            ->with('success', 'Return created successfully!');
    }

    /**
     * Remove the specified return.
     */
    public function destroy(ReturnItem $return)
    {
        $saleId = $return->saleItem->sale_id;
        $return->delete();

        return redirect()->route('sales.show', $saleId)
            ->with('success', 'Return deleted successfully!');
    }

    /**
     * Get sale items for AJAX (for return form).
     */
    public function getSaleItems($saleId)
    {
        $sale = Sale::with('items.product', 'items.returns')->findOrFail($saleId);
        
        $items = $sale->items->map(function($item) {
            $totalReturned = $item->returns->sum('qty');
            return [
                'id' => $item->id,
                'product_name' => $item->product->name,
                'sold_qty' => $item->qty,
                'returned_qty' => $totalReturned,
                'returnable_qty' => $item->qty - $totalReturned,
                'unit' => $item->product->unit,
            ];
        })->filter(function($item) {
            return $item['returnable_qty'] > 0;
        })->values();

        return response()->json($items);
    }
}