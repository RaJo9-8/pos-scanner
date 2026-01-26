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
                                <i class="fas fa-exclamation"></i>
                                {{ $product->min_stock }} {{ $product->unit }}
                            </span>
                            <h5 class="description-header">Min Stock</h5>
                            <span class="description-text">Minimum Level</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Information</h3>
                <div class="card-tools">
                    @if(auth()->user()->level <= 3)
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    @endif
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
                                <td><strong>Product Name:</strong></td>
                                <td>{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Barcode:</strong></td>
                                <td>
                                    <code>{{ $product->barcode }}</code>
                                    <button class="btn btn-sm btn-info ml-2" onclick="copyBarcode()">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Category:</strong></td>
                                <td>{{ $product->category ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Unit:</strong></td>
                                <td>{{ $product->unit }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Purchase Price:</strong></td>
                                <td class="text-primary">{{ $product->formatted_purchase_price }}</td>
                            </tr>
                            <tr>
                                <td><strong>Selling Price:</strong></td>
                                <td class="text-success">{{ $product->formatted_selling_price }}</td>
                            </tr>
                            <tr>
                                <td><strong>Profit per Unit:</strong></td>
                                <td class="text-info">{{ $product->formatted_profit }}</td>
                            </tr>
                            <tr>
                                <td><strong>Profit Margin:</strong></td>
                                <td>{{ $product->selling_price > 0 ? round(($product->profit / $product->selling_price) * 100, 2) : 0 }}%</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($product->description)
                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Description</h5>
                        <p>{{ $product->description }}</p>
                    </div>
                </div>
                @endif

                <div class="row mt-3">
                    <div class="col-12">
                        <h5>Transaction History</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Invoice</th>
                                        <th>Type</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->transactionItems()->with('transaction')->latest()->limit(10)->get() as $item)
                                    <tr>
                                        <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <a href="{{ route('transactions.show', $item->transaction) }}">
                                                {{ $item->transaction->invoice_number }}
                                            </a>
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

@push('scripts')
<script>
function copyBarcode() {
    var barcode = '{{ $product->barcode }}';
    navigator.clipboard.writeText(barcode).then(function() {
        alert('Barcode copied to clipboard: ' + barcode);
    });
}
</script>
@endpush
