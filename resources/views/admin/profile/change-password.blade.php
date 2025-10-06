@extends('layouts.admin')

@section('content')
<main class="main-content">
    <div class="breadcrumb-professional">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{route('admin.dashboard')}}">
                        <i class="icon-home me-2"></i>Dashboard
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    {{@$title}}
                </li>
            </ol>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="wiz-card">
                <div class="wiz-card-body">

                    <form action="{{ route('admin.profile.change-password.update') }}" method="POST" id="password-form">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                       id="current_password" name="current_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                    <i class="fa fa-eye" id="current_password_icon"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                       id="new_password" name="new_password" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                    <i class="fa fa-eye" id="new_password_icon"></i>
                                </button>
                            </div>
                            <small class="form-text text-muted">Password must be at least 8 characters long.</small>
                            <div class="password-strength" id="password-strength">
                                <div class="password-strength-bar" id="password-strength-bar"></div>
                            </div>
                            <div id="password-strength-text" class="small mt-1"></div>
                            @error('new_password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                       id="new_password_confirmation" name="new_password_confirmation" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                    <i class="fa fa-eye" id="new_password_confirmation_icon"></i>
                                </button>
                            </div>
                            @error('new_password_confirmation')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-lock"></i> Save Password
                            </button>
                            <a href="{{ route('admin.profile.index') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Requirements Card -->
            <div class="wiz-card mt-4">
                <div class="wiz-card-header">
                    <h6 class="mb-0">Password Requirements</h6>
                </div>
                <div class="wiz-card-body password-requirements">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fa fa-check-circle text-success me-2"></i>
                            At least 8 characters long
                        </li>
                        <li class="mb-2">
                            <i class="fa fa-info-circle text-info me-2"></i>
                            Use a mix of letters, numbers, and special characters for better security
                        </li>
                        <li class="mb-0">
                            <i class="fa fa-shield text-warning me-2"></i>
                            Avoid using common words or personal information
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection


@push('css')
    <link rel="stylesheet" href="{{ asset('admin/partial/css/profile.css') }}">
@endpush
@push('js')
    <script src="{{ asset('admin/partial/js/profile.js') }}"></script>
@endpush

