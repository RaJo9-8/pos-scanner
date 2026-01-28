<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockOut;
use App\Models\Product;
use App\Models\ActivityLog;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $stockOut = StockOut::with(['product', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($stockOut)
                ->addIndexColumn()
                ->addColumn('product_name', function ($row) {
                    return $row->product->name;
                })
                ->addColumn('user_name', function ($row) {
                    return $row->user->name;
                })
                ->addColumn('date', function ($row) {
                    return $row->date->format('d M Y');
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('stock-out.show', $row) . '" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>';
                    if (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin()) {
                        $btn .= ' <button class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('stock-out.index');
    }

    public function create()
    {
        $products = Product::where('stock', '>', 0)->get();
        return view('stock-out.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return redirect()->back()
                ->with('error', 'Stok tidak mencukupi! Stok tersedia: ' . $product->stock)
                ->withInput();
        }

        $stockOut = StockOut::create([
            'code' => StockOut::generateCode(),
            'product_id' => $request->product_id,
            'user_id' => auth()->id(),
            'quantity' => $request->quantity,
            'reason' => $request->reason,
            'notes' => $request->notes,
            'date' => $request->date,
        ]);

        $product->decrement('stock', $request->quantity);

        ActivityLog::log('stock_out', 'inventory', 
            "Stock out {$stockOut->code}: {$request->quantity} units of {$product->name} ({$request->reason})",
            null,
            [
                'stock_out_code' => $stockOut->code,
                'product_name' => $product->name,
                'quantity' => $request->quantity,
                'reason' => $request->reason
            ]
        );

        return redirect()->route('stock-out.index')
            ->with('success', 'Barang keluar berhasil dicatat!');
    }

    public function show(StockOut $stockOut)
    {
        return view('stock-out.show', compact('stockOut'));
    }

    public function destroy(StockOut $stockOut)
    {
        if (!auth()->user()->isAdmin() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $product = $stockOut->product;
        $product->increment('stock', $stockOut->quantity);

        ActivityLog::log('stock_out_deleted', 'inventory',
            "Deleted stock out {$stockOut->code}: {$stockOut->quantity} units of {$product->name}",
            [
                'stock_out_code' => $stockOut->code,
                'product_name' => $product->name,
                'quantity' => $stockOut->quantity
            ],
            null
        );

        $stockOut->delete();

        return redirect()->route('stock-out.index')
            ->with('success', 'Record barang keluar berhasil dihapus!');
    }

    public function getProduct($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }
}
