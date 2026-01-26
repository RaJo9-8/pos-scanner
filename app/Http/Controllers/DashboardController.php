<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\ReturnTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        $totalProducts = Product::count();
        $lowStockProducts = Product::where('stock', '<=', 'min_stock')->count();
        $totalTransactions = Transaction::count();
        $totalUsers = User::count();

        $todaySales = Transaction::whereDate('created_at', $today)->sum('total_amount');
        $thisMonthSales = Transaction::where('created_at', '>=', $thisMonth)->sum('total_amount');
        $lastMonthSales = Transaction::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->sum('total_amount');

        $todayProfit = TransactionItem::whereHas('transaction', function($query) use ($today) {
            $query->whereDate('created_at', $today);
        })->get()->sum(function($item) {
            return ($item->product->selling_price - $item->product->purchase_price) * $item->quantity;
        });

        $thisMonthProfit = TransactionItem::whereHas('transaction', function($query) use ($thisMonth) {
            $query->where('created_at', '>=', $thisMonth);
        })->get()->sum(function($item) {
            return ($item->product->selling_price - $item->product->purchase_price) * $item->quantity;
        });

        $recentTransactions = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $topProducts = Product::select(
                'products.id',
                'products.name',
                'products.barcode',
                'products.selling_price',
                'products.stock',
                'products.unit',
                DB::raw('SUM(transaction_items.quantity) as total_sold')
            )
            ->join('transaction_items', 'products.id', '=', 'transaction_items.product_id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.created_at', '>=', $thisMonth)
            ->whereNull('products.deleted_at')
            ->groupBy('products.id', 'products.name', 'products.barcode', 'products.selling_price', 'products.stock', 'products.unit')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        $salesChart = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $categoryChart = Product::select('category', DB::raw('COUNT(*) as count'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();

        $recentActivities = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $pendingReturns = ReturnTransaction::where('status', 'pending')->count();

        return view('dashboard', compact(
            'user',
            'totalProducts',
            'lowStockProducts',
            'totalTransactions',
            'totalUsers',
            'todaySales',
            'thisMonthSales',
            'lastMonthSales',
            'todayProfit',
            'thisMonthProfit',
            'recentTransactions',
            'topProducts',
            'salesChart',
            'categoryChart',
            'recentActivities',
            'pendingReturns'
        ));
    }

    public function getSalesData(Request $request)
    {
        $period = $request->get('period', '7');
        
        $startDate = match($period) {
            '7' => Carbon::now()->subDays(7),
            '30' => Carbon::now()->subDays(30),
            '90' => Carbon::now()->subDays(90),
            '365' => Carbon::now()->subDays(365),
            default => Carbon::now()->subDays(7)
        };

        $salesData = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($salesData);
    }

    public function getProfitData(Request $request)
    {
        $period = $request->get('period', '7');
        
        $startDate = match($period) {
            '7' => Carbon::now()->subDays(7),
            '30' => Carbon::now()->subDays(30),
            '90' => Carbon::now()->subDays(90),
            '365' => Carbon::now()->subDays(365),
            default => Carbon::now()->subDays(7)
        };

        $profitData = Transaction::select(
                DB::raw('DATE(transactions.created_at) as date'),
                DB::raw('SUM(transaction_items.quantity * (products.selling_price - products.purchase_price)) as profit')
            )
            ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->where('transactions.created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($profitData);
    }
}
