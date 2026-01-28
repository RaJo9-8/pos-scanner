@extends('layouts.app')

@section('title', 'Barang Masuk')
@section('breadcrumb', 'Barang Masuk')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Barang Masuk</h3>
                <div class="card-tools">
                    <a href="{{ route('stock-in.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Tambah Barang Masuk
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped" id="stock-in-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Harga Beli</th>
                            <th>Total Harga</th>
                            <th>Supplier</th>
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
    $('#stock-in-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('stock-in.index') }}',
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'code', name: 'code'},
            {data: 'date', name: 'date'},
            {data: 'product_name', name: 'product_name'},
            {data: 'quantity', name: 'quantity'},
            {data: 'purchase_price', name: 'purchase_price'},
            {data: 'total_price', name: 'total_price'},
            {data: 'supplier', name: 'supplier'},
            {data: 'user_name', name: 'user_name'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[1, 'desc']]
    });

    $(document).on('click', '.delete-btn', function() {
        if (confirm('Apakah Anda yakin ingin menghapus record ini?')) {
            var id = $(this).data('id');
            $.ajax({
                url: '{{ route('stock-in.index') }}/' + id,
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
