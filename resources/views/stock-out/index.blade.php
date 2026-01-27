@extends('layouts.app')

@section('title', 'Barang Keluar')
@section('breadcrumb', 'Barang Keluar')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Barang Keluar</h3>
                <div class="card-tools">
                    <a href="{{ route('stock-out.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Barang Keluar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="stock-out-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Alasan</th>
                            <th>Petugas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#stock-out-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('stock-out.index') }}',
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'code', name: 'code'},
            {data: 'date', name: 'date'},
            {data: 'product_name', name: 'product_name'},
            {data: 'quantity', name: 'quantity'},
            {data: 'reason', name: 'reason'},
            {data: 'user_name', name: 'user_name'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[1, 'desc']]
    });

    $(document).on('click', '.delete-btn', function() {
        if (confirm('Apakah Anda yakin ingin menghapus record ini?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '{{ route('stock-out.index') }}/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        }
    });
});
</script>
@endpush
