<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Return Report</title>
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
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: black;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
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
        <h2>LAPORAN BARANG RETURN</h2>
        <p>Periode: {{ $startDate->format('d M Y') }} - {{ $endDate->format('d M Y') }}</p>
        <p>Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
    </div>

    <div class="summary">
        <span>Total Returns: {{ number_format($totalReturns) }}</span>
        <span>Total Amount: Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
        <span>Transactions: {{ $returns->count() }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Return</th>
                <th>Tanggal</th>
                <th>No Transaksi</th>
                <th>Customer</th>
                <th>Leader</th>
                <th class="text-right">Total</th>
                <th>Alasan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($returns as $return)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $return->return_number }}</td>
                <td>{{ $return->created_at->format('d M Y H:i') }}</td>
                <td>{{ $return->transaction->invoice_number }}</td>
                <td>{{ $return->transaction->user->name }}</td>
                <td>{{ $return->user->name }}</td>
                <td class="text-right">Rp {{ number_format($return->total_amount, 0, ',', '.') }}</td>
                <td>{{ $return->reason_text }}</td>
                <td class="text-center">
                    <span class="badge badge-{{ $return->status == 'approved' ? 'success' : ($return->status == 'rejected' ? 'danger' : 'warning') }}">
                        {{ ucfirst($return->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" class="text-right">TOTAL:</th>
                <th class="text-right">Rp {{ number_format($totalAmount, 0, ',', '.') }}</th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Laporan ini dicetak secara otomatis dari sistem POS Scanner</p>
    </div>
</body>
</html>
