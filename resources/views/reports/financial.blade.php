@extends('layouts.app')

@section('title', 'Financial Report')
@section('breadcrumb', 'Financial Report')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Financial Report</h3>
                <div class="card-tools">
                    <form method="GET" action="{{ route('reports.financial') }}" class="form-inline">
                        <div class="input-group input-group-sm mr-2">
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                            <span class="input-group-append">
                                <span class="input-group-text">to</span>
                            </span>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                        <button type="submit" class="btn btn-default btn-sm">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                                <p>Total Revenue</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ number_format($totalCost, 0, ',', '.') }}</h3>
                                <p>Total Cost</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ number_format($totalProfit, 0, ',', '.') }}</h3>
                                <p>Total Profit</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ number_format($profitMargin, 2) }}%</h3>
                                <p>Profit Margin</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Daily Financial Trend</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="financialChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Financial Summary</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Revenue:</strong></td>
                                            <td class="text-right">{{ number_format($totalRevenue, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Cost:</strong></td>
                                            <td class="text-right">{{ number_format($totalCost, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Discount:</strong></td>
                                            <td class="text-right">-{{ number_format($totalDiscount, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tax:</strong></td>
                                            <td class="text-right">+{{ number_format($totalTax, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td><strong>Net Revenue:</strong></td>
                                            <td class="text-right"><strong>{{ number_format($netRevenue, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Profit:</strong></td>
                                            <td class="text-right text-success"><strong>{{ number_format($totalProfit, 0, ',', '.') }}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profit by Product -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Top Profitable Products</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="financialTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Product Name</th>
                                                <th>Quantity Sold</th>
                                                <th>Total Revenue</th>
                                                <th>Total Cost</th>
                                                <th>Total Profit</th>
                                                <th>Profit Margin</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($profitByProduct as $product)
                                            <tr>
                                                <td>{{ $product['product_name'] }}</td>
                                                <td>{{ $product['total_quantity'] }}</td>
                                                <td>{{ number_format($product['total_revenue'], 0, ',', '.') }}</td>
                                                <td>{{ number_format($product['total_cost'], 0, ',', '.') }}</td>
                                                <td class="text-success">{{ number_format($product['total_profit'], 0, ',', '.') }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $product['total_revenue'] > 0 ? ($product['total_profit'] / $product['total_revenue']) * 100 > 20 ? 'success' : 'warning' : 'danger' }}">
                                                        {{ $product['total_revenue'] > 0 ? number_format(($product['total_profit'] / $product['total_revenue']) * 100, 2) : 0 }}%
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

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
$(document).ready(function() {
    $('#financialTable').DataTable({
        "responsive": true,
        "lengthChange": true,
        "autoWidth": false,
        "ordering": true,
        "info": true,
        "pageLength": 25,
        "buttons": [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copy',
                className: 'btn btn-sm btn-secondary'
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-sm btn-success'
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-success'
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-sm btn-danger',
                orientation: 'landscape',
                pageSize: 'A4'
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-sm btn-primary'
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Columns',
                className: 'btn btn-sm btn-info'
            }
        ],
        "dom": 'Bfrtip',
        "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        }
    });
});
</script>
<script>
// Financial Chart
const financialCtx = document.getElementById('financialChart').getContext('2d');
const financialData = @json($dailyFinancial->values()->all());

new Chart(financialCtx, {
    type: 'bar',
    data: {
        labels: financialData.map(item => item.date),
        datasets: [
            {
                label: 'Revenue',
                data: financialData.map(item => item.revenue),
                backgroundColor: 'rgba(75, 192, 192, 0.8)',
                borderColor: 'rgb(75, 192, 192)',
                borderWidth: 1
            },
            {
                label: 'Cost',
                data: financialData.map(item => item.cost),
                backgroundColor: 'rgba(255, 99, 132, 0.8)',
                borderColor: 'rgb(255, 99, 132)',
                borderWidth: 1
            },
            {
                label: 'Profit',
                data: financialData.map(item => item.profit),
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }
        ]
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
        }
    }
});
</script>
@endpush
