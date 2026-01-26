@extends('layouts.app')

@section('title', 'Transaction Details')
@section('breadcrumb', 'Transaction Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Transaction Details</h3>
                <div class="card-tools">
                    <a href="{{ route('transactions.print', $transaction) }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="fas fa-print"></i> Print Invoice
                    </a>
                    <a href="{{ route('transactions.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Invoice Number:</strong></td>
                                <td><code>{{ $transaction->invoice_number }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Date:</strong></td>
                                <td>{{ $transaction->created_at->format('d M Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Cashier:</strong></td>
                                <td>{{ $transaction->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Payment Method:</strong></td>
                                <td>{{ ucfirst($transaction->payment_method) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge badge-{{ $transaction->status == 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Total Amount:</strong></td>
                                <td class="text-primary">{{ $transaction->formatted_total_amount }}</td>
                            </tr>
                            <tr>
                                <td><strong>Cash Amount:</strong></td>
                                <td>{{ $transaction->formatted_cash_amount }}</td>
                            </tr>
                            <tr>
                                <td><strong>Change:</strong></td>
                                <td class="text-success">{{ $transaction->formatted_change_amount }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($transaction->discount > 0 || $transaction->tax > 0)
                <div class="row">
                    <div class="col-12">
                        <table class="table table-borderless">
                            @if($transaction->discount > 0)
                            <tr>
                                <td><strong>Discount:</strong></td>
                                <td class="text-danger">{{ $transaction->formatted_discount }}</td>
                            </tr>
                            @endif
                            @if($transaction->tax > 0)
                            <tr>
                                <td><strong>Tax:</strong></td>
                                <td class="text-info">{{ $transaction->formatted_tax }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
                @endif

                @if($transaction->notes)
                <div class="row">
                    <div class="col-12">
                        <h5>Notes</h5>
                        <p>{{ $transaction->notes }}</p>
                    </div>
                </div>
                @endif

                <hr>

                <h4>Transaction Items</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Product Name</th>
                                <th>Barcode</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transaction->transactionItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td><code>{{ $item->product->barcode }}</code></td>
                                <td>{{ $item->quantity }} {{ $item->product->unit }}</td>
                                <td>{{ $item->formatted_price }}</td>
                                <td class="text-right">{{ $item->formatted_subtotal }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-right">Total:</th>
                                <th class="text-right">{{ $transaction->formatted_total_amount }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <a href="{{ route('transactions.print', $transaction) }}" class="btn btn-success btn-block" target="_blank">
                        <i class="fas fa-print"></i> Print Invoice
                    </a>
                </div>
                
                @if(auth()->user()->canManageTransactions())
                <div class="form-group">
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-block">
                        <i class="fas fa-plus"></i> New Transaction
                    </a>
                </div>
                @endif

                <div class="form-group">
                    <a href="{{ route('transactions.index') }}" class="btn btn-default btn-block">
                        <i class="fas fa-list"></i> View All Transactions
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Transaction Summary</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-success">
                                <i class="fas fa-shopping-cart"></i>
                            </span>
                            <h5 class="description-header">{{ $transaction->transactionItems->count() }}</h5>
                            <span class="description-text">Items</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <span class="description-percentage text-info">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                            <h5 class="description-header">{{ $transaction->formatted_total_amount }}</h5>
                            <span class="description-text">Total Amount</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
