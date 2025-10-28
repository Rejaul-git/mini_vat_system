@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="display-5 fw-bold text-success">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </h1>
            <p class="text-muted">Welcome back, {{ Auth::user()->name }}! Here's your overview.</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Products -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-box fa-3x text-primary"></i>
                    </div>
                    <h3 class="fw-bold text-success">{{ $stats['total_products'] }}</h3>
                    <p class="text-muted mb-0">Total Products</p>
                </div>
            </div>
        </div>

        <!-- Total Purchases -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-shopping-cart fa-3x text-info"></i>
                    </div>
                    <h3 class="fw-bold text-info">{{ $stats['total_purchases'] }}</h3>
                    <p class="text-muted mb-0">Total Purchases</p>
                </div>
            </div>
        </div>

        <!-- Total Sales -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-cash-register fa-3x text-warning"></i>
                    </div>
                    <h3 class="fw-bold text-warning">{{ $stats['total_sales'] }}</h3>
                    <p class="text-muted mb-0">Total Sales</p>
                </div>
            </div>
        </div>

        <!-- Total Returns -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-undo fa-3x text-danger"></i>
                    </div>
                    <h3 class="fw-bold text-danger">{{ $stats['total_returns'] }}</h3>
                    <p class="text-muted mb-0">Total Returns</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Purchases -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="fas fa-shopping-cart"></i> Recent Purchases
                </div>
                <div class="card-body">
                    @if($recent_purchases->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_purchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->date->format('d M Y') }}</td>
                                    <td>{{ $purchase->supplier_name }}</td>
                                    <td class="text-end">৳{{ number_format($purchase->grand_total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center mb-0">No recent purchases</p>
                    @endif
                    <div class="text-center mt-3">
                        <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-primary">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="fas fa-cash-register"></i> Recent Sales
                </div>
                <div class="card-body">
                    @if($recent_sales->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_sales as $sale)
                                <tr>
                                    <td>{{ $sale->date->format('d M Y') }}</td>
                                    <td>{{ $sale->customer_name }}</td>
                                    <td class="text-end">৳{{ number_format($sale->grand_total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center mb-0">No recent sales</p>
                    @endif
                    <div class="text-center mt-3">
                        <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-primary">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="fas fa-exclamation-triangle"></i> Low Stock Alert
                </div>
                <div class="card-body">
                    @if($low_stock_products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Unit</th>
                                    <th class="text-end">Available Stock</th>
                                    <th class="text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($low_stock_products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>{{ $product->unit }}</td>
                                    <td class="text-end">{{ number_format($product->available_stock, 2) }}</td>
                                    <td class="text-end">
                                        @if($product->available_stock <= 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($product->available_stock < 10)
                                            <span class="badge bg-warning text-dark">Low Stock</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-success mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        All products are in good stock!
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection