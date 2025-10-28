@extends('layouts.app')

@section('title', 'Purchases')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold text-success">
                <i class="fas fa-shopping-cart"></i> Purchases Management
            </h1>
            <p class="text-muted">Track all your purchases</p>
        </div>
        <div class="col-md-4 text-end">
            @if(Auth::user()->isAdmin())
            <a href="{{ route('purchases.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Purchase
            </a>
            @endif
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('purchases.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search supplier..." value="{{ request('search') }}">
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
                        <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Purchases Table -->
    <div class="card shadow-sm">
        <div class="card-header">
            <i class="fas fa-list"></i> Purchases List ({{ $purchases->total() }} total)
        </div>
        <div class="card-body p-0">
            @if($purchases->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>#ID</th>
                            <th>Date</th>
                            <th>Supplier</th>
                            <th>Items</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">VAT</th>
                            <th class="text-end">Grand Total</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchases as $purchase)
                        <tr>
                            <td>{{ $purchase->id }}</td>
                            <td>{{ $purchase->date->format('d M Y') }}</td>
                            <td><strong>{{ $purchase->supplier_name }}</strong></td>
                            <td><span class="badge bg-info">{{ $purchase->items->count() }} items</span></td>
                            <td class="text-end">৳{{ number_format($purchase->subtotal, 2) }}</td>
                            <td class="text-end">৳{{ number_format($purchase->vat_amount, 2) }}</td>
                            <td class="text-end"><strong>৳{{ number_format($purchase->grand_total, 2) }}</strong></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                    <a href="{{ route('purchases.edit', $purchase) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="deletePurchase({{ $purchase->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $purchase->id }}" action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="d-none">
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
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <p class="text-muted">No purchases found</p>
            </div>
            @endif
        </div>
        @if($purchases->hasPages())
       <!-- <div class="d-flex justify-content-center mt-3">
         {{ $purchases->links() }}
        </div> -->
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function deletePurchase(id) {
    if(confirm('Are you sure you want to delete this purchase?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush