<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::orderBy('created_at', 'desc')->get();
            
            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('level_name', function ($row) {
                    return $row->level_name;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="' . route('users.show', $row) . '" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>';
                    $btn .= ' <a href="' . route('users.edit', $row) . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>';
                    if (auth()->user()->level < $row->level || auth()->user()->isSuperAdmin()) {
                        $btn .= ' <button class="btn btn-danger btn-sm delete-btn" data-id="' . $row->id . '"><i class="fas fa-trash"></i></button>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('users.index');
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'level' => 'required|integer|in:1,2,3,4,5',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'level' => $request->level,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        ActivityLog::log('create', 'users', 
            "Created user: {$user->name} ({$user->level_name})",
            null,
            [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'level' => $user->level,
                'level_name' => $user->level_name
            ]
        );

        return redirect()->route('users.index')
            ->with('success', 'User created successfully!');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if (!auth()->user()->isSuperAdmin() && auth()->user()->level >= $user->level) {
            abort(403, 'Unauthorized action.');
        }

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->isSuperAdmin() && auth()->user()->level >= $user->level) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'level' => 'required|integer|in:1,2,3,4,5',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'level' => $user->level,
            'level_name' => $user->level_name,
            'phone' => $user->phone,
            'address' => $user->address,
        ];

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'level' => $request->level,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        ActivityLog::log('update', 'users', 
            "Updated user: {$user->name} ({$user->level_name})",
            $oldValues,
            [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'level' => $user->level,
                'level_name' => $user->level_name,
                'phone' => $user->phone,
                'address' => $user->address,
            ]
        );

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if (!auth()->user()->isSuperAdmin() && auth()->user()->level >= $user->level) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Cannot delete your own account!');
        }

        $oldValues = [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'level' => $user->level,
            'level_name' => $user->level_name,
        ];

        ActivityLog::log('delete', 'users', 
            "Deleted user: {$user->name} ({$user->level_name})",
            $oldValues,
            null
        );

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully!');
    }

    public function trashed()
    {
        // Debug: Log untuk melihat apakah method ini dipanggil
        \Log::info('UserController::trashed() called');
        
        $users = User::onlyTrashed()->get();
        
        // Debug: Log jumlah users
        \Log::info('Trashed users count: ' . $users->count());
        
        // Debug: Log user data
        foreach($users as $user) {
            \Log::info('User: ' . $user->name . ' (ID: ' . $user->id . ')');
        }
        
        return view('users.trashed', compact('users'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        
        if (!auth()->user()->isSuperAdmin() && auth()->user()->level >= $user->level) {
            abort(403, 'Unauthorized action.');
        }

        $user->restore();

        ActivityLog::log('restore', 'users', 
            "Restored user: {$user->name} ({$user->level_name})");

        return redirect()->route('users.trashed')->with('success', 'User restored successfully!');
    }
}
