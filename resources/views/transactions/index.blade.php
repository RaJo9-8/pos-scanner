@extends('layouts.app')

@section('title', 'Transactions')
@section('breadcrumb', 'Transactions')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Transaction History</h3>
                @if(auth()->user()->canManageTransactions())
                <div class="card-tools">
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> New Transaction
                    </a>
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="transactions-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Date</th>
                                <th>Cashier</th>
                                <th>Total Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
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
    $('#transactions-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("transactions.index") }}',
        columns: [
            {data: 'invoice_number', name: 'invoice_number'},
            {data: 'created_at', name: 'created_at',
                render: function(data) {
                    return moment(data).format('DD MMM YYYY HH:mm');
                }
            },
            {data: 'user_name', name: 'user.name'},
            {data: 'formatted_total_amount', name: 'total_amount'},
            {data: 'payment_method', name: 'payment_method'},
            {data: 'status_badge', name: 'status', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[1, 'desc']]
    });
});
</script>
@endpush
