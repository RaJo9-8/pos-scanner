<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $transaction->invoice_number }}</title>
    <style>
        /* Preview styles - untuk tampilan di browser */
        @media screen {
            body {
                font-family: 'Courier New', monospace;
                font-size: 8px;
                margin: 20px auto;
                padding: 0; /* Full kertas tanpa padding */
                width: 58mm;
                max-width: 58mm;
                box-sizing: border-box;
                border: 1px solid #ccc;
                background: white;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                overflow: hidden; /* Sembunyikan overflow */
            }
        }
        
        /* Print styles - untuk hasil cetakan PDF */
        @media print {
            body {
                font-family: 'Courier New', monospace;
                font-size: 8px;
                margin: 0;
                padding: 0; /* Full kertas tanpa padding */
                width: 58mm;
                max-width: 58mm;
                box-sizing: border-box;
                overflow: visible; /* Tampilkan semua konten saat print */
            }
            
            /* Pastikan semua konten terlihat saat print */
            * {
                overflow: visible !important;
            }
        }
        
        .invoice-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }
        .invoice-header h2 {
            margin: 0;
            font-size: 12px;
            color: #333;
        }
        .invoice-header p {
            margin: 5px 0;
            font-size: 9px;
        }
        .invoice-info {
            margin-bottom: 15px;
        }
        .invoice-info table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        .invoice-info td {
            padding: 5px;
            border: none;
        }
        .invoice-items {
            margin-bottom: 15px;
        }
        .invoice-items table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        .invoice-items th,
        .invoice-items td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .invoice-items th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .invoice-items .text-right {
            text-align: right;
        }
        .invoice-summary {
            margin-top: 15px;
        }
        .invoice-summary table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        .invoice-summary td {
            padding: 5px;
        }
        .invoice-summary .text-right {
            text-align: right;
        }
        .invoice-summary .total {
            font-weight: bold;
            font-size: 12px;
            border-top: 2px solid #333;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div >
        <h2>POS SCANNER</h2>
        <p>Invoice / Receipt</p>
    </div>

    <div class="invoice-info">
        <table>
            <tr>
                <td><strong>Invoice Number:</strong></td>
                <td>{{ $transaction->invoice_number }}</td>
                <td><strong>Date:</strong></td>
                <td>{{ $transaction->created_at->format('d M Y H:i:s') }}</td>
            </tr>
            <tr>
                <td><strong>Cashier:</strong></td>
                <td>{{ $transaction->user->name }}</td>
                <td><strong>Payment Method:</strong></td>
                <td>{{ ucfirst($transaction->payment_method) }}</td>
            </tr>
        </table>
    </div>

    <div class="invoice-items">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->transactionItems as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="invoice-summary">
        <table>
            @if($transaction->discount > 0)
            <tr>
                <td colspan="4" class="text-right">Subtotal:</td>
                <td class="text-right">{{ number_format($transaction->total_amount + $transaction->discount - $transaction->tax, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right">Discount:</td>
                <td class="text-right">-{{ number_format($transaction->discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($transaction->tax > 0)
            <tr>
                <td colspan="4" class="text-right">Tax:</td>
                <td class="text-right">+{{ number_format($transaction->tax, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="total">
                <td colspan="4" class="text-right"><strong>Total:</strong></td>
                <td class="text-right"><strong>{{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td colspan="4" class="text-right">Cash:</td>
                <td class="text-right">{{ number_format($transaction->cash_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="4" class="text-right">Change:</td>
                <td class="text-right">{{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    @if($transaction->notes)
    <div style="margin-top: 20px;">
        <strong>Notes:</strong> {{ $transaction->notes }}
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your purchase!</p>
        <p>Generated on {{ now()->format('d M Y H:i:s') }}</p>
    </div>
</body>
</html>
