@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold text-success">
                <i class="fas fa-chart-bar"></i> VAT Purchase & Sale Register
            </h1>
            <p class="text-muted">Comprehensive reports for your business</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('reports.export', request()->query()) }}" class="btn btn-success">
                <i class="fas fa-download"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <i class="fas fa-filter"></i> Report Filters
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="date_from" class="form-label fw-bold">From Date</label>
                    <input type="date" name="date_from" id="date_from" class="form-control"
                           value="{{ $dateFrom }}">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label fw-bold">To Date</label>
                    <input type="date" name="date_to" id="date_to" class="form-control"
                           value="{{ $dateTo }}">
                </div>
                <div class="col-md-3">
                    <label for="company_name" class="form-label fw-bold">Company Name</label>
                    <input type="text" name="company_name" id="company_name" class="form-control"
                           value="{{ $companyName }}" placeholder="Your Company Name">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="d-grid gap-2 d-md-flex w-100">
                        <button type="submit" class="btn btn-success flex-fill">
                            <i class="fas fa-search"></i> Generate Report
                        </button>
                        <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Summary -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <i class="fas fa-calculator"></i> Sales Summary ({{ $dateFrom }} to {{ $dateTo }})
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded">
                        <h4 class="text-success mb-1">৳{{ number_format($salesSummary['subtotal'], 2) }}</h4>
                        <small class="text-muted">Total Subtotal</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div style="background-color: rgb(90 122 60)" class="p-3  rounded text-white">
                        <h4 class="mb-1">৳{{ number_format($salesSummary['vat'], 2) }}</h4>
                        <small>Total VAT Amount</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-success rounded text-white">
                        <h4 class="mb-1">৳{{ number_format($salesSummary['grand_total'], 2) }}</h4>
                        <small>Grand Total</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Report -->
    <div class="card shadow-sm">
        <div class="card-header">
            <i class="fas fa-boxes"></i> Product Movement Report ({{ $products->count() }} products)
        </div>
        <div class="card-body p-0">
            @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th class="text-center">Unit</th>
                            <th class="text-center">Purchased Qty</th>
                            <th class="text-center">Sold Qty</th>
                            <th class="text-center">Returned Qty</th>
                            <th class="text-center">Available Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td><strong>{{ $product->name }}</strong></td>
                            <td><code>{{ $product->sku }}</code></td>
                            <td class="text-center">{{ $product->unit }}</td>
                            <td class="text-center">
                                @if($product->total_purchased_qty > 0)
                                    <span class="badge bg-primary">{{ number_format($product->total_purchased_qty, 2) }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if($product->total_sold_qty > 0)
                                    <span class="badge bg-success">{{ number_format($product->total_sold_qty, 2) }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if($product->total_returned_qty > 0)
                                    <span class="badge bg-warning text-dark">{{ number_format($product->total_returned_qty, 2) }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ number_format($product->current_stock, 2) }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                <p class="text-muted">No product activity found for the selected period</p>
            </div>
            @endif
        </div>

    </div>

    <!-- Report Info -->
    <div class="mt-4 text-muted">
        <small>
            <i class="fas fa-info-circle"></i>
            Report generated for <strong>{{ $companyName }}</strong> |
            Period: <strong>{{ $dateFrom }}</strong> to <strong>{{ $dateTo }}</strong> |
            Generated: <strong>{{ now()->format('d M Y, h:i A') }}</strong>
        </small>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Set default date range if not set
    if (!$('#date_from').val()) {
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
        $('#date_from').val(thirtyDaysAgo.toISOString().split('T')[0]);
    }

    if (!$('#date_to').val()) {
        $('#date_to').val(new Date().toISOString().split('T')[0]);
    }
});
</script>
@endpush
