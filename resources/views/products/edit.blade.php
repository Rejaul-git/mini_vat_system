@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="fas fa-edit"></i> Edit Product
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('products.update', $product) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku', $product->sku) }}" required>
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit <span class="text-danger">*</span></label>
                                <select name="unit" class="form-select @error('unit') is-invalid @enderror" required>
                                    <option value="">Select Unit</option>
                                    <option value="Pcs" {{ old('unit', $product->unit) == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                                    <option value="Kg" {{ old('unit', $product->unit) == 'Kg' ? 'selected' : '' }}>Kg</option>
                                    <option value="Ltr" {{ old('unit', $product->unit) == 'Ltr' ? 'selected' : '' }}>Ltr</option>
                                    <option value="Box" {{ old('unit', $product->unit) == 'Box' ? 'selected' : '' }}>Box</option>
                                    <option value="Dozen" {{ old('unit', $product->unit) == 'Dozen' ? 'selected' : '' }}>Dozen</option>
                                    <option value="Set" {{ old('unit', $product->unit) == 'Set' ? 'selected' : '' }}>Set</option>
                                </select>
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">VAT Rate (%) <span class="text-danger">*</span></label>
                                <input type="number" name="vat_rate" step="0.01" class="form-control @error('vat_rate') is-invalid @enderror" value="{{ old('vat_rate', $product->vat_rate) }}" required>
                                @error('vat_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Product
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection