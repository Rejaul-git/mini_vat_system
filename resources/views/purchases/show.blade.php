@extends('layouts.app')

@section('title', 'Purchase Details')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-info-circle"></i> Purchase Details #{{ $purchase->id }}</span>
            <div class="btn-group btn-group-sm">
                @if(Auth::user()->isAdmin())
                <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endif
                <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="text-success">Purchase Information</h5>
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold" width="40%">Purchase ID:</td>
                            <td>#{{ $purchase->id }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Date:</td>
                            <td>{{ $purchase->date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Supplier Name:</td>
                            <td><strong>{{ $purchase->supplier_name }}</strong></td>
                        </tr>
                        @if($purchase->note)
                        <tr>
                            <td class="fw-bold">Note:</td>
                            <td>{{ $purchase->note }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="text-success">Summary</h5>
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold">Total Items:</td>
                            <td><span class="badge bg-info">{{ $purchase->items->count() }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Subtotal:</td>
                            <td>৳{{ number_format($purchase->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">VAT Amount:</td>
                            <td>৳{{ number_format($purchase->vat_amount, 2) }}</td>
                        </tr>
                        <tr class="table-success">
                            <td class="fw-bold">Grand Total:</td>
                            <td><strong class="fs-5">৳{{ number_format($purchase->grand_total, 2) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr>

            <h5 class="mb-3 text-success"><i class="fas fa-box"></i> Purchase Items</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-center">VAT Rate</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">VAT</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $item->product->name }}</strong></td>
                            <td><code>{{ $item->product->sku }}</code></td>
                            <td class="text-center">{{ number_format($item->qty, 2) }} {{ $item->product->unit }}</td>
                            <td class="text-end">৳{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-center">{{ $item->vat_rate }}%</td>
                            <td class="text-end">৳{{ number_format($item->line_total, 2) }}</td>
                            <td class="text-end">৳{{ number_format($item->line_vat, 2) }}</td>
                            <td class="text-end"><strong>৳{{ number_format($item->line_grand_total, 2) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-success">
                        <tr>
                            <th colspan="6" class="text-end">Totals:</th>
<th class="text-end">৳{{ number_format($purchase->subtotal, 2) }}</th>
<th class="text-end">৳{{ number_format($purchase->vat_amount, 2) }}</th>
<th class="text-end">৳{{ number_format($purchase->grand_total, 2) }}</th>
</tr>
</tfoot>
</table>
</div>
<div class="text-muted mt-3">
            <small>
                <i class="fas fa-clock"></i> Created: {{ $purchase->created_at->format('d M Y, h:i A') }} |
                Last Updated: {{ $purchase->updated_at->format('d M Y, h:i A') }}
            </small>
        </div>
    </div>
</div>
</div>
@endsection