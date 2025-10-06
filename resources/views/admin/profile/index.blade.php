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

    <div class="row">
        <div class="col-12">
            <div class="wiz-card">
                <div class="wiz-card-body">

                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form" class="profile-form">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Avatar Section -->
                            <div class="col-md-4 mb-4">
                                <div class="text-center">
                                    <div class="avatar-upload">
                                        <div class="avatar-preview">
                                            <div id="imagePreview" style="background-image: url('{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('admin/images/avatar/default-avatar.png') }}');">
                                            </div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input type="file" id="imageUpload" name="avatar" accept=".png, .jpg, .jpeg" />
                                            <label for="imageUpload" class="btn btn-sm btn-outline-secondary">
                                                <i class="fa fa-camera"></i> Change Avatar
                                            </label>
                                        </div>
                                    </div>
                                    @error('avatar')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Profile Form -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                               id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror"
                                                  id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Update Profile
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
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
