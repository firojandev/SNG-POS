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
                    <li class="breadcrumb-item">
                        <a href="{{route('admin.roles.index')}}">
                            Role & Permission
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        {{@$title}}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="theme-card">
            <div class="theme-card-header">
                <h6 class="theme-card-title">{{@$title}}</h6>
            </div>
            <div class="theme-card-body">
                <form id="roleForm">
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="roleName" class="form-label">Role Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="roleName" name="name" value="{{ $role->name }}" required placeholder="Enter role name" {{ $role->name === 'Admin' ? 'readonly' : '' }}>
                            @if($role->name === 'Admin')
                                <small class="text-muted">The Admin role name cannot be changed.</small>
                            @endif
                            <div class="invalid-feedback" id="nameError"></div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="mb-3">Assign Permissions</h5>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="selectAll" onclick="toggleAllPermissions()">
                                <label class="form-check-label fw-bold" for="selectAll">
                                    Select All Permissions
                                </label>
                            </div>
                            <hr>
                        </div>
                        @php
                            $rolePermissionIds = $role->permissions->pluck('id')->toArray();
                        @endphp
                        @foreach($groupedPermissions as $category => $permissions)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">{{ $category }}</h6>
                                        <div class="form-check">
                                            <input class="form-check-input category-select-all" type="checkbox"
                                                id="select_all_{{ Str::slug($category) }}"
                                                data-category="{{ Str::slug($category) }}">
                                            <label class="form-check-label small" for="select_all_{{ Str::slug($category) }}">
                                                Select All
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @foreach($permissions as $permission)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input permission-checkbox category-{{ Str::slug($category) }}" type="checkbox"
                                                    name="permissions[]"
                                                    value="{{ $permission->id }}"
                                                    id="permission_{{ $permission->id }}"
                                                    data-category="{{ Str::slug($category) }}"
                                                    {{ in_array($permission->id, $rolePermissionIds) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                    {{ $permission->display_name ?? ucwords(str_replace('_', ' ', $permission->name)) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span>
                                <i class="fa fa-save"></i> Update Role
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </main>
@endsection

@push('js')
    <script type="text/javascript" src="{{asset('admin/partial/js/roles.js')}}"></script>
@endpush
