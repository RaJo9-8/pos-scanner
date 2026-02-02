@extends('layouts.app')

@section('title', 'Stock In Report')
@section('breadcrumb', 'Stock In Report')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Stock In Report</h3>
                <div class="card-tools">
                    <form method="GET" action="{{ route('reports.stock-in') }}" class="form-inline">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}" placeholder="Start Date">
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}" placeholder="End Date">
                            <span class="input-group-append">
                                <button type="submit" class="btn btn-default btn-flat">
                                    <i class="fas fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <!-- Summary Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-plus-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Items</span>
                                <span class="info-box-number">{{ number_format($totalItems) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Value</span>
                                <span class="info-box-number">Rp {{ number_format($totalValue, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-chart-line"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Average Price</span>
                                <span class="info-box-number">Rp {{ number_format($averagePrice, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fas fa-file-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Transactions</span>
                                <span class="info-box-number">{{ $stockIns->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock In Table -->
                <div class="table-responsive mt-4">
                    <table id="stockInTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Code</th>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Purchase Price</th>
                                <th>Total Price</th>
                                <th>Supplier</th>
                                <th>User</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stockIns as $stockIn)
                            <tr>
                                <td>{{ $stockIn->date->format('d M Y') }}</td>
                                <td>{{ $stockIn->code }}</td>
                                <td>{{ $stockIn->product->name }}</td>
                                <td>{{ number_format($stockIn->quantity) }}</td>
                                <td>Rp {{ number_format($stockIn->purchase_price, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($stockIn->total_price, 0, ',', '.') }}</td>
                                <td>{{ $stockIn->supplier ?: '-' }}</td>
                                <td>{{ $stockIn->user->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#stockInTable').DataTable({
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
