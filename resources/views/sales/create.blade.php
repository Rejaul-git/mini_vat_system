@extends('layouts.app')

@section('title', 'Create Sale')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header">
            <i class="fas fa-plus"></i> Create New Sale
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('sales.store') }}" id="saleForm">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Sale Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" 
                               value="{{ old('date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" class="form-control @error('customer_name') is-invalid @enderror" 
                               value="{{ old('customer_name') }}" required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Note (Optional)</label>
                    <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
                </div>

                <hr>

                <h5 class="mb-3"><i class="fas fa-box"></i> Sale Items</h5>

                <div id="items-container">
                    <div class="item-row card mb-3 shadow-sm">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Product <span class="text-danger">*</span></label>
                                    <select name="items[0][product_id]" class="form-select product-select" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" 
                                                    data-unit="{{ $product->unit }}" 
                                                    data-vat="{{ $product->vat_rate }}"
                                                    data-stock="{{ $product->available_stock }}">
                                                {{ $product->name }} ({{ $product->sku }}) - Stock: {{ number_format($product->available_stock, 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="items[0][qty]" class="form-control item-qty" 
                                           step="0.01" min="0.01" required>
                                    <small class="text-muted stock-info"></small>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Unit Price <span class="text-danger">*</span></label>
                                    <input type="number" name="items[0][unit_price]" class="form-control item-price" 
                                           step="0.01" min="0.01" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">VAT % <span class="text-danger">*</span></label>
                                    <input type="number" name="items[0][vat_rate]" class="form-control item-vat" 
                                           step="0.01" min="0" max="100" value="15" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Total</label>
                                    <input type="text" class="form-control item-total" readonly value="0.00">
                                    <button type="button" class="btn btn-danger btn-sm mt-1 w-100 remove-item">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-success mb-3" id="add-item">
                    <i class="fas fa-plus"></i> Add Another Item
                </button>

                <hr>

                <!-- Summary -->
                <div class="row">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Subtotal:</strong></td>
                                <td class="text-end"><span id="subtotal">৳0.00</span></td>
                            </tr>
                            <tr>
                                <td><strong>VAT (15%):</strong></td>
                                <td class="text-end"><span id="vat">৳0.00</span></td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>Grand Total:</strong></td>
                                <td class="text-end"><strong><span id="grand-total">৳0.00</span></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Sale
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = 1;

$(document).ready(function() {
    $('#add-item').click(function() {
        let newRow = `
        <div class="item-row card mb-3 shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Product <span class="text-danger">*</span></label>
                        <select name="items[${itemIndex}][product_id]" class="form-select product-select" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-unit="{{ $product->unit }}" data-vat="{{ $product->vat_rate }}" data-stock="{{ $product->available_stock }}">
                                    {{ $product->name }} ({{ $product->sku }}) - Stock: {{ number_format($product->available_stock, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="items[${itemIndex}][qty]" class="form-control item-qty" step="0.01" min="0.01" required>
                        <small class="text-muted stock-info"></small>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Unit Price <span class="text-danger">*</span></label>
                        <input type="number" name="items[${itemIndex}][unit_price]" class="form-control item-price" step="0.01" min="0.01" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">VAT % <span class="text-danger">*</span></label>
                        <input type="number" name="items[${itemIndex}][vat_rate]" class="form-control item-vat" step="0.01" min="0" max="100" value="15" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Total</label>
                        <input type="text" class="form-control item-total" readonly value="0.00">
                        <button type="button" class="btn btn-danger btn-sm mt-1 w-100 remove-item">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>`;
        
        $('#items-container').append(newRow);
        itemIndex++;
    });

    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateTotal();
        } else {
            alert('At least one item is required!');
        }
    });

    $(document).on('change', '.product-select', function() {
        let vatRate = $(this).find(':selected').data('vat');
        let stock = $(this).find(':selected').data('stock');
        let row = $(this).closest('.row');
        
        row.find('.item-vat').val(vatRate);
        row.find('.stock-info').text('Available: ' + stock);
        row.find('.item-qty').attr('max', stock);
        
        calculateRowTotal(row);
    });

    $(document).on('input', '.item-qty', function() {
        let row = $(this).closest('.row');
        let stock = row.find('.product-select :selected').data('stock');
        let qty = parseFloat($(this).val()) || 0;
        
        if (qty > stock) {
            alert('Quantity cannot exceed available stock (' + stock + ')');
            $(this).val(stock);
        }
        
        calculateRowTotal(row);
    });

    $(document).on('input', '.item-price, .item-vat', function() {
        calculateRowTotal($(this).closest('.row'));
    });

    function calculateRowTotal(row) {
        let qty = parseFloat(row.find('.item-qty').val()) || 0;
        let price = parseFloat(row.find('.item-price').val()) || 0;
        let vatRate = parseFloat(row.find('.item-vat').val()) || 0;
        
        let subtotal = qty * price;
        let vat = (subtotal * vatRate) / 100;
        let total = subtotal + vat;
        
        row.find('.item-total').val(total.toFixed(2));
        calculateTotal();
    }

    function calculateTotal() {
        let subtotal = 0;
        let totalVat = 0;
        
        $('.item-row').each(function() {
            let qty = parseFloat($(this).find('.item-qty').val()) || 0;
            let price = parseFloat($(this).find('.item-price').val()) || 0;
            let vatRate = parseFloat($(this).find('.item-vat').val()) || 0;
            
            let itemSubtotal = qty * price;
            let itemVat = (itemSubtotal * vatRate) / 100;
            
            subtotal += itemSubtotal;
            totalVat += itemVat;
        });
        
        let grandTotal = subtotal + totalVat;
        
        $('#subtotal').text('৳' + subtotal.toFixed(2));
        $('#vat').text('৳' + totalVat.toFixed(2));
        $('#grand-total').text('৳' + grandTotal.toFixed(2));
    }

    calculateTotal();
});
</script>
@endpush