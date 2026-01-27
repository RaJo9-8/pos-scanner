@extends('layouts.app')

@section('title', 'User Details')
@section('breadcrumb', 'User Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">User Details: {{ $user->name }}</h3>
                <div class="card-tools">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td><strong>User Level:</strong></td>
                                <td><span class="badge badge-{{ $user->level == 1 ? 'danger' : ($user->level == 2 ? 'warning' : ($user->level == 3 ? 'info' : ($user->level == 4 ? 'success' : 'primary'))) }}">{{ $user->level_name }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td>{{ $user->phone ?: '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Address:</strong></td>
                                <td>{{ $user->address ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Created At:</strong></td>
                                <td>{{ $user->created_at->format('d M Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Last Updated:</strong></td>
                                <td>{{ $user->updated_at->format('d M Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email Verified:</strong></td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge badge-success">Verified</span>
                                    @else
                                        <span class="badge badge-warning">Not Verified</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Permissions</h4>
            </div>
            <div class="card-body">
                <h6>Access Level:</h6>
                <p><span class="badge badge-{{ $user->level == 1 ? 'danger' : ($user->level == 2 ? 'warning' : ($user->level == 3 ? 'info' : ($user->level == 4 ? 'success' : 'primary'))) }}">{{ $user->level_name }}</span></p>
                
                <h6 class="mt-3">Can Access:</h6>
                <ul class="list-unstyled">
                    <li><i class="fas fa-{{ $user->canAccessAllData() ? 'check text-success' : 'times text-danger' }}"></i> All Data Management</li>
                    <li><i class="fas fa-{{ $user->canManageTransactions() ? 'check text-success' : 'times text-danger' }}"></i> Transaction Management</li>
                    <li><i class="fas fa-{{ $user->canManageReturns() ? 'check text-success' : 'times text-danger' }}"></i> Return Management</li>
                    <li><i class="fas fa-{{ $user->canViewActivityLogs() ? 'check text-success' : 'times text-danger' }}"></i> Activity Logs</li>
                    <li><i class="fas fa-{{ $user->canViewReports() ? 'check text-success' : 'times text-danger' }}"></i> Reports</li>
                    <li><i class="fas fa-{{ $user->canRestoreDeletedData() ? 'check text-success' : 'times text-danger' }}"></i> Restore Deleted Data</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Quick Actions</h4>
            </div>
            <div class="card-body">
                @if(auth()->user()->isSuperAdmin() && auth()->id() !== $user->id)
                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm btn-block mb-2">
                    <i class="fas fa-edit"></i> Edit User
                </a>
                @endif
                
                <a href="{{ route('activity-logs.index') }}?user_id={{ $user->id }}" class="btn btn-info btn-sm btn-block">
                    <i class="fas fa-history"></i> View Activity Logs
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
