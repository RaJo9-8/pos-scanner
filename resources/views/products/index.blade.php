@extends('layouts.app')

@section('title', 'Products')
@section('breadcrumb', 'Products')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Products</h3>
                <div class="card-tools">
                    @if(auth()->user()->canManageProducts())
                    <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Product
                    </a>
                    @endif
                    
                    @if(auth()->user()->isSuperAdmin())
                    <a href="{{ route('products.trashed') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-trash"></i> Trashed
                    </a>
                    @endif
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
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#products-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("products.index") }}',
        columns: [
            {data: 'id', name: 'id'},
            {data: 'image', name: 'image', orderable: false, searchable: false},
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

// Function to show image modal
function showImageModal(imageSrc, productName) {
    // Create modal HTML
    const modalHtml = `
        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" style="display: none;">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Product Image: ${productName}</h5>
                        <button type="button" class="close" onclick="closeImageModal()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${imageSrc}" alt="${productName}" class="img-fluid" style="max-height: 500px;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeImageModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('imageModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Show modal
    const modal = document.getElementById('imageModal');
    modal.style.display = 'block';
    modal.classList.add('show');
    document.body.classList.add('modal-open');
    
    // Add backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    backdrop.id = 'imageModalBackdrop';
    document.body.appendChild(backdrop);
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    const backdrop = document.getElementById('imageModalBackdrop');
    
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
        setTimeout(() => modal.remove(), 200);
    }
    
    if (backdrop) {
        backdrop.classList.remove('show');
        setTimeout(() => backdrop.remove(), 200);
    }
    
    document.body.classList.remove('modal-open');
}
</script>
@endpush
