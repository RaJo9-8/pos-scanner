<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Stock Out Report</title>
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
        <h2>LAPORAN BARANG KELUAR</h2>
        <p>Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="summary">
        <span>Total Items: {{ number_format($totalItems) }}</span>
        <span>Transactions: {{ $stockOuts->count() }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kode</th>
                <th>Produk</th>
                <th class="text-right">Qty</th>
                <th>Alasan</th>
                <th>Catatan</th>
                <th>User</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($stockOuts as $stockOut)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $stockOut->date->format('d M Y') }}</td>
                <td>{{ $stockOut->code }}</td>
                <td>{{ $stockOut->product->name }}</td>
                <td class="text-right">{{ number_format($stockOut->quantity) }}</td>
                <td>{{ ucfirst($stockOut->reason) }}</td>
                <td>{{ $stockOut->notes ?: '-' }}</td>
                <td>{{ $stockOut->user->name }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-right">TOTAL:</th>
                <th class="text-right">{{ number_format($totalItems) }}</th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Laporan ini dicetak secara otomatis dari sistem POS Scanner</p>
    </div>
</body>
</html>
