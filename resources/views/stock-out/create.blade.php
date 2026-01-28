@extends('layouts.app')

@section('title', 'Tambah Barang Keluar')
@section('breadcrumb', 'Tambah Barang Keluar')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Barang Keluar</h3>
                <div class="card-tools">
                    <a href="{{ route('stock-out.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('stock-out.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="barcode">Scan Barcode Produk <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="barcode" name="barcode" placeholder="Scan barcode atau ketik manual" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-info" id="scan-btn">
                                            <i class="fas fa-barcode"></i> Scan
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">Gunakan scanner barcode atau ketik barcode manual</small>
                                @error('product_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                                @error('date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Hidden field untuk product_id -->
                    <input type="hidden" id="product_id" name="product_id" required>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantity">Jumlah <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                                @error('quantity')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reason">Alasan <span class="text-danger">*</span></label>
                                <select class="form-control" id="reason" name="reason" required>
                                    <option value="">-- Pilih Alasan --</option>
                                    <option value="Rusak">Rusak</option>
                                    <option value="Kadaluarsa">Kadaluarsa</option>
                                    <option value="Hilang">Hilang</option>
                                    <option value="Sample">Sample</option>
                                    <option value="Promosi">Promosi</option>
                                    <option value="Retur Supplier">Retur Supplier</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                @error('reason')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Catatan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Catatan tambahan"></textarea>
                        @error('notes')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('stock-out.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Info Produk</h4>
            </div>
            <div class="card-body" id="product-info">
                <p class="text-muted">Pilih produk untuk melihat informasi detail</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Peringatan</h4>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Pastikan jumlah barang keluar tidak melebihi stok yang tersedia.
                </div>
                <p class="text-muted">
                    Barang yang keluar akan mengurangi stok produk secara otomatis.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$(document).ready(function() {
    // Fungsi untuk mencari produk berdasarkan barcode
    function searchProductByBarcode(barcode) {
        if (barcode.length >= 3) {
            $.get('/api/products/barcode/' + barcode, function(data) {
                if (data.success) {
                    // Set product_id
                    $('#product_id').val(data.product.id);
                    
                    // Tampilkan info produk
                    var html = `
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle"></i> Produk Ditemukan!</h6>
                            <table class="table table-sm mb-0">
                                <tr>
                                    <td><strong>Nama:</strong></td>
                                    <td>${data.product.name}</td>
                                </tr>
                                <tr>
                                    <td><strong>Barcode:</strong></td>
                                    <td><code>${data.product.barcode}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Stok Tersedia:</strong></td>
                                    <td class="text-success">${data.product.stock} ${data.product.unit}</td>
                                </tr>
                                <tr>
                                    <td><strong>Harga Jual:</strong></td>
                                    <td>Rp ${parseFloat(data.product.selling_price).toLocaleString('id-ID')}</td>
                                </tr>
                            </table>
                        </div>
                    `;
                    $('#product-info').html(html);
                    
                    // Set max quantity
                    $('#quantity').attr('max', data.product.stock);
                    
                    // Enable form fields
                    $('#quantity, #reason, #notes').prop('disabled', false);
                    $('button[type="submit"]').prop('disabled', false);
                } else {
                    $('#product-info').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i> 
                            Produk dengan barcode "${barcode}" tidak ditemukan!
                        </div>
                    `);
                    $('#product_id').val('');
                    $('#quantity, #reason, #notes').prop('disabled', true);
                    $('button[type="submit"]').prop('disabled', true);
                }
            }).fail(function() {
                $('#product-info').html(`
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Produk dengan barcode "${barcode}" tidak ditemukan!
                    </div>
                `);
                $('#product_id').val('');
                $('#quantity, #reason, #notes').prop('disabled', true);
                $('button[type="submit"]').prop('disabled', true);
            });
        } else {
            $('#product-info').html('<p class="text-muted">Masukkan minimal 3 karakter barcode</p>');
            $('#product_id').val('');
            $('#quantity, #reason, #notes').prop('disabled', true);
            $('button[type="submit"]').prop('disabled', true);
        }
    }

    // Event listener untuk barcode input
    $('#barcode').on('input', function() {
        var barcode = $(this).val();
        searchProductByBarcode(barcode);
    });

    // Event listener untuk scan button
    $('#scan-btn').on('click', function() {
        $('#barcode').focus();
        // Fokus ke input barcode untuk siap menerima scan
    });

    // Auto-submit saat barcode selesai di-scan (biasanya ada enter)
    $('#barcode').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            var barcode = $(this).val();
            if (barcode.length >= 3) {
                searchProductByBarcode(barcode);
                // Pindah fokus ke quantity setelah barcode ditemukan
                setTimeout(function() {
                    $('#quantity').focus();
                }, 500);
            }
        }
    });

    // Disable form fields awal
    $('#quantity, #reason, #notes').prop('disabled', true);
    $('button[type="submit"]').prop('disabled', true);

    // Validate quantity
    $('#quantity').on('input', function() {
        var maxQuantity = parseInt($(this).attr('max')) || 0;
        var currentValue = parseInt($(this).val()) || 0;
        
        if (currentValue > maxQuantity) {
            $(this).val(maxQuantity);
            alert('Jumlah tidak boleh melebihi stok yang tersedia!');
        }
    });

    // Focus ke barcode input saat page load
    $('#barcode').focus();
});
</script>
@endpush