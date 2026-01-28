@extends('layouts.app')

@section('title', 'Detail Barang Masuk')
@section('breadcrumb', 'Detail Barang Masuk')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Barang Masuk</h3>
                <div class="card-tools">
                    <a href="{{ route('stock-in.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <form action="{{ route('stock-in.destroy', $stockIn) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus record ini?')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Kode:</strong></td>
                                <td><code>{{ $stockIn->code }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal:</strong></td>
                                <td>{{ $stockIn->date->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Produk:</strong></td>
                                <td>{{ $stockIn->product->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Barcode:</strong></td>
                                <td><code>{{ $stockIn->product->barcode }}</code></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Jumlah:</strong></td>
                                <td>{{ $stockIn->quantity }} {{ $stockIn->product->unit }}</td>
                            </tr>
                            <tr>
                                <td><strong>Harga Beli:</strong></td>
                                <td>Rp {{ $stockIn->formatted_purchase_price }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Harga:</strong></td>
                                <td class="text-primary">Rp {{ $stockIn->formatted_total_price }}</td>
                            </tr>
                            <tr>
                                <td><strong>Supplier:</strong></td>
                                <td>{{ $stockIn->supplier ?: '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($stockIn->notes)
                <div class="row">
                    <div class="col-12">
                        <h5>Catatan</h5>
                        <p>{{ $stockIn->notes }}</p>
                    </div>
                </div>
                @endif

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h5>Info Petugas</h5>
                        <table class="table table-sm">
                            <tr>
                                <td>Nama:</td>
                                <td>{{ $stockIn->user->name }}</td>
                            </tr>
                            <tr>
                                <td>Email:</td>
                                <td>{{ $stockIn->user->email }}</td>
                            </tr>
                            <tr>
                                <td>Waktu Input:</td>
                                <td>{{ $stockIn->created_at->format('d M Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Info Produk</h5>
                        <table class="table table-sm">
                            <tr>
                                <td>Kategori:</td>
                                <td>{{ $stockIn->product->category ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td>Stok Sebelum:</td>
                                <td>{{ $stockIn->product->stock - $stockIn->quantity }} {{ $stockIn->product->unit }}</td>
                            </tr>
                            <tr>
                                <td>Stok Sesudah:</td>
                                <td class="text-success">{{ $stockIn->product->stock }} {{ $stockIn->product->unit }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Ringkasan</h4>
            </div>
            <div class="card-body">
                <div class="description-block border-right">
                    <span class="description-percentage text-success">
                        <i class="fas fa-arrow-up"></i>
                    </span>
                    <h5 class="description-header">{{ $stockIn->quantity }}</h5>
                    <span class="description-text">Jumlah Barang</span>
                </div>
                <div class="description-block">
                    <span class="description-percentage text-primary">
                        <i class="fas fa-dollar-sign"></i>
                    </span>
                    <h5 class="description-header">Rp {{ $stockIn->formatted_total_price }}</h5>
                    <span class="description-text">Total Harga</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Aksi Cepat</h4>
            </div>
            <div class="card-body">
                <a href="{{ route('products.show', $stockIn->product) }}" class="btn btn-info btn-sm btn-block mb-2">
                    <i class="fas fa-box"></i> Lihat Produk
                </a>
                <a href="{{ route('stock-out.create') }}?product_id={{ $stockIn->product_id }}" class="btn btn-warning btn-sm btn-block mb-2">
                    <i class="fas fa-arrow-down"></i> Barang Keluar
                </a>
                <a href="{{ route('transactions.create') }}?product_id={{ $stockIn->product_id }}" class="btn btn-success btn-sm btn-block">
                    <i class="fas fa-shopping-cart"></i> Jual Produk
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
