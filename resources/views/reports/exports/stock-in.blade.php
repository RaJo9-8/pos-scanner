<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock In Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h2 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }
        .summary span {
            display: inline-block;
            margin-right: 30px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN BARANG MASUK</h2>
        <p>Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="summary">
        <span>Total Items: {{ number_format($totalItems) }}</span>
        <span>Total Value: Rp {{ number_format($totalValue, 0, ',', '.') }}</span>
        <span>Transactions: {{ $stockIns->count() }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kode</th>
                <th>Produk</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga Beli</th>
                <th class="text-right">Total Harga</th>
                <th>Supplier</th>
                <th>User</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($stockIns as $stockIn)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $stockIn->date->format('d M Y') }}</td>
                <td>{{ $stockIn->code }}</td>
                <td>{{ $stockIn->product->name }}</td>
                <td class="text-right">{{ number_format($stockIn->quantity) }}</td>
                <td class="text-right">Rp {{ number_format($stockIn->purchase_price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($stockIn->total_price, 0, ',', '.') }}</td>
                <td>{{ $stockIn->supplier ?: '-' }}</td>
                <td>{{ $stockIn->user->name }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">TOTAL:</th>
                <th class="text-right">{{ number_format($totalItems) }}</th>
                <th></th>
                <th class="text-right">Rp {{ number_format($totalValue, 0, ',', '.') }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Laporan ini dicetak secara otomatis dari sistem POS Scanner</p>
    </div>
</body>
</html>
