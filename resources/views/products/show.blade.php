@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-info-circle"></i> Product Details</span>
                    <div class="btn-group btn-group-sm">
                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        @endif
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Product ID:</div>
                        <div class="col-md-8">{{ $product->id }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Product Name:</div>
                        <div class="col-md-8">{{ $product->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">SKU:</div>
                        <div class="col-md-8"><code>{{ $product->sku }}</code></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Unit:</div>
                        <div class="col-md-8">{{ $product->unit }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">VAT Rate:</div>
                        <div class="col-md-8"><span class="badge bg-info">{{ $product->vat_rate }}%</span></div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Total Purchased:</div>
                        <div class="col-md-8"><span class="badge bg-success">{{ number_format($product->total_purchased, 2) }}</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Total Sold:</div>
                        <div class="col-md-8"><span class="badge bg-warning text-dark">{{ number_format($product->total_sold, 2) }}</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Total Returned:</div>
                        <div class="col-md-8"><span class="badge bg-danger">{{ number_format($product->total_returned, 2) }}</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Available Stock:</div>
                        <div class="col-md-8">
                            <h4><span class="badge {{ $product->available_stock > 10 ? 'bg-success' : ($product->available_stock > 0 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ number_format($product->available_stock, 2) }} {{ $product->unit }}
                            </span></h4>
                        </div>
                    </div>
                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Created At:</div>
                        <div class="col-md-8">{{ $product->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 fw-bold">Updated At:</div>
                        <div class="col-md-8">{{ $product->updated_at->format('d M Y, h:i A') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection