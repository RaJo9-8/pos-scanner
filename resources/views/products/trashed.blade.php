@extends('layouts.app')

@section('title', 'Trashed Products')
@section('breadcrumb', 'Trashed Products')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Trashed Products</h3>
                <div class="card-tools">
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Barcode</th>
                                <th>Category</th>
                                <th>Stock</th>
                                <th>Purchase Price</th>
                                <th>Selling Price</th>
                                <th>Deleted At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->name }}</td>
                                <td><code>{{ $product->barcode }}</code></td>
                                <td>{{ $product->category ?: '-' }}</td>
                                <td>{{ $product->stock }} {{ $product->unit }}</td>
                                <td>{{ $product->formatted_purchase_price }}</td>
                                <td>{{ $product->formatted_selling_price }}</td>
                                <td>{{ $product->deleted_at->format('d M Y H:i') }}</td>
                                <td>
                                    @if(auth()->check())
                                        @if(auth()->user()->isSuperAdmin())
                                        <form action="{{ route('products.restore', $product->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to restore this product?')">
                                                <i class="fas fa-undo"></i> Restore
                                            </button>
                                        </form>
                                        @else
                                            <span class="text-muted">No access</span>
                                        @endif
                                    @else
                                        <span class="text-muted">Not logged in</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">No trashed products found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
