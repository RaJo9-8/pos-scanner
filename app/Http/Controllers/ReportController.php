<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\ReturnTransaction;
use App\Models\User;
use Carbon\Carbon;
use PDF;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfDay());
        
        $query = Transaction::with(['user', 'transactionItems.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed');

        $transactions = $query->orderBy('created_at', 'desc')->get();
        
        $totalSales = $transactions->sum('total_amount');
        $totalTransactions = $transactions->count();
        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;
        
        $salesByCashier = $transactions->groupBy('user_id')->map(function ($group) {
            return [
                'user' => $group->first()->user,
                'total_sales' => $group->sum('total_amount'),
                'transaction_count' => $group->count(),
                'average_transaction' => $group->sum('total_amount') / $group->count()
            ];
        });

        $salesByPayment = $transactions->groupBy('payment_method')->map(function ($group) {
            return [
                'payment_method' => ucfirst($group->first()->payment_method),
                'total_sales' => $group->sum('total_amount'),
                'transaction_count' => $group->count(),
                'percentage' => ($group->sum('total_amount') / $transactions->sum('total_amount')) * 100
            ];
        });

        $dailySales = $transactions->groupBy(function ($transaction) {
            return $transaction->created_at->format('Y-m-d');
        })->map(function ($group) {
            return [
                'date' => $group->first()->created_at->format('Y-m-d'),
                'total_sales' => $group->sum('total_amount'),
                'transaction_count' => $group->count()
            ];
        });

        return view('reports.sales', compact(
            'transactions', 
            'totalSales', 
            'totalTransactions', 
            'averageTransaction',
            'salesByCashier',
            'salesByPayment',
            'dailySales',
            'startDate',
            'endDate'
        ));
    }

    public function financial(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfDay());
        
        $transactions = Transaction::with('transactionItems.product')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $totalRevenue = $transactions->sum('total_amount');
        $totalDiscount = $transactions->sum('discount');
        $totalTax = $transactions->sum('tax');
        $netRevenue = $totalRevenue - $totalDiscount + $totalTax;

        $totalCost = 0;
        $totalProfit = 0;

        foreach ($transactions as $transaction) {
            foreach ($transaction->transactionItems as $item) {
                $product = $item->product;
                $itemCost = $item->quantity * $product->purchase_price;
                $itemRevenue = $item->subtotal;
                $itemProfit = $itemRevenue - $itemCost;
                
                $totalCost += $itemCost;
                $totalProfit += $itemProfit;
            }
        }

        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        $profitByProduct = [];
        foreach ($transactions as $transaction) {
            foreach ($transaction->transactionItems as $item) {
                $productId = $item->product_id;
                $productName = $item->product_name;
                $itemCost = $item->quantity * $item->product->purchase_price;
                $itemRevenue = $item->subtotal;
                $itemProfit = $itemRevenue - $itemCost;
                $itemQuantity = $item->quantity;

                if (!isset($profitByProduct[$productId])) {
                    $profitByProduct[$productId] = [
                        'product_name' => $productName,
                        'total_cost' => 0,
                        'total_revenue' => 0,
                        'total_profit' => 0,
                        'total_quantity' => 0
                    ];
                }

                $profitByProduct[$productId]['total_cost'] += $itemCost;
                $profitByProduct[$productId]['total_revenue'] += $itemRevenue;
                $profitByProduct[$productId]['total_profit'] += $itemProfit;
                $profitByProduct[$productId]['total_quantity'] += $itemQuantity;
            }
        }

        usort($profitByProduct, function ($a, $b) {
            return $b['total_profit'] <=> $a['total_profit'];
        });

        $dailyFinancial = $transactions->groupBy(function ($transaction) {
            return $transaction->created_at->format('Y-m-d');
        })->map(function ($group) {
            $dayRevenue = $group->sum('total_amount');
            $dayCost = 0;
            $dayProfit = 0;

            foreach ($group as $transaction) {
                foreach ($transaction->transactionItems as $item) {
                    $itemCost = $item->quantity * $item->product->purchase_price;
                    $itemRevenue = $item->subtotal;
                    $dayCost += $itemCost;
                    $dayProfit += $itemRevenue - $itemCost;
                }
            }

            return [
                'date' => $group->first()->created_at->format('Y-m-d'),
                'revenue' => $dayRevenue,
                'cost' => $dayCost,
                'profit' => $dayProfit
            ];
        });

        return view('reports.financial', compact(
            'totalRevenue',
            'totalCost',
            'totalProfit',
            'totalDiscount',
            'totalTax',
            'netRevenue',
            'profitMargin',
            'profitByProduct',
            'dailyFinancial',
            'startDate',
            'endDate'
        ));
    }

    public function inventory(Request $request)
    {
        $products = Product::with(['transactionItems' => function ($query) {
            $query->whereHas('transaction', function ($q) {
                $q->where('status', 'completed');
            });
        }])->get();

        $lowStockProducts = $products->filter(function ($product) {
            return $product->isLowStock();
        });

        $outOfStockProducts = $products->filter(function ($product) {
            return $product->stock == 0;
        });

        $inventoryValue = 0;
        $inventoryCost = 0;

        foreach ($products as $product) {
            $inventoryValue += $product->stock * $product->selling_price;
            $inventoryCost += $product->stock * $product->purchase_price;
        }

        $inventoryProfit = $inventoryValue - $inventoryCost;

        $categorySummary = $products->groupBy('category')->map(function ($group) {
            return [
                'category' => $group->first()->category ?: 'Uncategorized',
                'total_products' => $group->count(),
                'total_stock' => $group->sum('stock'),
                'total_value' => $group->sum(function ($product) {
                    return $product->stock * $product->selling_price;
                })
            ];
        });

        $topSellingProducts = TransactionItem::with('product')
            ->whereHas('transaction', function ($query) {
                $query->where('status', 'completed');
            })
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        return view('reports.inventory', compact(
            'products',
            'lowStockProducts',
            'outOfStockProducts',
            'inventoryValue',
            'inventoryCost',
            'inventoryProfit',
            'categorySummary',
            'topSellingProducts'
        ));
    }

    public function exportSales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfDay());
        
        $transactions = Transaction::with(['user', 'transactionItems.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalSales = $transactions->sum('total_amount');
        $totalTransactions = $transactions->count();

        $pdf = PDF::loadView('reports.exports.sales', compact(
            'transactions',
            'totalSales',
            'totalTransactions',
            'startDate',
            'endDate'
        ));

        return $pdf->download('sales_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf');
    }

    public function exportFinancial(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfDay());
        
        $transactions = Transaction::with('transactionItems.product')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $totalRevenue = $transactions->sum('total_amount');
        $totalCost = 0;
        $totalProfit = 0;

        foreach ($transactions as $transaction) {
            foreach ($transaction->transactionItems as $item) {
                $product = $item->product;
                $itemCost = $item->quantity * $product->purchase_price;
                $itemRevenue = $item->subtotal;
                $itemProfit = $itemRevenue - $itemCost;
                
                $totalCost += $itemCost;
                $totalProfit += $itemProfit;
            }
        }

        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        $pdf = PDF::loadView('reports.exports.financial', compact(
            'transactions',
            'totalRevenue',
            'totalCost',
            'totalProfit',
            'profitMargin',
            'startDate',
            'endDate'
        ));

        return $pdf->download('financial_report_' . $startDate->format('Y-m-d') . '_to_' . $endDate->format('Y-m-d') . '.pdf');
    }
}
