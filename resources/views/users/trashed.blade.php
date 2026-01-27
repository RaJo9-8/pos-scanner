@extends('layouts.app')

@section('title', 'Trashed Users')
@section('breadcrumb', 'Trashed Users')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Trashed Users</h3>
                <div class="card-tools">
                    <a href="{{ route('users.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Level</th>
                                <th>Phone</th>
                                <th>Deleted At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->level == 1)
                                        <span class="badge badge-danger">{{ $user->level_name }}</span>
                                    @elseif($user->level == 2)
                                        <span class="badge badge-warning">{{ $user->level_name }}</span>
                                    @elseif($user->level == 3)
                                        <span class="badge badge-info">{{ $user->level_name }}</span>
                                    @elseif($user->level == 4)
                                        <span class="badge badge-success">{{ $user->level_name }}</span>
                                    @else
                                        <span class="badge badge-primary">{{ $user->level_name }}</span>
                                    @endif
                                </td>
                                <td>{{ $user->phone ?: '-' }}</td>
                                <td>{{ $user->deleted_at->format('d M Y H:i') }}</td>
                                <td>
                                    @if(auth()->user()->isSuperAdmin())
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to restore this user?')">
                                            <i class="fas fa-undo"></i> Restore
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No trashed users found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
