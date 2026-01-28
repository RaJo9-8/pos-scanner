<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Dompdf\Dompdf;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Transaction::with('user');
            
            return DataTables::of($query)
                ->addColumn('action', function ($transaction) {
                    $buttons = '';
                    $buttons .= '<a href="' . route('transactions.show', $transaction) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    $buttons .= '<a href="' . route('transactions.print', $transaction) . '" class="btn btn-sm btn-success" target="_blank"><i class="fas fa-print"></i></a>';
                    
                    return $buttons;
                })
                ->addColumn('formatted_total_amount', function ($transaction) {
                    return $transaction->formatted_total_amount;
                })
                ->addColumn('user_name', function ($transaction) {
                    return $transaction->user->name;
                })
                ->addColumn('status_badge', function ($transaction) {
                    $badgeClass = $transaction->status == 'completed' ? 'success' : 'warning';
                    return '<span class="badge badge-' . $badgeClass . '">' . ucfirst($transaction->status) . '</span>';
                })
                ->addColumn('payment_method', function ($transaction) {
                    return ucfirst($transaction->payment_method);
                })
                ->rawColumns(['action', 'status_badge'])
                ->make(true);
        }

        return view('transactions.index');
    }

    public function create()
    {
        return view('transactions.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'cash_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'payment_method' => 'required|in:cash,card,transfer,ewallet',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $transactionItems = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product: {$product->name}. Available: {$product->stock}, Required: {$item['quantity']}"
                    ], 400);
                }

                $subtotal = $item['quantity'] * $item['price'];
                $totalAmount += $subtotal;

                $transactionItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $subtotal,
                    'discount' => $item['discount'] ?? 0,
                ];
            }

            $totalAmount = $totalAmount - $request->discount + $request->tax;
            $changeAmount = $request->cash_amount - $totalAmount;

            if ($request->cash_amount < $totalAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient cash amount'
                ], 400);
            }

            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'total_amount' => $totalAmount,
                'cash_amount' => $request->cash_amount,
                'change_amount' => $changeAmount,
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax ?? 0,
                'payment_method' => $request->payment_method,
                'status' => 'completed',
                'notes' => $request->notes,
            ]);

            foreach ($transactionItems as $item) {
                $transaction->transactionItems()->create($item);
            }

            ActivityLog::log('create', 'transaction', "Created transaction: {$transaction->invoice_number}");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction completed successfully',
                'transaction_id' => $transaction->id,
                'invoice_number' => $transaction->invoice_number
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Transaction failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['transactionItems.product', 'user']);
        return view('transactions.show', compact('transaction'));
    }

    public function print($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->load(['transactionItems.product', 'user']);
        
        // Kertas thermal standar 58mm x 200mm
        // 58mm = 164.41 points, 200mm = 566.93 points
        $customPaper = array(0, 0, 164.41, 566.93); // 58mm x 200mm in points
        
        $pdf = new Dompdf();
        $pdf->loadHtml(view('transactions.print', compact('transaction'))->render());
        $pdf->setPaper($customPaper, 'portrait');
        $pdf->render();
        
        return $pdf->stream('invoice_' . $transaction->invoice_number . '.pdf');
    }

    public function searchProduct(Request $request)
    {
        $search = $request->get('q');
        $products = Product::where('name', 'like', "%{$search}%")
            ->orWhere('barcode', 'like', "%{$search}%")
            ->where('stock', '>', 0)
            ->limit(10)
            ->get(['id', 'name', 'barcode', 'selling_price', 'stock', 'unit']);

        return response()->json($products);
    }

    public function getProductByBarcode($barcode)
    {
        $product = Product::where('barcode', $barcode)
            ->where('stock', '>', 0)
            ->first();
        
        if (!$product) {
            return response()->json(['error' => 'Product not found or out of stock'], 404);
        }

        return response()->json($product);
    }
}
