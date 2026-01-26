@extends('layouts.app')

@section('title', 'Products')
@section('breadcrumb', 'Products')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Product Management</h3>
                <div class="card-tools">
                    @if(auth()->user()->level <= 3)
                    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                    @endif
                    
                    <a href="{{ route('products.trashed') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-trash"></i> Trashed
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="products-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Barcode</th>
                                <th>Category</th>
                                <th>Stock</th>
                                <th>Purchase Price</th>
                                <th>Selling Price</th>
                                <th>Profit</th>
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
    $('#products-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("products.index") }}',
        columns: [
            {data: 'id', name: 'id'},
            {data: 'image', name: 'image', orderable: false, searchable: false,
                render: function(data) {
                    if (data) {
                        return '<img src="{{ asset("storage") }}/' + data + '" width="50" height="50">';
                    }
                    return '<img src="{{ asset("dist/img/default-150x150.png") }}" width="50" height="50">';
                }
            },
            {data: 'name', name: 'name'},
            {data: 'barcode', name: 'barcode'},
            {data: 'category', name: 'category'},
            {data: 'stock', name: 'stock'},
            {data: 'formatted_purchase_price', name: 'purchase_price'},
            {data: 'formatted_selling_price', name: 'selling_price'},
            {data: 'profit', name: 'profit'},
            {data: 'stock_status', name: 'stock_status', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[0, 'desc']]
    });

    $(document).on('click', '.delete-product', function() {
        var productId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this product?')) {
            $.ajax({
                url: '{{ route("products.destroy", ":id") }}'.replace(':id', productId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#products-table').DataTable().ajax.reload();
                    alert(response.success);
                },
                error: function(xhr) {
                    alert('Error deleting product');
                }
            });
        }
    });
});
</script>
@endpush
