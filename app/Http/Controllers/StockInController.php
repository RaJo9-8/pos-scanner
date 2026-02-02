<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockIn;
use App\Models\Product;
use App\Models\ActivityLog;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $stockIn = StockIn::with(['product', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                return DataTables::of($stockIn)
                    ->addIndexColumn()
                    ->addColumn('product_name', function ($row) {
                        return $row->product ? $row->product->name : 'N/A';
                    })
                    ->addColumn('user_name', function ($row) {
                        return $row->user ? $row->user->name : 'N/A';
                    })
                    ->addColumn('date', function ($row) {
                        return $row->date ? $row->date->format('d M Y') : 'N/A';
                    })
                    ->addColumn('purchase_price', function ($row) {
                        return 'Rp ' . number_format($row->purchase_price, 0, ',', '.');
                    })
                    ->addColumn('total_price', function ($row) {
                        return 'Rp ' . number_format($row->total_price, 0, ',', '.');
                    })
                    ->addColumn('supplier', function ($row) {
                        return $row->supplier ?? 'N/A';
                    })
                    ->addColumn('action', function ($row) {
                        $btn = '<a href="' . route('stock-in.show', $row) . '" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>';
                        if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) {
                            $btn .= ' <button class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return view('stock-in.index');
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('stock-in.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'purchase_price' => 'required|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $product = Product::findOrFail($request->product_id);
        $totalPrice = $request->quantity * $request->purchase_price;

        $stockIn = StockIn::create([
            'code' => StockIn::generateCode(),
            'product_id' => $request->product_id,
            'user_id' => auth()->id(),
            'quantity' => $request->quantity,
            'purchase_price' => $request->purchase_price,
            'total_price' => $totalPrice,
            'supplier' => $request->supplier,
            'notes' => $request->notes,
            'date' => $request->date,
        ]);

        $product->increment('stock', $request->quantity);

        ActivityLog::log('stock_in', 'inventory', 
            "Stock in {$stockIn->code}: {$request->quantity} units of {$product->name}",
            null,
            [
                'stock_in_code' => $stockIn->code,
                'product_name' => $product->name,
                'quantity' => $request->quantity,
                'total_price' => $totalPrice
            ]
        );

        return redirect()->route('stock-in.index')
            ->with('success', 'Barang masuk berhasil dicatat!');
    }

    public function show(StockIn $stockIn)
    {
        return view('stock-in.show', compact('stockIn'));
    }

    public function destroy(StockIn $stockIn)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $product = $stockIn->product;
        
        if ($product->stock >= $stockIn->quantity) {
            $product->decrement('stock', $stockIn->quantity);
        } else {
            return redirect()->route('stock-in.index')
                ->with('error', 'Tidak dapat menghapus record. Stok produk tidak mencukupi.');
        }

        ActivityLog::log('stock_in_deleted', 'inventory',
            "Deleted stock in {$stockIn->code}: {$stockIn->quantity} units of {$product->name}",
            [
                'stock_in_code' => $stockIn->code,
                'product_name' => $product->name,
                'quantity' => $stockIn->quantity
            ],
            null
        );

        $stockIn->delete();

        return redirect()->route('stock-in.index')
            ->with('success', 'Record barang masuk berhasil dihapus!');
    }

    public function getProduct($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }
}
