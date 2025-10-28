@extends('layouts.app')

@section('title', 'Sale Details')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-info-circle"></i> Sale Details #{{ $sale->id }}</span>
            <div class="btn-group btn-group-sm">
                @if(Auth::user()->isAdmin())
                <a href="{{ route('returns.create', ['sale_id' => $sale->id]) }}" class="btn btn-danger">
                    <i class="fas fa-undo"></i> Add Return
                </a>
                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @endif
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
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
                            <td class="fw-bold">Customer Name:</td>
                            <td><strong>{{ $sale->customer_name }}</strong></td>
                        </tr>
                        @if($sale->note)
                        <tr>
                            <td class="fw-bold">Note:</td>
                            <td>{{ $sale->note }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="text-success">Summary</h5>
                    <table class="table table-sm">
                        <tr>
                            <td class="fw-bold">Total Items:</td>
                            <td><span class="badge bg-info">{{ $sale->items->count() }}</span></td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Subtotal:</td>
                            <td>৳{{ number_format($sale->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">VAT Amount:</td>
                            <td>৳{{ number_format($sale->vat_amount, 2) }}</td>
                        </tr>
                        <tr class="table-success">
                            <td class="fw-bold">Grand Total:</td>
                            <td><strong class="fs-5">৳{{ number_format($sale->grand_total, 2) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <hr>

            <h5 class="mb-3 text-success"><i class="fas fa-box"></i> Sale Items</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th class="text-center">Sold Qty</th>
                            <th class="text-center">Returned Qty</th>
                            <th class="text-center">Net Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-center">VAT Rate</th>
                            <th class="text-end">Total</th>
                            @if(Auth::user()->isAdmin())
                            <th class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $item->product->name }}</strong></td>
                            <td><code>{{ $item->product->sku }}</code></td>
                            <td class="text-center">{{ number_format($item->qty, 2) }}</td>
                            <td class="text-center">
                                @if($item->returned_qty > 0)
                                    <span class="badge bg-warning text-dark">{{ number_format($item->returned_qty, 2) }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center"><strong>{{ number_format($item->net_qty, 2) }} {{ $item->product->unit }}</strong></td>
                            <td class="text-end">৳{{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-center">{{ $item->vat_rate }}%</td>
                            <td class="text-end"><strong>৳{{ number_format($item->line_grand_total, 2) }}</strong></td>
                            @if(Auth::user()->isAdmin())
                            <td class="text-center">
                                @if($item->net_qty > 0)
                                    <a href="{{ route('returns.create', ['sale_id' => $sale->id]) }}" class="btn btn-sm btn-danger" title="Return Item">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($sale->items->flatMap->returns->count() > 0)
            <hr>
            <h5 class="mb-3 text-danger"><i class="fas fa-undo"></i> Return History</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead class="table-danger">
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th class="text-center">Quantity</th>
                            <th>Reason</th>
                            @if(Auth::user()->isAdmin())
                            <th class="text-center">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                            @foreach($item->returns as $return)
                            <tr>
                                <td>{{ $return->date->format('d M Y') }}</td>
                                <td>{{ $item->product->name }}</td>
                                <td class="text-center">{{ number_format($return->qty, 2) }} {{ $item->product->unit }}</td>
                                <td>{{ $return->reason ?? 'N/A' }}</td>
                                @if(Auth::user()->isAdmin())
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteReturn({{ $return->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-return-{{ $return->id }}" action="{{ route('returns.destroy', $return) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <div class="text-muted mt-3">
                <small>
                    <i class="fas fa-clock"></i> Created: {{ $sale->created_at->format('d M Y, h:i A') }} |
                    Last Updated: {{ $sale->updated_at->format('d M Y, h:i A') }}
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteReturn(id) {
    if(confirm('Are you sure you want to delete this return?')) {
        document.getElementById('delete-return-' + id).submit();
    }
}
</script>
@endpush