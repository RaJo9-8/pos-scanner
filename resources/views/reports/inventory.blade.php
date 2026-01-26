@extends('layouts.app')

@section('title', 'Inventory Report')
@section('breadcrumb', 'Inventory Report')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Inventory Report</h3>
            </div>
            <div class="card-body">
                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $products->count() }}</h3>
                                <p>Total Products</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-box"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ number_format($inventoryValue, 0, ',', '.') }}</h3>
                                <p>Inventory Value</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $lowStockProducts->count() }}</h3>
                                <p>Low Stock Items</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ $outOfStockProducts->count() }}</h3>
                                <p>Out of Stock</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inventory Summary -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Inventory Summary</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Total Cost:</strong></td>
                                            <td class="text-right">{{ number_format($inventoryCost, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Value:</strong></td>
                                            <td class="text-right">{{ number_format($inventoryValue, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Potential Profit:</strong></td>
                                            <td class="text-right text-success">{{ number_format($inventoryProfit, 0, ',', '.') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Category Summary</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th>Category</th>
                                                <th>Products</th>
                                                <th>Total Stock</th>
                                                <th>Total Value</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($categorySummary as $category)
                                            <tr>
                                                <td>{{ $category['category'] }}</td>
                                                <td>{{ $category['total_products'] }}</td>
                                                <td>{{ $category['total_stock'] }}</td>
                                                <td>{{ number_format($category['total_value'], 0, ',', '.') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alert -->
                @if($lowStockProducts->count() > 0)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="text-warning">Low Stock Alert</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Barcode</th>
                                                <th>Current Stock</th>
                                                <th>Min Stock</th>
                                                <th>Unit</th>
                                                <th>Value</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lowStockProducts as $product)
                                            <tr>
                                                <td>{{ $product->name }}</td>
                                                <td><code>{{ $product->barcode }}</code></td>
                                                <td class="text-warning">{{ $product->stock }}</td>
                                                <td>{{ $product->min_stock }}</td>
                                                <td>{{ $product->unit }}</td>
                                                <td>{{ number_format($product->stock * $product->selling_price, 0, ',', '.') }}</td>
                                                <td>
                                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i> Update
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Out of Stock Alert -->
                @if($outOfStockProducts->count() > 0)
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="text-danger">Out of Stock Products</h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    {{ $outOfStockProducts->count() }} products are out of stock and need immediate restocking.
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Barcode</th>
                                                <th>Category</th>
                                                <th>Unit</th>
                                                <th>Last Price</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($outOfStockProducts as $product)
                                            <tr>
                                                <td>{{ $product->name }}</td>
                                                <td><code>{{ $product->barcode }}</code></td>
                                                <td>{{ $product->category ?: '-' }}</td>
                                                <td>{{ $product->unit }}</td>
                                                <td>{{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                                <td>
                                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-plus"></i> Restock
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Top Selling Products -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Top Selling Products</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Total Quantity Sold</th>
                                                <th>Total Revenue</th>
                                                <th>Average Price</th>
                                                <th>Current Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($topSellingProducts as $item)
                                            <tr>
                                                <td>{{ $item->product->name }}</td>
                                                <td>{{ $item->total_quantity }}</td>
                                                <td>{{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                                                <td>{{ number_format($item->total_revenue / $item->total_quantity, 0, ',', '.') }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $item->product->isLowStock() ? 'warning' : 'success' }}">
                                                        {{ $item->product->stock }} {{ $item->product->unit }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
