@extends('layouts.app')

@section('title', 'Sales Report')
@section('breadcrumb', 'Sales Report')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sales Report</h3>
                <div class="card-tools">
                    <form method="GET" action="{{ route('reports.sales') }}" class="form-inline">
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
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ number_format($totalSales, 0, ',', '.') }}</h3>
                                <p>Total Sales</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $totalTransactions }}</h3>
                                <p>Total Transactions</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ number_format($averageTransaction, 0, ',', '.') }}</h3>
                                <p>Average Transaction</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-primary">
                            <div class="inner">
                                <h3>{{ count($salesByCashier) }}</h3>
                                <p>Active Cashiers</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row mt-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Daily Sales Trend</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="dailySalesChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Payment Methods</h4>
                            </div>
                            <div class="card-body">
                                <canvas id="paymentChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tables -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Sales by Cashier</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Cashier</th>
                                                <th>Transactions</th>
                                                <th>Total Sales</th>
                                                <th>Average</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($salesByCashier as $cashier)
                                            <tr>
                                                <td>{{ $cashier['user']->name }}</td>
                                                <td>{{ $cashier['transaction_count'] }}</td>
                                                <td>{{ number_format($cashier['total_sales'], 0, ',', '.') }}</td>
                                                <td>{{ number_format($cashier['average_transaction'], 0, ',', '.') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Payment Method Summary</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Payment Method</th>
                                                <th>Transactions</th>
                                                <th>Total Sales</th>
                                                <th>Percentage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($salesByPayment as $payment)
                                            <tr>
                                                <td>{{ $payment['payment_method'] }}</td>
                                                <td>{{ $payment['transaction_count'] }}</td>
                                                <td>{{ number_format($payment['total_sales'], 0, ',', '.') }}</td>
                                                <td>{{ number_format($payment['percentage'], 2) }}%</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Recent Transactions</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="salesTable" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Invoice</th>
                                                <th>Date</th>
                                                <th>Cashier</th>
                                                <th>Payment Method</th>
                                                <th>Total Amount</th>
                                                <th>Items</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($transactions as $transaction)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('transactions.show', $transaction) }}">
                                                        {{ $transaction->invoice_number }}
                                                    </a>
                                                </td>
                                                <td>{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                                <td>{{ $transaction->user->name }}</td>
                                                <td>{{ ucfirst($transaction->payment_method) }}</td>
                                                <td>{{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                                                <td>{{ $transaction->transactionItems->count() }}</td>
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
    $('#salesTable').DataTable({
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
// Daily Sales Chart
const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
const dailySalesData = @json($dailySales->values()->all());

new Chart(dailySalesCtx, {
    type: 'line',
    data: {
        labels: dailySalesData.map(item => item.date),
        datasets: [{
            label: 'Daily Sales',
            data: dailySalesData.map(item => item.total_sales),
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
        }
    }
});

// Payment Method Chart
const paymentCtx = document.getElementById('paymentChart').getContext('2d');
const paymentData = @json($salesByPayment->values()->all());

new Chart(paymentCtx, {
    type: 'doughnut',
    data: {
        labels: paymentData.map(item => item.payment_method),
        datasets: [{
            data: paymentData.map(item => item.total_sales),
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush
