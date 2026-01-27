@extends('layouts.app')

@section('title', 'Edit User')
@section('breadcrumb', 'Edit User')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit User: {{ $user->name }}</h3>
                <div class="card-tools">
                    <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-default btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="level">User Level <span class="text-danger">*</span></label>
                                <select class="form-control @error('level') is-invalid @enderror" id="level" name="level" required>
                                    <option value="">-- Select Level --</option>
                                    <option value="1" {{ $user->level == 1 ? 'selected' : '' }}>Super Admin</option>
                                    <option value="2" {{ $user->level == 2 ? 'selected' : '' }}>Admin</option>
                                    <option value="3" {{ $user->level == 3 ? 'selected' : '' }}>Leader</option>
                                    <option value="4" {{ $user->level == 4 ? 'selected' : '' }}>Kasir</option>
                                    <option value="5" {{ $user->level == 5 ? 'selected' : '' }}>Manager</option>
                                </select>
                                @error('level')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                @error('phone')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    @if(auth()->user()->isSuperAdmin())
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Security Notice</h6>
                        <p class="mb-0">You are editing a user with level <strong>{{ $user->level_name }}</strong>. Be careful when changing user levels as it affects system access permissions.</p>
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update User
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-default">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h4>Current User Info</h4>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Current Level:</strong></td>
                        <td><span class="badge badge-{{ $user->level == 1 ? 'danger' : ($user->level == 2 ? 'warning' : ($user->level == 3 ? 'info' : ($user->level == 4 ? 'success' : 'primary'))) }}">{{ $user->level_name }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Member Since:</strong></td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>User Level Information</h4>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>Super Admin</strong></td>
                        <td>Full system access</td>
                    </tr>
                    <tr>
                        <td><strong>Admin</strong></td>
                        <td>Manage users & products</td>
                    </tr>
                    <tr>
                        <td><strong>Leader</strong></td>
                        <td>Manage products & returns</td>
                    </tr>
                    <tr>
                        <td><strong>Kasir</strong></td>
                        <td>Sales transactions</td>
                    </tr>
                    <tr>
                        <td><strong>Manager</strong></td>
                        <td>View reports</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
