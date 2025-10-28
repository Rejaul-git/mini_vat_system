<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display reports page.
     */
    public function index(Request $request)
    {
        // Default date range: last 30 days
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $companyName = $request->get('company_name', 'Your Company Name');

        // Get products with their statistics
        $products = Product::select('products.*')
            ->with(['purchaseItems' => function($query) use ($dateFrom, $dateTo) {
                $query->whereHas('purchase', function($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('date', [$dateFrom, $dateTo]);
                });
            }])
            ->with(['saleItems' => function($query) use ($dateFrom, $dateTo) {
                $query->whereHas('sale', function($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('date', [$dateFrom, $dateTo]);
                });
            }])
            ->get()
            ->map(function($product) use ($dateFrom, $dateTo) {
                // Calculate quantities for date range
                $purchasedQty = $product->purchaseItems->sum('qty');
                $soldQty = $product->saleItems->sum('qty');
                
                // Calculate returned quantity
                $returnedQty = DB::table('return_items')
                    ->join('sale_items', 'return_items.sale_item_id', '=', 'sale_items.id')
                    ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                    ->where('sale_items.product_id', $product->id)
                    ->whereBetween('return_items.date', [$dateFrom, $dateTo])
                    ->sum('return_items.qty');

                $product->total_purchased_qty = $purchasedQty;
                $product->total_sold_qty = $soldQty;
                $product->total_returned_qty = $returnedQty;
                $product->current_stock = $product->available_stock;

                return $product;
            })
            ->filter(function($product) {
                // Only show products with activity
                return $product->total_purchased_qty > 0 || 
                       $product->total_sold_qty > 0 || 
                       $product->total_returned_qty > 0;
            });

        // Calculate VAT summary for sales
        $salesSummary = Sale::whereBetween('date', [$dateFrom, $dateTo])
            ->with('items')
            ->get()
            ->reduce(function($carry, $sale) {
                $carry['subtotal'] += $sale->subtotal;
                $carry['vat'] += $sale->vat_amount;
                $carry['grand_total'] += $sale->grand_total;
                return $carry;
            }, ['subtotal' => 0, 'vat' => 0, 'grand_total' => 0]);

        return view('reports.index', compact(
            'products', 
            'salesSummary', 
            'dateFrom', 
            'dateTo',
            'companyName'
        ));
    }

    /**
     * Export report to CSV.
     */
    public function export(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $companyName = $request->get('company_name', 'Your Company Name');

        $products = Product::select('products.*')
            ->with(['purchaseItems' => function($query) use ($dateFrom, $dateTo) {
                $query->whereHas('purchase', function($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('date', [$dateFrom, $dateTo]);
                });
            }])
            ->with(['saleItems' => function($query) use ($dateFrom, $dateTo) {
                $query->whereHas('sale', function($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('date', [$dateFrom, $dateTo]);
                });
            }])
            ->get()
            ->map(function($product) use ($dateFrom, $dateTo) {
                $purchasedQty = $product->purchaseItems->sum('qty');
                $soldQty = $product->saleItems->sum('qty');
                
                $returnedQty = DB::table('return_items')
                    ->join('sale_items', 'return_items.sale_item_id', '=', 'sale_items.id')
                    ->where('sale_items.product_id', $product->id)
                    ->whereBetween('return_items.date', [$dateFrom, $dateTo])
                    ->sum('return_items.qty');

                return [
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'unit' => $product->unit,
                    'purchased_qty' => $purchasedQty,
                    'sold_qty' => $soldQty,
                    'returned_qty' => $returnedQty,
                    'available_stock' => $product->available_stock,
                ];
            })
            ->filter(function($product) {
                return $product['purchased_qty'] > 0 || 
                       $product['sold_qty'] > 0 || 
                       $product['returned_qty'] > 0;
            });

        $filename = "vat_register_report_{$dateFrom}_to_{$dateTo}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($products, $companyName, $dateFrom, $dateTo) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 support
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header info
            fputcsv($file, ['Company:', $companyName]);
            fputcsv($file, ['Report:', 'VAT Purchase & Sale Register']);
            fputcsv($file, ['Period:', "{$dateFrom} to {$dateTo}"]);
            fputcsv($file, ['Generated:', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []);
            
            // Column headers
            fputcsv($file, [
                'Product Name',
                'SKU',
                'Unit',
                'Total Purchased Qty',
                'Total Sold Qty',
                'Total Returned Qty',
                'Available Stock'
            ]);

            // Data rows
            foreach ($products as $product) {
                fputcsv($file, [
                    $product['product_name'],
                    $product['sku'],
                    $product['unit'],
                    $product['purchased_qty'],
                    $product['sold_qty'],
                    $product['returned_qty'],
                    $product['available_stock'],
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}