<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Yajra\DataTables\DataTables;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $activityLogs = ActivityLog::with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return DataTables::of($activityLogs)
                ->addIndexColumn()
                ->addColumn('user_name', function ($row) {
                    return $row->user ? $row->user->name : 'System';
                })
                ->addColumn('action_formatted', function ($row) {
                    $actions = [
                        'create' => '<span class="badge badge-primary">Create</span>',
                        'update' => '<span class="badge badge-info">Update</span>',
                        'delete' => '<span class="badge badge-danger">Delete</span>',
                        'login' => '<span class="badge badge-success">Login</span>',
                        'login_failed' => '<span class="badge badge-warning">Login Failed</span>',
                        'logout' => '<span class="badge badge-secondary">Logout</span>',
                        'stock_in' => '<span class="badge badge-success">Stock In</span>',
                        'stock_out' => '<span class="badge badge-danger">Stock Out</span>',
                        'stock_in_deleted' => '<span class="badge badge-warning">Stock In Deleted</span>',
                        'stock_out_deleted' => '<span class="badge badge-warning">Stock Out Deleted</span>',
                    ];
                    
                    return $actions[$row->action] ?? '<span class="badge badge-secondary">' . $row->action . '</span>';
                })
                ->addColumn('module_formatted', function ($row) {
                    $modules = [
                        'auth' => '<span class="badge badge-dark">Authentication</span>',
                        'products' => '<span class="badge badge-primary">Products</span>',
                        'transactions' => '<span class="badge badge-success">Transactions</span>',
                        'returns' => '<span class="badge badge-info">Returns</span>',
                        'inventory' => '<span class="badge badge-warning">Inventory</span>',
                    ];
                    
                    return $modules[$row->module] ?? '<span class="badge badge-secondary">' . $row->module . '</span>';
                })
                ->addColumn('date', function ($row) {
                    return $row->created_at->format('d M Y H:i:s');
                })
                ->addColumn('description', function ($row) {
                    return $row->description ? substr($row->description, 0, 50) . '...' : '-';
                })
                ->rawColumns(['action_formatted', 'module_formatted'])
                ->make(true);
        }

        return view('activity-logs.index');
    }
}
