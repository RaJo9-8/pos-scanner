@extends('layouts.app')

@section('title', 'New Transaction')
@section('breadcrumb', 'New Transaction')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
<style>
    .product-item {
        border: 1px solid #ddd;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 5px;
        background-color: #f9f9f9;
    }
    .barcode-scanner {
        border: 2px dashed #007bff;
        padding: 20px;
        text-align: center;
        border-radius: 5px;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">New Transaction</h3>
                <div class="card-tools">
                    <a href="{{ route('transactions.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <form id="transaction-form">
                @csrf
                <div class="card-body">
                    <div class="barcode-scanner">
                        <h4><i class="fas fa-barcode"></i> Barcode Scanner</h4>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" id="barcode-input" class="form-control" placeholder="Scan barcode or type here..." autofocus>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-info btn-block" onclick="searchByBarcode()">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Search Product</label>
                        <select id="product-search" class="form-control" style="width: 100%;">
                            <option></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-success" onclick="addManualProduct()">
                            <i class="fas fa-plus"></i> Add Product Manually
                        </button>
                    </div>

                    <hr>

                    <h4>Cart Items</h4>
                    <div id="cart-items">
                        <!-- Cart items will be added here dynamically -->
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="discount">Discount</label>
                                <input type="number" id="discount" class="form-control" value="0" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tax">Tax</label>
                                <input type="number" id="tax" class="form-control" value="0" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="payment_method">Payment Method</label>
                                <select id="payment_method" class="form-control" required>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="ewallet">E-Wallet</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea id="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Transaction Summary</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tr>
                            <td><strong>Subtotal:</strong></td>
                            <td class="text-right" id="subtotal">Rp 0</td>
                        </tr>
                        <tr>
                            <td><strong>Discount:</strong></td>
                            <td class="text-right" id="discount-display">Rp 0</td>
                        </tr>
                        <tr>
                            <td><strong>Tax:</strong></td>
                            <td class="text-right" id="tax-display">Rp 0</td>
                        </tr>
                        <tr>
                            <td><strong>Total:</strong></td>
                            <td class="text-right" id="total"><strong>Rp 0</strong></td>
                        </tr>
                    </table>
                </div>

                <hr>

                <div class="form-group">
                    <label for="cash_amount">Cash Amount</label>
                    <input type="number" id="cash_amount" class="form-control" value="0" min="0" step="0.01">
                </div>

                <div class="form-group">
                    <label>Change</label>
                    <input type="text" id="change_amount" class="form-control" value="Rp 0" readonly>
                </div>

                <div class="form-group">
                    <button type="button" class="btn btn-primary btn-block" onclick="processTransaction()">
                        <i class="fas fa-shopping-cart"></i> Process Transaction
                    </button>
                </div>

                <div class="form-group">
                    <button type="button" class="btn btn-danger btn-block" onclick="clearCart()">
                        <i class="fas fa-trash"></i> Clear Cart
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manual Product Modal -->
<div class="modal fade" id="manualProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Product Manually</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Product</label>
                    <select id="manual-product-select" class="form-control">
                        <option value="">Select Product</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" id="manual-quantity" class="form-control" value="1" min="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addManualProductToCart()">Add to Cart</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
let cart = [];
let productIdCounter = 0;

$(document).ready(function() {
    // Initialize Select2
    $('#product-search').select2({
        placeholder: 'Search for a product...',
        ajax: {
            url: '{{ route("transactions.search-product") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.id,
                            text: item.name + ' - ' + item.barcode + ' (Stock: ' + item.stock + ' ' + item.unit + ')',
                            product: item
                        };
                    })
                };
            }
        }
    });

    $('#product-search').on('select2:select', function (e) {
        const product = e.params.data.product;
        addToCart(product);
        $(this).val(null).trigger('change');
    });

    // Barcode input enter key
    $('#barcode-input').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            searchByBarcode();
        }
    });

    // Load products for manual selection
    loadManualProducts();

    // Update calculations when discount or tax changes
    $('#discount, #tax').on('input', updateSummary);
    $('#cash_amount').on('input', updateChange);
});

function loadManualProducts() {
    $.get('{{ route("api.products.search") }}', {q: ''}, function(data) {
        let options = '<option value="">Select Product</option>';
        data.forEach(function(product) {
            options += '<option value="' + product.id + '" data-product=\'' + JSON.stringify(product) + '\'">' + 
                       product.name + ' (Stock: ' + product.stock + ')</option>';
        });
        $('#manual-product-select').html(options);
    });
}

function searchByBarcode() {
    const barcode = $('#barcode-input').val();
    if (!barcode) return;

    $.get('{{ route("transactions.get-product-barcode", ":barcode") }}'.replace(':barcode', barcode))
        .done(function(product) {
            addToCart(product);
            $('#barcode-input').val('');
        })
        .fail(function() {
            alert('Product not found or out of stock');
            $('#barcode-input').val('');
        });
}

function addManualProduct() {
    $('#manualProductModal').modal('show');
}

function addManualProductToCart() {
    const productId = $('#manual-product-select').val();
    const quantity = parseInt($('#manual-quantity').val());
    
    if (!productId) {
        alert('Please select a product');
        return;
    }

    const productData = $('#manual-product-select option:selected').data('product');
    productData.quantity = quantity;
    
    addToCart(productData);
    $('#manualProductModal').modal('hide');
    $('#manual-quantity').val(1);
}

function addToCart(product) {
    const existingItem = cart.find(item => item.product_id === product.id);
    
    if (existingItem) {
        if (existingItem.quantity + (product.quantity || 1) > product.stock) {
            alert('Insufficient stock. Available: ' + product.stock);
            return;
        }
        existingItem.quantity += (product.quantity || 1);
        existingItem.subtotal = existingItem.quantity * existingItem.price;
    } else {
        if ((product.quantity || 1) > product.stock) {
            alert('Insufficient stock. Available: ' + product.stock);
            return;
        }
        
        cart.push({
            id: ++productIdCounter,
            product_id: product.id,
            name: product.name,
            barcode: product.barcode,
            price: product.selling_price,
            quantity: product.quantity || 1,
            stock: product.stock,
            unit: product.unit,
            subtotal: (product.quantity || 1) * product.selling_price
        });
    }
    
    renderCart();
    updateSummary();
}

function removeFromCart(itemId) {
    cart = cart.filter(item => item.id !== itemId);
    renderCart();
    updateSummary();
}

function updateQuantity(itemId, quantity) {
    const item = cart.find(item => item.id === itemId);
    if (item) {
        if (quantity > item.stock) {
            alert('Insufficient stock. Available: ' + item.stock);
            renderCart();
            return;
        }
        item.quantity = parseInt(quantity);
        item.subtotal = item.quantity * item.price;
        renderCart();
        updateSummary();
    }
}

function renderCart() {
    const container = $('#cart-items');
    
    if (cart.length === 0) {
        container.html('<p class="text-muted">No items in cart</p>');
        return;
    }
    
    let html = '';
    cart.forEach(function(item) {
        html += `
            <div class="product-item">
                <div class="row">
                    <div class="col-md-6">
                        <strong>${item.name}</strong><br>
                        <small class="text-muted">${item.barcode}</small>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control form-control-sm" value="${item.quantity}" 
                               min="1" max="${item.stock}" onchange="updateQuantity(${item.id}, this.value)">
                        <small class="text-muted">Stock: ${item.stock} ${item.unit}</small>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control form-control-sm" value="${item.price}" 
                               min="0" step="0.01" onchange="updatePrice(${item.id}, this.value)">
                    </div>
                    <div class="col-md-2">
                        <div class="text-right">
                            <strong>Rp ${item.subtotal.toLocaleString('id-ID')}</strong><br>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeFromCart(${item.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

function updatePrice(itemId, price) {
    const item = cart.find(item => item.id === itemId);
    if (item) {
        item.price = parseFloat(price);
        item.subtotal = item.quantity * item.price;
        renderCart();
        updateSummary();
    }
}

function updateSummary() {
    const subtotal = cart.reduce((sum, item) => sum + item.subtotal, 0);
    const discount = parseFloat($('#discount').val()) || 0;
    const tax = parseFloat($('#tax').val()) || 0;
    const total = subtotal - discount + tax;
    
    $('#subtotal').text('Rp ' + subtotal.toLocaleString('id-ID'));
    $('#discount-display').text('Rp ' + discount.toLocaleString('id-ID'));
    $('#tax-display').text('Rp ' + tax.toLocaleString('id-ID'));
    $('#total').text('Rp ' + total.toLocaleString('id-ID'));
    
    updateChange();
}

function updateChange() {
    const totalText = $('#total').text();
    const total = parseFloat(totalText.replace(/[^\d]/g, '')) || 0;
    const cashAmount = parseFloat($('#cash_amount').val()) || 0;
    const change = cashAmount - total;
    
    $('#change_amount').val('Rp ' + change.toLocaleString('id-ID'));
}

function clearCart() {
    if (confirm('Are you sure you want to clear the cart?')) {
        cart = [];
        renderCart();
        updateSummary();
        $('#discount').val(0);
        $('#tax').val(0);
        $('#cash_amount').val(0);
    }
}

function processTransaction() {
    if (cart.length === 0) {
        alert('Please add items to cart');
        return;
    }
    
    const totalText = $('#total').text();
    const total = parseFloat(totalText.replace(/[^\d]/g, '')) || 0;
    const cashAmount = parseFloat($('#cash_amount').val()) || 0;
    
    if (cashAmount < total) {
        alert('Insufficient cash amount');
        return;
    }
    
    const items = cart.map(item => ({
        product_id: item.product_id,
        quantity: item.quantity,
        price: item.price
    }));
    
    const data = {
        items: items,
        cash_amount: cashAmount,
        discount: parseFloat($('#discount').val()) || 0,
        tax: parseFloat($('#tax').val()) || 0,
        payment_method: $('#payment_method').val(),
        notes: $('#notes').val()
    };
    
    $.ajax({
        url: '{{ route("transactions.store") }}',
        method: 'POST',
        data: data,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                alert('Transaction completed successfully!\nInvoice: ' + response.invoice_number);
                window.location.href = '{{ route("transactions.index") }}';
            } else {
                alert('Transaction failed: ' + response.message);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            alert('Transaction failed: ' + (response.message || 'Unknown error'));
        }
    });
}
</script>
@endpush
