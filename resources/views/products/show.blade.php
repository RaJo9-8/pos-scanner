@extends('layouts.app')

@section('title', 'Product Details')
@section('breadcrumb', 'Product Details')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Image</h3>
            </div>
            <div class="card-body text-center">
                <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('dist/img/default-150x150.png') }}" 
                     alt="{{ $product->name }}" class="img-fluid" style="max-height: 300px;">
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Stock Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage {{ $product->isLowStock() ? 'text-danger' : 'text-success' }}">
                                <i class="fas fa-{{ $product->isLowStock() ? 'exclamation-triangle' : 'check' }}"></i>
                                {{ $product->isLowStock() ? 'Low Stock' : 'In Stock' }}
                            </span>
                            <h5 class="description-header">{{ $product->stock }} {{ $product->unit }}</h5>
                            <span class="description-text">Current Stock</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <span class="description-percentage text-warning">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Details: {{ $product->name }}</h3>
                <div class="card-tools">
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Barcode:</strong></td>
                                <td><code>{{ $product->barcode }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Category:</strong></td>
                                <td>{{ $product->category ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Stock:</strong></td>
                                <td>
                                    <span class="badge {{ $product->stock <= $product->min_stock ? 'badge-danger' : 'badge-success' }}">
                                        {{ $product->stock }} {{ $product->unit }}
                                    </span>
                                    @if($product->stock <= $product->min_stock)
                                    <small class="text-danger">(Low Stock)</small>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Purchase Price:</strong></td>
                                <td>{{ $product->formatted_purchase_price }}</td>
                            </tr>
                            <tr>
                                <td><strong>Selling Price:</strong></td>
                                <td>{{ $product->formatted_selling_price }}</td>
                            </tr>
                            <tr>
                                <td><strong>Profit:</strong></td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $product->formatted_profit }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Created At:</strong></td>
                                <td>{{ $product->created_at->format('d M Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($product->description)
                <div class="mt-3">
                    <h5>Description</h5>
                    <p>{{ $product->description }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Image</h3>
                <small class="text-muted">Click image to zoom</small>
            </div>
            <div class="card-body text-center">
                <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('dist/img/default-150x150.png') }}" 
                     alt="{{ $product->name }}" 
                     class="img-fluid zoomable" 
                     style="max-height: 300px; cursor: zoom-in;"
                     data-zoomable>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Quick Actions</h4>
            </div>
            <div class="card-body">
                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm btn-block mb-2">
                    <i class="fas fa-edit"></i> Edit Product
                </a>
                
                <form action="{{ route('products.destroy', $product) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm btn-block" onclick="return confirm('Are you sure you want to delete this product?')">
                        <i class="fas fa-trash"></i> Delete Product
                    </button>
                </form>
                                        </td>
                                        <td>
                                            <span class="badge badge-success">Sale</span>
                                        </td>
                                        <td>{{ $item->quantity }} {{ $product->unit }}</td>
                                        <td>{{ $item->formatted_price }}</td>
                                        <td>{{ $item->formatted_subtotal }}</td>
                                    </tr>
                                    @endforeach
                                    @if($product->transactionItems()->count() == 0)
                                    <tr>
                                        <td colspan="6" class="text-center">No transactions found</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
