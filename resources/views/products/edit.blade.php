@extends('layouts.app')

@section('title', 'Edit Product')
@section('breadcrumb', 'Edit Product')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Product: {{ $product->name }}</h3>
                <div class="card-tools">
                    <a href="{{ route('products.show', $product) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="barcode">Barcode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}" required>
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
                                <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category', $product->category) }}">
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
                                    <option value="pcs" {{ old('unit', $product->unit) == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                    <option value="kg" {{ old('unit', $product->unit) == 'kg' ? 'selected' : '' }}>Kg</option>
                                    <option value="liter" {{ old('unit', $product->unit) == 'liter' ? 'selected' : '' }}>Liter</option>
                                    <option value="box" {{ old('unit', $product->unit) == 'box' ? 'selected' : '' }}>Box</option>
                                    <option value="pack" {{ old('unit', $product->unit) == 'pack' ? 'selected' : '' }}>Pack</option>
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
                                <input type="number" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" step="0.01" min="0" required>
                                @error('purchase_price')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="selling_price">Selling Price <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" step="0.01" min="0" required>
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
                                <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required>
                                @error('stock')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="min_stock">Minimum Stock <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('min_stock') is-invalid @enderror" id="min_stock" name="min_stock" value="{{ old('min_stock', $product->min_stock) }}" min="0" required>
                                @error('min_stock')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
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
                        
                        @if($product->image)
                        <div class="mt-2">
                            <small class="text-muted">Current image:</small>
                            <br>
                            <img src="{{ asset('storage/'.$product->image) }}" alt="Current Image" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <img id="image_preview" src="{{ $product->image ? asset('storage/'.$product->image) : asset('dist/img/default-150x150.png') }}" alt="Image Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Product
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

// Initialize profit calculation
calculateProfit();
</script>
@endpush
