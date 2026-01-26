@extends('layouts.app')

@section('title', 'Return Request Details')
@section('breadcrumb', 'Return Request Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Return Request Details</h3>
                <div class="card-tools">
                    @if(auth()->user()->isLeader() && $returnTransaction->status == 'pending')
                    <form action="{{ route('returns.approve', $returnTransaction) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this return request?')">
                            <i class="fas fa-check"></i> Approve
                        </button>
                    </form>
                    <form action="{{ route('returns.reject', $returnTransaction) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this return request?')">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('returns.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Return Number:</strong></td>
                                <td><code>{{ $returnTransaction->return_number }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Transaction:</strong></td>
                                <td>
                                    <a href="{{ route('transactions.show', $returnTransaction->transaction) }}">
                                        {{ $returnTransaction->transaction->invoice_number }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Customer:</strong></td>
                                <td>{{ $returnTransaction->transaction->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Request Date:</strong></td>
                                <td>{{ $returnTransaction->created_at->format('d M Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Requested By:</strong></td>
                                <td>{{ $returnTransaction->user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status:</strong></td>
                                <td>
                                    <span class="badge badge-{{ $returnTransaction->status == 'approved' ? 'success' : ($returnTransaction->status == 'rejected' ? 'danger' : 'warning') }}">
                                        {{ $returnTransaction->status_text }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Reason:</strong></td>
                                <td>{{ $returnTransaction->reason_text }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Amount:</strong></td>
                                <td class="text-primary">{{ $returnTransaction->formatted_total_amount }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($returnTransaction->description)
                <div class="row">
                    <div class="col-12">
                        <h5>Description</h5>
                        <p>{{ $returnTransaction->description }}</p>
                    </div>
                </div>
                @endif

                <hr>

                <h4>Return Items</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Product Name</th>
                                <th>Return Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($returnItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item['product_name'] }}</td>
                                <td>{{ $item['quantity'] }}</td>
                                <td>{{ number_format($item['price'], 0, ',', '.') }}</td>
                                <td class="text-right">{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total Return Amount:</th>
                                <th class="text-right">{{ $returnTransaction->formatted_total_amount }}</th>
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
                <h3 class="card-title">Transaction Information</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Invoice:</strong></td>
                        <td><code>{{ $returnTransaction->transaction->invoice_number }}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Date:</strong></td>
                        <td>{{ $returnTransaction->transaction->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Cashier:</strong></td>
                        <td>{{ $returnTransaction->transaction->user->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Payment:</strong></td>
                        <td>{{ ucfirst($returnTransaction->transaction->payment_method) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Original Total:</strong></td>
                        <td>{{ $returnTransaction->transaction->formatted_total_amount }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Return Summary</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-info">
                                <i class="fas fa-undo"></i>
                            </span>
                            <h5 class="description-header">{{ count($returnItems) }}</h5>
                            <span class="description-text">Items</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="description-block">
                            <span class="description-percentage text-primary">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                            <h5 class="description-header">{{ $returnTransaction->formatted_total_amount }}</h5>
                            <span class="description-text">Return Value</span>
                        </div>
                    </div>
                </div>

                @if($returnTransaction->status == 'approved')
                <div class="alert alert-success mt-3">
                    <i class="fas fa-check"></i> This return has been approved and stock has been returned.
                </div>
                @elseif($returnTransaction->status == 'rejected')
                <div class="alert alert-danger mt-3">
                    <i class="fas fa-times"></i> This return has been rejected.
                </div>
                @else
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-clock"></i> This return is pending approval.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
