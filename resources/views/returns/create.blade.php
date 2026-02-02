@extends('layouts.app')

@section('title', 'Create Return Request')
@section('breadcrumb', 'Create Return Request')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create New Return Request</h3>
                <div class="card-tools">
                    <a href="{{ route('returns.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <form id="return-form">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="transaction_id">Select Transaction <span class="text-danger">*</span></label>
                        <select id="transaction_id" name="transaction_id" class="form-control" required>
                            <option value="">Select Transaction</option>
                            @foreach($transactions as $transaction)
                            <option value="{{ $transaction->id }}" {{ $selectedTransaction && $selectedTransaction->id == $transaction->id ? 'selected' : '' }}>
                                {{ $transaction->invoice_number }} - {{ $transaction->user->name }} - 
                                {{ $transaction->formatted_total_amount }} ({{ $transaction->created_at->format('d M Y') }})
                            </option>
                            @endforeach
                        </select>
                        @if($selectedTransaction)
                        <small class="text-info">Transaction pre-selected: {{ $selectedTransaction->invoice_number }}</small>
                        @endif
                    </div>

                    <div id="transaction-items" style="display: none;">
                        <h4>Transaction Items</h4>
                        <div id="items-container"></div>
                    </div>

                    <div class="form-group">
                        <label for="reason">Return Reason <span class="text-danger">*</span></label>
                        <select id="reason" name="reason" class="form-control" required>
                            <option value="">Select Reason</option>
                            <option value="defective">Barang Rusak</option>
                            <option value="wrong_item">Salah Barang</option>
                            <option value="customer_request">Permintaan Customer</option>
                            <option value="expired">Kadaluarsa</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <h4>Return Summary</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Return Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="return-items-summary">
                                <tr><td colspan="3" class="text-center text-muted">No items selected</td></tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-right">Total:</th>
                                    <th class="text-right" id="total-return-amount">Rp 0</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-primary" onclick="submitReturn()">Submit Return Request</button>
                    <a href="{{ route('returns.index') }}" class="btn btn-default">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let returnItems = [];
let transactionItems = [];

$(document).ready(function() {
    $('#transaction_id').on('change', function() {
        const transactionId = $(this).val();
        if (transactionId) {
            loadTransactionItems(transactionId);
        } else {
            $('#transaction-items').hide();
            $('#items-container').empty();
            returnItems = [];
            updateReturnSummary();
        }
    });
    
    // Auto-load if transaction is pre-selected
    @if($selectedTransaction)
        loadTransactionItems({{ $selectedTransaction->id }});
    @endif
});

function loadTransactionItems(transactionId) {
    console.log('Loading items for transaction:', transactionId);
    $.get('{{ route("returns.get-transaction-items", ":id") }}'.replace(':id', transactionId))
        .done(function(data) {
            console.log('Transaction items loaded:', data);
            transactionItems = data;
            renderTransactionItems();
            $('#transaction-items').show();
        })
        .fail(function(xhr) {
            console.error('Failed to load transaction items:', xhr);
            alert('Failed to load transaction items: ' + (xhr.responseJSON?.message || 'Unknown error'));
        });
}

function renderTransactionItems() {
    const container = $('#items-container');
    let html = '';

    transactionItems.forEach(function(item) {
        html += `
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label>${item.product_name}</label>
                        <small class="text-muted d-block">Barcode: ${item.product.barcode}</small>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control" id="return-qty-${item.id}" 
                               max="${item.quantity}" min="0" value="0" 
                               onchange="updateReturnItem(${item.id}, this.value)">
                        <small>Max: ${item.quantity}</small>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" value="${item.formatted_price}" readonly>
                    </div>
                </div>
            </div>
        `;
    });

    container.html(html);
}

function updateReturnItem(itemId, quantity) {
    const qty = parseInt(quantity) || 0;
    const item = transactionItems.find(i => i.id === itemId);
    
    if (!item) return;

    if (qty > item.quantity) {
        alert('Return quantity cannot exceed original quantity');
        $(`#return-qty-${itemId}`).val(item.quantity);
        return;
    }

    const existingIndex = returnItems.findIndex(i => i.transaction_item_id === itemId);
    
    if (qty > 0) {
        const subtotal = qty * item.price;
        if (existingIndex >= 0) {
            returnItems[existingIndex].quantity = qty;
            returnItems[existingIndex].subtotal = subtotal;
        } else {
            returnItems.push({
                transaction_item_id: itemId,
                quantity: qty,
                subtotal: subtotal
            });
        }
    } else if (existingIndex >= 0) {
        returnItems.splice(existingIndex, 1);
    }

    updateReturnSummary();
}

function updateReturnSummary() {
    const tbody = $('#return-items-summary');
    
    if (returnItems.length === 0) {
        tbody.html('<tr><td colspan="3" class="text-center text-muted">No items selected</td></tr>');
        $('#total-return-amount').text('Rp 0');
        return;
    }

    let html = '';
    let total = 0;

    returnItems.forEach(function(returnItem) {
        const item = transactionItems.find(i => i.id === returnItem.transaction_item_id);
        if (item) {
            html += `
                <tr>
                    <td>${item.product_name}</td>
                    <td>${returnItem.quantity}</td>
                    <td class="text-right">Rp ${returnItem.subtotal.toLocaleString('id-ID')}</td>
                </tr>
            `;
            total += returnItem.subtotal;
        }
    });

    tbody.html(html);
    $('#total-return-amount').text('Rp ' + total.toLocaleString('id-ID'));
}

function submitReturn() {
    const transactionId = $('#transaction_id').val();
    const reason = $('#reason').val();
    const description = $('#description').val();

    if (!transactionId) {
        alert('Please select a transaction');
        return;
    }

    if (returnItems.length === 0) {
        alert('Please select items to return');
        return;
    }

    if (!reason) {
        alert('Please select a return reason');
        return;
    }

    const data = {
        transaction_id: transactionId,
        items: returnItems,
        reason: reason,
        description: description
    };

    $.ajax({
        url: '{{ route("returns.store") }}',
        method: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                alert('Return request created successfully!\nReturn Number: ' + response.return_number);
                window.location.href = '{{ route("returns.index") }}';
            } else {
                alert('Failed to create return request: ' + response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert('Failed to create return request: ' + (response.message || 'Unknown error'));
        }
    });
}
</script>
@endpush
