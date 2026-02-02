<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReturnTransaction;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ReturnTransaction::with(['transaction.user', 'user']);
            
            // Debug: Log query count
            \Log::info('Returns count: ' . $query->count());
            
            return DataTables::of($query)
                ->addColumn('action', function ($return) {
                    $buttons = '';
                    $buttons .= '<a href="' . route('returns.show', $return) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    
                    if (auth()->user()->canManageReturns() && $return->status == 'pending') {
                        $buttons .= '<button type="button" class="btn btn-sm btn-success ml-1" onclick="approveReturn(' . $return->id . ')"><i class="fas fa-check"></i></button>';
                        $buttons .= '<button type="button" class="btn btn-sm btn-danger ml-1" onclick="rejectReturn(' . $return->id . ')"><i class="fas fa-times"></i></button>';
                    }
                    
                    return $buttons;
                })
                ->addColumn('formatted_total_amount', function ($return) {
                    return $return->formatted_total_amount;
                })
                ->addColumn('transaction_invoice', function ($return) {
                    return $return->transaction ? $return->transaction->invoice_number : 'N/A';
                })
                ->addColumn('customer_name', function ($return) {
                    return $return->transaction && $return->transaction->user ? $return->transaction->user->name : 'N/A';
                })
                ->addColumn('leader_name', function ($return) {
                    return $return->user ? $return->user->name : 'N/A';
                })
                ->addColumn('status_badge', function ($return) {
                    $badgeClass = $return->status == 'approved' ? 'success' : 
                                 ($return->status == 'rejected' ? 'danger' : 'warning');
                    return '<span class="badge badge-' . $badgeClass . '">' . $return->status_text . '</span>';
                })
                ->addColumn('reason_text', function ($return) {
                    return $return->reason_text;
                })
                ->rawColumns(['action', 'status_badge'])
                ->make(true);
        }

        return view('returns.index');
    }

    public function create()
    {
        $transactions = Transaction::with('transactionItems.product')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $selectedTransaction = null;
        if (request()->has('transaction_id')) {
            $selectedTransaction = Transaction::with('transactionItems.product')
                ->findOrFail(request('transaction_id'));
        }
        
        return view('returns.create', compact('transactions', 'selectedTransaction'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'items' => 'required|array|min:1',
            'items.*.transaction_item_id' => 'required|exists:transaction_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'reason' => 'required|in:defective,wrong_item,customer_request,expired,other',
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $transaction = Transaction::findOrFail($request->transaction_id);
            $totalAmount = 0;
            $returnItems = [];

            foreach ($request->items as $item) {
                $transactionItem = TransactionItem::findOrFail($item['transaction_item_id']);
                $product = $transactionItem->product;
                
                if ($item['quantity'] > $transactionItem->quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Return quantity cannot exceed original quantity for product: {$product->name}"
                    ], 400);
                }

                $subtotal = $item['quantity'] * $transactionItem->price;
                $totalAmount += $subtotal;

                $returnItems[] = [
                    'transaction_item_id' => $transactionItem->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'price' => $transactionItem->price,
                    'subtotal' => $subtotal,
                ];
            }

            $returnTransaction = ReturnTransaction::create([
                'transaction_id' => $transaction->id,
                'user_id' => auth()->id(),
                'total_amount' => $totalAmount,
                'reason' => $request->reason,
                'description' => $request->description,
                'status' => 'pending',
                'notes' => json_encode($returnItems),
            ]);

            ActivityLog::log('create', 'return', "Created return request: {$returnTransaction->return_number}");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Return request created successfully',
                'return_id' => $returnTransaction->id,
                'return_number' => $returnTransaction->return_number
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Return request failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(ReturnTransaction $returnTransaction)
    {
        $returnTransaction->load(['transaction.transactionItems.product', 'transaction.user', 'user']);
        $returnItems = json_decode($returnTransaction->notes, true) ?? [];
        
        return view('returns.show', compact('returnTransaction', 'returnItems'));
    }

    public function approve(ReturnTransaction $returnTransaction)
    {
        if ($returnTransaction->status != 'pending') {
            return redirect()->back()->with('error', 'Return request cannot be approved');
        }

        try {
            DB::beginTransaction();

            $returnItems = json_decode($returnTransaction->notes, true) ?? [];

            foreach ($returnItems as $item) {
                $product = Product::findOrFail($item['product_id']);
                $product->increment('stock', $item['quantity']);
                
                // Buat data barang masuk otomatis saat return disetujui
                StockIn::create([
                    'code' => StockIn::generateCode(),
                    'product_id' => $item['product_id'],
                    'user_id' => auth()->id(),
                    'quantity' => $item['quantity'],
                    'purchase_price' => $product->purchase_price,
                    'total_price' => $product->purchase_price * $item['quantity'],
                    'supplier' => 'Return Barang',
                    'date' => now(),
                ]);
            }

            $returnTransaction->update([
                'status' => 'approved'
            ]);

            ActivityLog::log('approve', 'return', "Approved return request: {$returnTransaction->return_number}");

            DB::commit();

            return redirect()->route('returns.index')->with('success', 'Return request approved successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve return request: ' . $e->getMessage());
        }
    }

    public function reject(ReturnTransaction $returnTransaction)
    {
        if ($returnTransaction->status != 'pending') {
            return redirect()->back()->with('error', 'Return request cannot be rejected');
        }

        $returnTransaction->update([
            'status' => 'rejected'
        ]);

        ActivityLog::log('reject', 'return', "Rejected return request: {$returnTransaction->return_number}");

        return redirect()->route('returns.index')->with('success', 'Return request rejected successfully');
    }

    public function getTransactionItems($transactionId)
    {
        $transaction = Transaction::with('transactionItems.product')
            ->findOrFail($transactionId);

        $items = $transaction->transactionItems->map(function ($item) {
            return [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'product' => $item->product,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $item->subtotal,
                'formatted_price' => $item->formatted_price,
                'formatted_subtotal' => $item->formatted_subtotal,
            ];
        });

        return response()->json($items);
    }
}
