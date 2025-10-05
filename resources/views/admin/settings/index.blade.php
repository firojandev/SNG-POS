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
                        General Settings
                    </li>
                </ol>
            </nav>
        </div>

        <div class="theme-card">
            <div class="theme-card-header">
                <h6 class="theme-card-title">General Settings</h6>
            </div>
            <div class="theme-card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_name" class="form-label">Application Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="app_name" name="app_name"
                                       value="{{ old('app_name', get_option('app_name')) }}" required>
                                <small class="form-text text-muted">Current: {{get_option('app_name')}}</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="app_phone" name="app_phone"
                                       value="{{ old('app_phone', get_option('app_phone')) }}">
                            </div>
                        </div>
                    </div>




                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_logo" class="form-label">Application Logo</label>
                                <input type="file" class="form-control" id="app_logo" name="app_logo" accept="image/*">
                                <small class="form-text text-muted">Recommended size: 200x60px. Max size: 2MB</small>
                                @if(get_option('app_logo'))
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . get_option('app_logo')) }}" alt="Current Logo" style="max-height: 60px;">
                                    </div>
                                @endif
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_favicon" class="form-label">Favicon</label>
                                <input type="file" class="form-control" id="app_favicon" name="app_favicon" accept="image/*">
                                <small class="form-text text-muted">Recommended size: 32x32px. Max size: 1MB</small>
                                @if(get_option('app_favicon'))
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . get_option('app_favicon')) }}" alt="Current Favicon" style="max-height: 32px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="app_address" class="form-label">Address</label>
                                <textarea class="form-control" id="app_address" name="app_address" rows="3">{{ old('app_address', get_option('app_address')) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_format" class="form-label">Date Format <span class="text-danger">*</span></label>
                                <select class="form-select" id="date_format" name="date_format" required>
                                    <option value="Y-m-d" {{ get_option('date_format') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD ({{date('Y')}}-01-15)</option>
                                    <option value="d-m-Y" {{ get_option('date_format') == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY (15-01-{{date('Y')}})</option>
                                    <option value="m/d/Y" {{ get_option('date_format') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (01/15/{{date('Y')}})</option>
                                    <option value="d/m/Y" {{ get_option('date_format') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (15/01/{{date('Y')}})</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-brand-primary">
                                <i class="fa fa-save me-2"></i>Update Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </main>
@endsection
