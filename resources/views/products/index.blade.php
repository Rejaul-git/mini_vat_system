@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-6 fw-bold text-success">
                <i class="fas fa-box"></i> Products Management
            </h1>
            <p class="text-muted">Manage your product inventory</p>
        </div>
        <div class="col-md-4 text-end">
            @if(Auth::user()->isAdmin())
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Product
            </a>
            @endif
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or SKU..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="sort_by" class="form-select">
                        <option value="id" {{ request('sort_by') == 'id' ? 'selected' : '' }}>Sort by ID</option>
                        <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Sort by Name</option>
                        <option value="sku" {{ request('sort_by') == 'sku' ? 'selected' : '' }}>Sort by SKU</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-success flex-fill">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card shadow-sm">
        <div class="card-header">
            <i class="fas fa-list"></i> Products List ({{ $products->total() }} total)
        </div>
        <div class="card-body p-0">
            @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>#ID</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Unit</th>
                            <th>VAT Rate</th>
                            <th class="text-end">Available Stock</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td><strong>{{ $product->name }}</strong></td>
                            <td><code>{{ $product->sku }}</code></td>
                            <td>{{ $product->unit }}</td>
                            <td>{{ $product->vat_rate }}%</td>
                            <td class="text-end">
                                <span class="badge {{ $product->available_stock > 10 ? 'bg-success' : ($product->available_stock > 0 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ number_format($product->available_stock, 2) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(Auth::user()->isAdmin())
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="deleteProduct({{ $product->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $product->id }}" action="{{ route('products.destroy', $product) }}" method="POST" class="d-none">
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
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">No products found</p>
            </div>
            @endif
        </div>
        @if($products->hasPages())
        <div class="card-footer">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteProduct(id) {
    if(confirm('Are you sure you want to delete this product?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush