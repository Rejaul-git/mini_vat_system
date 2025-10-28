@extends('layouts.app')

@section('title', 'Create Return')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold text-danger">
                <i class="fas fa-undo"></i> Create Return
            </h1>
            <p class="text-muted">Process return for sold items</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('sales.show', $sale ? $sale->id : null) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Sale
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <i class="fas fa-info-circle"></i> Return Details
        </div>
        <div class="card-body">
            @if($sale)
            <!-- Sale Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="text-success">Sale Information</h5>
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold" width="40%">Sale ID:</td>
                            <td>#{{ $sale->id }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Date:</td>
                            <td>{{ $sale->date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Customer:</td>
                            <td><strong>{{ $sale->customer_name }}</strong></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="text-success">Sale Items</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-success">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Sold Qty</th>
                                    <th class="text-center">Returned Qty</th>
                                    <th class="text-center">Returnable Qty</th>
                                </tr>
                            </thead>
                            <tbody id="sale-items-table">
                                @foreach($sale->items as $item)
                                <tr>
                                    <td>{{ $item->product->name }}</td>
                                    <td class="text-center">{{ number_format($item->qty, 2) }}</td>
                                    <td class="text-center">{{ number_format($item->returned_qty, 2) }}</td>
                                    <td class="text-center">
                                        @if($item->net_qty > 0)
                                            <span class="badge bg-info">{{ number_format($item->net_qty, 2) }}</span>
                                        @else
                                            <span class="badge bg-secondary">0</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <hr>

            <!-- Return Form -->
            <form method="POST" action="{{ route('returns.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="sale_item_id" class="form-label fw-bold">Select Product to Return <span class="text-danger">*</span></label>
                            <select name="sale_item_id" id="sale_item_id" class="form-select @error('sale_item_id') is-invalid @enderror" required>
                                <option value="">Choose a product...</option>
                                @if($sale)
                                    @foreach($sale->items as $item)
                                        @if($item->net_qty > 0)
                                        <option value="{{ $item->id }}" data-max-qty="{{ $item->net_qty }}" data-unit="{{ $item->product->unit }}">
                                            {{ $item->product->name }} ({{ $item->product->sku }}) - Max: {{ number_format($item->net_qty, 2) }} {{ $item->product->unit }}
                                        </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            @error('sale_item_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="qty" class="form-label fw-bold">Return Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="qty" id="qty" class="form-control @error('qty') is-invalid @enderror"
                                   step="0.01" min="0.01" required>
                            <div class="form-text" id="qty-help">Select a product first</div>
                            @error('qty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="date" class="form-label fw-bold">Return Date <span class="text-danger">*</span></label>
                            <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror"
                                   value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="reason" class="form-label fw-bold">Reason for Return</label>
                    <textarea name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror"
                              rows="3" placeholder="Optional reason for the return...">{{ old('reason') }}</textarea>
                    @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save"></i> Create Return
                    </button>
                    <a href="{{ route('sales.show', $sale ? $sale->id : null) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Update quantity help text and max when product is selected
    $('#sale_item_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const maxQty = selectedOption.data('max-qty');
        const unit = selectedOption.data('unit');

        if (maxQty) {
            $('#qty').attr('max', maxQty);
            $('#qty-help').text(`Maximum returnable quantity: ${maxQty} ${unit}`);
        } else {
            $('#qty').removeAttr('max');
            $('#qty-help').text('Select a product first');
        }
    });

    // Validate quantity on input
    $('#qty').on('input', function() {
        const maxQty = parseFloat($('#sale_item_id').find('option:selected').data('max-qty')) || 0;
        const enteredQty = parseFloat($(this).val()) || 0;

        if (enteredQty > maxQty) {
            $(this).addClass('is-invalid');
            $('#qty-help').text(`Cannot return more than ${maxQty} units`);
        } else {
            $(this).removeClass('is-invalid');
            $('#qty-help').text(`Maximum returnable quantity: ${maxQty} units`);
        }
    });
});
</script>
@endpush
