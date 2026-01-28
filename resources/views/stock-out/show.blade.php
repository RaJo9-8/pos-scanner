@extends('layouts.app')

@section('title', 'Detail Barang Keluar')
@section('breadcrumb', 'Detail Barang Keluar')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Barang Keluar</h3>
                <div class="card-tools">
                    <a href="{{ route('stock-out.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                    <form action="{{ route('stock-out.destroy', $stockOut) }}" method="POST" style="display: inline;">
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
                                <td><code>{{ $stockOut->code }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal:</strong></td>
                                <td>{{ $stockOut->date->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Produk:</strong></td>
                                <td>{{ $stockOut->product->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Barcode:</strong></td>
                                <td><code>{{ $stockOut->product->barcode }}</code></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Jumlah:</strong></td>
                                <td class="text-danger">{{ $stockOut->quantity }} {{ $stockOut->product->unit }}</td>
                            </tr>
                            <tr>
                                <td><strong>Alasan:</strong></td>
                                <td><span class="badge badge-warning">{{ $stockOut->reason }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Stok Sebelum:</strong></td>
                                <td>{{ $stockOut->product->stock + $stockOut->quantity }} {{ $stockOut->product->unit }}</td>
                            </tr>
                            <tr>
                                <td><strong>Stok Sesudah:</strong></td>
                                <td class="text-success">{{ $stockOut->product->stock }} {{ $stockOut->product->unit }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($stockOut->notes)
                <div class="row">
                    <div class="col-12">
                        <h5>Catatan</h5>
                        <p>{{ $stockOut->notes }}</p>
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
                                <td>{{ $stockOut->user->name }}</td>
                            </tr>
                            <tr>
                                <td>Email:</td>
                                <td>{{ $stockOut->user->email }}</td>
                            </tr>
                            <tr>
                                <td>Waktu Input:</td>
                                <td>{{ $stockOut->created_at->format('d M Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Info Produk</h5>
                        <table class="table table-sm">
                            <tr>
                                <td>Kategori:</td>
                                <td>{{ $stockOut->product->category ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td>Harga Jual:</td>
                                <td>Rp {{ number_format($stockOut->product->selling_price, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Status Stok:</td>
                                <td>
                                    @if($stockOut->product->isLowStock())
                                        <span class="badge badge-warning">Low Stock</span>
                                    @else
                                        <span class="badge badge-success">Aman</span>
                                    @endif
                                </td>
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
                    <span class="description-percentage text-danger">
                        <i class="fas fa-arrow-down"></i>
                    </span>
                    <h5 class="description-header">{{ $stockOut->quantity }}</h5>
                    <span class="description-text">Jumlah Barang</span>
                </div>
                <div class="description-block">
                    <span class="description-percentage text-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </span>
                    <h5 class="description-header">{{ $stockOut->reason }}</h5>
                    <span class="description-text">Alasan Keluar</span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Aksi Cepat</h4>
            </div>
            <div class="card-body">
                <a href="{{ route('products.show', $stockOut->product) }}" class="btn btn-info btn-sm btn-block mb-2">
                    <i class="fas fa-box"></i> Lihat Produk
                </a>
                <a href="{{ route('stock-in.create') }}?product_id={{ $stockOut->product_id }}" class="btn btn-success btn-sm btn-block mb-2">
                    <i class="fas fa-arrow-up"></i> Barang Masuk
                </a>
                <a href="{{ route('transactions.create') }}?product_id={{ $stockOut->product_id }}" class="btn btn-primary btn-sm btn-block">
                    <i class="fas fa-shopping-cart"></i> Jual Produk
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
