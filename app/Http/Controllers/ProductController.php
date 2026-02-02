<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ActivityLog;
use App\Models\StockIn;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Picqer\Barcode\BarcodeGenerator;
use Picqer\Barcode\BarcodeGeneratorHTML;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Product::query();
            
            if ($request->get('low_stock')) {
                $query->where('stock', '<=', 'min_stock');
            }
            
            return DataTables::of($query)
                ->addColumn('image', function ($product) {
                    if ($product->image) {
                        return '<img src="' . asset('storage/' . $product->image) . '" width="50" height="50" class="product-image" style="cursor: pointer;" onclick="showImageModal(\'' . asset('storage/' . $product->image) . '\', \'' . $product->name . '\')">';
                    }
                    return '<img src="' . asset('dist/img/default-150x150.png') . '" width="50" height="50" class="product-image" style="cursor: pointer;" onclick="showImageModal(\'' . asset('dist/img/default-150x150.png') . '\', \'Default Image\')">';
                })
                ->addColumn('barcode', function ($product) {
                    $generator = new BarcodeGeneratorHTML();
                    $barcode = $generator->getBarcode($product->barcode, $generator::TYPE_CODE_128);
                    return '<div style="text-align: center;">
                        <div>' . $barcode . '</div>
                        <small style="font-size: 10px;">' . $product->barcode . '</small>
                    </div>';
                })
                ->addColumn('action', function ($product) {
                    $buttons = '';
                    
                    if (auth()->user()->canManageProducts()) {
                        $buttons .= '<a href="' . route('products.edit', $product) . '" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>';
                        $buttons .= '<button type="button" class="btn btn-sm btn-danger delete-product" data-id="' . $product->id . '"><i class="fas fa-trash"></i></button>';
                    }
                    
                    $buttons .= '<a href="' . route('products.show', $product) . '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>';
                    
                    return $buttons;
                })
                ->addColumn('stock_status', function ($product) {
                    if ($product->isLowStock()) {
                        return '<span class="badge badge-danger">Low Stock</span>';
                    }
                    return '<span class="badge badge-success">In Stock</span>';
                })
                ->addColumn('formatted_purchase_price', function ($product) {
                    return $product->formatted_purchase_price;
                })
                ->addColumn('formatted_selling_price', function ($product) {
                    return $product->formatted_selling_price;
                })
                ->addColumn('profit', function ($product) {
                    return $product->formatted_profit;
                })
                ->rawColumns(['action', 'stock_status', 'image', 'barcode', 'profit'])
                ->make(true);
        }

        return view('products.index');
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode',
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('products', $imageName, 'public');
            $data['image'] = 'products/' . $imageName;
        }

        $product = Product::create($data);

        // Jika stok awal > 0, buat data stock in otomatis
        if ($product->stock > 0) {
            StockIn::create([
                'code' => StockIn::generateCode(),
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'quantity' => $product->stock,
                'purchase_price' => $product->purchase_price,
                'total_price' => $product->stock * $product->purchase_price,
                'supplier' => 'Initial Stock',
                'notes' => 'Otomatis dibuat saat tambah produk baru',
                'date' => now(),
            ]);
        }

        ActivityLog::log('create', 'product', "Created product: {$product->name}", null, $data);

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'required|string|unique:products,barcode,' . $product->id,
            'description' => 'nullable|string',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'category' => 'nullable|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $oldData = $product->toArray();
        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->storeAs('products', $imageName, 'public');
            $data['image'] = 'products/' . $imageName;
        }

        $product->update($data);

        ActivityLog::log('update', 'product', "Updated product: {$product->name}", $oldData, $data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $productData = $product->toArray();
        $product->delete();

        ActivityLog::log('delete', 'product', "Deleted product: {$product->name}", $productData, null);

        return response()->json(['success' => 'Product deleted successfully']);
    }

    public function trashed()
    {
        // Debug: Log untuk melihat apakah method ini dipanggil
        \Log::info('Trashed method called');
        
        $products = Product::onlyTrashed()->get();
        
        // Debug: Log jumlah produk yang dihapus
        \Log::info('Trashed products count: ' . $products->count());
        
        return view('products.trashed', compact('products'));
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        $product->restore();

        ActivityLog::log('restore', 'product', "Restored product: {$product->name}");

        return redirect()->route('products.trashed')->with('success', 'Product restored successfully');
    }

    public function search(Request $request)
    {
        $search = $request->get('q');
        $products = Product::where('name', 'like', "%{$search}%")
            ->orWhere('barcode', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'barcode', 'selling_price', 'stock']);

        return response()->json($products);
    }

    public function findByBarcode($barcode)
    {
        $product = Product::where('barcode', $barcode)->first();
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }
}
