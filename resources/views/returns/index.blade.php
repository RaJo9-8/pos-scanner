@extends('layouts.app')

@section('title', 'Return Requests')
@section('breadcrumb', 'Return Requests')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Return Requests Management</h3>
                @if(auth()->user()->isLeader())
                <div class="card-tools">
                    <a href="{{ route('returns.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> New Return Request
                    </a>
                </div>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="returns-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Return Number</th>
                                <th>Transaction</th>
                                <th>Customer</th>
                                <th>Leader</th>
                                <th>Total Amount</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Date</th>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script>
$(document).ready(function() {
    console.log('Initializing Returns DataTable...');
    
    $('#returns-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("returns.index") }}',
            error: function(xhr, error, code) {
                console.error('DataTables error:', xhr, error, code);
                alert('Error loading returns data: ' + (xhr.responseJSON?.message || error));
            }
        },
        columns: [
            {data: 'return_number', name: 'return_number'},
            {data: 'transaction_invoice', name: 'transaction.invoice_number'},
            {data: 'customer_name', name: 'transaction.user.name'},
            {data: 'leader_name', name: 'user.name'},
            {data: 'formatted_total_amount', name: 'total_amount'},
            {data: 'reason_text', name: 'reason'},
            {data: 'status_badge', name: 'status', orderable: false, searchable: false},
            {data: 'created_at', name: 'created_at',
                render: function(data) {
                    return moment(data).format('DD MMM YYYY HH:mm');
                }
            },
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[7, 'desc']],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy"></i> Copy',
                className: 'btn btn-sm btn-secondary',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-sm btn-success',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-success',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-sm btn-danger',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-sm btn-primary',
                exportOptions: {
                    columns: ':visible:not(:last-child)'
                }
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i> Columns',
                className: 'btn btn-sm btn-info'
            }
        ],
        language: {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        initComplete: function() {
            console.log('Returns DataTable initialized successfully');
        }
    });
});

function approveReturn(id) {
    if (confirm('Are you sure you want to approve this return request?')) {
        $.ajax({
            url: '{{ route("returns.approve", ":id") }}'.replace(':id', id),
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#returns-table').DataTable().ajax.reload();
                alert(response.success || response.message || 'Return request approved successfully');
            },
            error: function(xhr) {
                alert('Error approving return request');
            }
        });
    }
}

function rejectReturn(id) {
    if (confirm('Are you sure you want to reject this return request?')) {
        $.ajax({
            url: '{{ route("returns.reject", ":id") }}'.replace(':id', id),
            method: 'PUT',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#returns-table').DataTable().ajax.reload();
                alert(response.success || response.message || 'Return request rejected successfully');
            },
            error: function(xhr) {
                alert('Error rejecting return request');
            }
        });
    }
}
</script>
@endpush
