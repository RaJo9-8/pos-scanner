@extends('layouts.app')

@section('title', 'Create Product')
@section('breadcrumb', 'Create Product')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create New Product</h3>
                <div class="card-tools">
                    <a href="{{ route('products.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="barcode">Barcode <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode" name="barcode" value="{{ old('barcode') }}" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-info" onclick="generateBarcode()">
                                            <i class="fas fa-barcode"></i> Generate
                                        </button>
                                    </div>
                                </div>
                                @error('barcode')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category') }}">
                                @error('category')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="unit">Unit <span class="text-danger">*</span></label>
                                <select class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" required>
                                    <option value="">Select Unit</option>
                                    <option value="pcs" {{ old('unit') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                    <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kg</option>
                                    <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liter</option>
                                    <option value="box" {{ old('unit') == 'box' ? 'selected' : '' }}>Box</option>
                                    <option value="pack" {{ old('unit') == 'pack' ? 'selected' : '' }}>Pack</option>
                                </select>
                                @error('unit')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="purchase_price">Purchase Price <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price" name="purchase_price" value="{{ old('purchase_price') }}" step="0.01" min="0" required>
                                @error('purchase_price')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="selling_price">Selling Price <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ old('selling_price') }}" step="0.01" min="0" required>
                                @error('selling_price')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Profit</label>
                                <input type="text" class="form-control" id="profit_display" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="stock">Stock <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required>
                                @error('stock')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="min_stock">Minimum Stock <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('min_stock') is-invalid @enderror" id="min_stock" name="min_stock" value="{{ old('min_stock', 5) }}" min="0" required>
                                @error('min_stock')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                <label class="custom-file-label" for="image">Choose file</label>
                            </div>
                        </div>
                        @error('image')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                        <small class="form-text text-muted">Allowed formats: JPEG, PNG, JPG, GIF. Max size: 2MB</small>
                    </div>

                    <div class="form-group">
                        <img id="image_preview" src="{{ asset('dist/img/default-150x150.png') }}" alt="Image Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Product
                    </button>
                    <a href="{{ route('products.index') }}" class="btn btn-default">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateBarcode() {
    var timestamp = Date.now();
    var barcode = 'PRD' + timestamp.toString().slice(-8);
    document.getElementById('barcode').value = barcode;
}

function calculateProfit() {
    var purchasePrice = parseFloat(document.getElementById('purchase_price').value) || 0;
    var sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
    var profit = sellingPrice - purchasePrice;
    document.getElementById('profit_display').value = 'Rp ' + profit.toLocaleString('id-ID');
}

document.getElementById('purchase_price').addEventListener('input', calculateProfit);
document.getElementById('selling_price').addEventListener('input', calculateProfit);

document.getElementById('image').addEventListener('change', function(e) {
    var file = e.target.files[0];
    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image_preview').src = e.target.result;
        }
        reader.readAsDataURL(file);
        document.querySelector('.custom-file-label').textContent = file.name;
    }
});

// Initialize barcode on page load
generateBarcode();
</script>
@endpush
