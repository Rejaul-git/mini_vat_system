@extends('layouts.app')

@section('title', 'Create Purchase')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header">
            <i class="fas fa-plus"></i> Create New Purchase
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
                @csrf
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" 
                               value="{{ old('date', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" name="supplier_name" class="form-control @error('supplier_name') is-invalid @enderror" 
                               value="{{ old('supplier_name') }}" required>
                        @error('supplier_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Note (Optional)</label>
                    <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
                </div>

                <hr>

                <h5 class="mb-3"><i class="fas fa-box"></i> Purchase Items</h5>

                <div id="items-container">
                    @if(old('items'))
                        @foreach(old('items') as $index => $item)
                        <div class="item-row card mb-3 shadow-sm">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Product <span class="text-danger">*</span></label>
                                        <select name="items[{{ $index }}][product_id]" class="form-select product-select" required>
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" 
                                                        data-unit="{{ $product->unit }}" 
                                                        data-vat="{{ $product->vat_rate }}"
                                                        {{ old("items.$index.product_id") == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }} ({{ $product->sku }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                        <input type="number" name="items[{{ $index }}][qty]" class="form-control item-qty" 
                                               step="0.01" min="0.01" value="{{ old("items.$index.qty") }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Unit Price <span class="text-danger">*</span></label>
                                        <input type="number" name="items[{{ $index }}][unit_price]" class="form-control item-price" 
                                               step="0.01" min="0.01" value="{{ old("items.$index.unit_price") }}" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">VAT % <span class="text-danger">*</span></label>
                                        <input type="number" name="items[{{ $index }}][vat_rate]" class="form-control item-vat" 
                                               step="0.01" min="0" max="100" value="{{ old("items.$index.vat_rate", 15) }}" required>
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
                        @endforeach
                    @else
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
                                                        data-vat="{{ $product->vat_rate }}">
                                                    {{ $product->name }} ({{ $product->sku }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                        <input type="number" name="items[0][qty]" class="form-control item-qty" 
                                               step="0.01" min="0.01" required>
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
                    @endif
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
                    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Purchase
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = {{ old('items') ? count(old('items')) : 1 }};

$(document).ready(function() {
    // Add new item row
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
                                <option value="{{ $product->id }}" data-unit="{{ $product->unit }}" data-vat="{{ $product->vat_rate }}">
                                    {{ $product->name }} ({{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="items[${itemIndex}][qty]" class="form-control item-qty" step="0.01" min="0.01" required>
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

    // Remove item row
    $(document).on('click', '.remove-item', function() {
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            calculateTotal();
        } else {
            alert('At least one item is required!');
        }
    });

    // Auto-fill VAT rate when product is selected
    $(document).on('change', '.product-select', function() {
        let vatRate = $(this).find(':selected').data('vat');
        $(this).closest('.row').find('.item-vat').val(vatRate);
        calculateRowTotal($(this).closest('.row'));
    });

    // Calculate row total
    $(document).on('input', '.item-qty, .item-price, .item-vat', function() {
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

    // Initial calculation
    calculateTotal();
});
</script>
@endpush