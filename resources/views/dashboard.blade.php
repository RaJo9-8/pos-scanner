@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ number_format($totalProducts) }}</h3>
                <p>Total Products</p>
            </div>
            <div class="icon">
                <i class="fas fa-box"></i>
            </div>
            @if(auth()->user()->level <= 3)
            <a href="{{ route('products.index') }}" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
            </a>
            @endif
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ number_format($lowStockProducts) }}</h3>
                <p>Low Stock Products</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            @if(auth()->user()->level <= 3)
            <a href="{{ route('products.index', ['low_stock' => true]) }}" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
            </a>
            @endif
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>Rp {{ number_format($todaySales, 0, ',', '.') }}</h3>
                <p>Today's Sales</p>
            </div>
            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            @if(auth()->user()->canManageTransactions())
            <a href="{{ route('transactions.index') }}" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
            </a>
            @endif
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ number_format($totalTransactions) }}</h3>
                <p>Total Transactions</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            @if(auth()->user()->canManageTransactions())
            <a href="{{ route('transactions.index') }}" class="small-box-footer">
                More info <i class="fas fa-arrow-circle-right"></i>
            </a>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sales Chart</h3>
                <div class="card-tools">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-default" onclick="updateSalesChart('7')">7 Days</button>
                        <button type="button" class="btn btn-sm btn-default" onclick="updateSalesChart('30')">30 Days</button>
                        <button type="button" class="btn btn-sm btn-default" onclick="updateSalesChart('90')">90 Days</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="salesChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sales Summary</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-success"><i class="fas fa-caret-up"></i> {{ $lastMonthSales > 0 ? round((($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100, 1) : 0 }}%</span>
                            <h5 class="description-header">Rp {{ number_format($thisMonthSales, 0, ',', '.') }}</h5>
                            <span class="description-text">This Month</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <span class="description-percentage text-warning"><i class="fas fa-caret-left"></i> 0%</span>
                            <h5 class="description-header">Rp {{ number_format($todayProfit, 0, ',', '.') }}</h5>
                            <span class="description-text">Today's Profit</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-info"><i class="fas fa-caret-up"></i> {{ $lastMonthSales > 0 ? round((($thisMonthProfit - ($thisMonthSales * 0.3)) / ($thisMonthSales * 0.3)) * 100, 1) : 0 }}%</span>
                            <h5 class="description-header">Rp {{ number_format($thisMonthProfit, 0, ',', '.') }}</h5>
                            <span class="description-text">This Month Profit</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <span class="description-percentage text-danger"><i class="fas fa-caret-down"></i> {{ $pendingReturns }}</span>
                            <h5 class="description-header">{{ $pendingReturns }}</h5>
                            <span class="description-text">Pending Returns</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Transactions</h3>
            </div>
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    @foreach($recentTransactions as $transaction)
                    <li class="item">
                        <div class="product-img">
                            <img src="{{ asset('dist/img/default-150x150.png') }}" alt="Product Image" class="img-size-50">
                        </div>
                        <div class="product-info">
                            <a href="{{ route('transactions.show', $transaction) }}" class="product-title">{{ $transaction->invoice_number }}
                                <span class="badge badge-info float-right">{{ $transaction->formatted_total_amount }}</span></a>
                            <span class="product-description">
                                {{ $transaction->user->name }} - {{ $transaction->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top Products This Month</h3>
            </div>
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    @foreach($topProducts as $product)
                    <li class="item">
                        <div class="product-img">
                            <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('dist/img/default-150x150.png') }}" alt="Product Image" class="img-size-50">
                        </div>
                        <div class="product-info">
                            <a href="{{ route('products.show', $product) }}" class="product-title">{{ $product->name }}
                                <span class="badge badge-warning float-right">{{ $product->total_sold }} sold</span></a>
                            <span class="product-description">
                                Stock: {{ $product->stock }} {{ $product->unit }}
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->level <= 2 || auth()->user()->level == 5)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Activities</h3>
            </div>
            <div class="card-body">
                <div class="timeline timeline-inverse">
                    @foreach($recentActivities as $activity)
                    <div class="time-label">
                        <span class="bg-info">{{ $activity->created_at->format('d M Y') }}</span>
                    </div>
                    <div>
                        <i class="fas fa-circle bg-blue"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">
                                <a href="#">{{ $activity->user->name }}</a> {{ $activity->action }} {{ $activity->module }}
                            </h3>
                            <div class="timeline-body">
                                {{ $activity->description }}
                            </div>
                            <div class="timeline-footer">
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div>
                        <i class="far fa-clock bg-gray"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
let salesChart = null;

function initSalesChart() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    salesChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Sales',
                data: [],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Sales: Rp ' + context.parsed.y.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    });
}

function updateSalesChart(period = '7') {
    fetch(`/api/sales-data?period=${period}`)
        .then(response => response.json())
        .then(data => {
            salesChart.data.labels = data.map(item => item.date);
            salesChart.data.datasets[0].data = data.map(item => item.total);
            salesChart.update();
        });
}

$(document).ready(function() {
    initSalesChart();
    updateSalesChart('7');
});
</script>
@endpush
