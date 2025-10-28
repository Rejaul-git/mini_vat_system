@extends('layouts.app')

@section('title', 'Sales')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold text-success">
                <i class="fas fa-cash-register"></i> Sales Management
            </h1>
            <p class="text-muted">Track all your sales transactions</p>
        </div>
        <div class="col-md-4 text-end">
            @if(Auth::user()->isAdmin())
            <a href="{{ route('sales.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Sale
            </a>
            @endif
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('sales.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search customer..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_from" class="form-control" placeholder="From Date" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="date_to" class="form-control" placeholder="To Date" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-success flex-fill">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card shadow-sm">
        <div class="card-header">
            <i class="fas fa-list"></i> Sales List ({{ $sales->total() }} total)
        </div>
        <div class="card-body p-0">
            @if($sales->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>#ID</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">VAT</th>
                            <th class="text-end">Grand Total</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr>
                            <td>{{ $sale->id }}</td>
                            <td>{{ $sale->date->format('d M Y') }}</td>
                            <td><strong>{{ $sale->customer_name }}</strong></td>
                            <td><span class="badge bg-info">{{ $sale->items->count() }} items</span></td>
                            <td class="text-end">৳{{ number_format($sale->subtotal, 2) }}</td>
                            <td class="text-end">৳{{ number_format($sale->vat_amount, 2) }}</td>
                            <td class="text-end"><strong>৳{{ number_format($sale->grand_total, 2) }}</strong></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                    <a href="{{ route('sales.edit', $sale) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="deleteSale({{ $sale->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $sale->id }}" action="{{ route('sales.destroy', $sale) }}" method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-cash-register fa-3x text-muted mb-3"></i>
                <p class="text-muted">No sales found</p>
            </div>
            @endif
        </div>
        @if($sales->hasPages())
        <div class="card-footer">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteSale(id) {
    if(confirm('Are you sure you want to delete this sale?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush