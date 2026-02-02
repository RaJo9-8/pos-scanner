@extends('layouts.app')

@section('title', 'Return Report')
@section('breadcrumb', 'Return Report')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Return Report</h3>
                <div class="card-tools">
                    <form method="GET" action="{{ route('reports.return') }}" class="form-inline">
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
                            <span class="info-box-icon bg-warning"><i class="fas fa-undo"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Returns</span>
                                <span class="info-box-number">{{ number_format($totalReturns) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-danger"><i class="fas fa-dollar-sign"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Amount</span>
                                <span class="info-box-number">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-chart-line"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Average Return</span>
                                <span class="info-box-number">Rp {{ number_format($averageReturn, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-primary"><i class="fas fa-file-alt"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Transactions</span>
                                <span class="info-box-number">{{ $returns->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Return Table -->
                <div class="table-responsive mt-4">
                    <table id="returnTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No Return</th>
                                <th>Tanggal</th>
                                <th>No Transaksi</th>
                                <th>Customer</th>
                                <th>Leader</th>
                                <th>Total</th>
                                <th>Alasan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($returns as $return)
                            <tr>
                                <td>{{ $return->return_number }}</td>
                                <td>{{ $return->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $return->transaction->invoice_number }}</td>
                                <td>{{ $return->transaction->user->name }}</td>
                                <td>{{ $return->user->name }}</td>
                                <td>Rp {{ number_format($return->total_amount, 0, ',', '.') }}</td>
                                <td>{{ $return->reason_text }}</td>
                                <td>
                                    <span class="badge badge-{{ $return->status == 'approved' ? 'success' : ($return->status == 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($return->status) }}
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
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#returnTable').DataTable({
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
@endpush
