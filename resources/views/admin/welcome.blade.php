@extends('layouts.admin')
@section('content')
    <main class="main-content">

        <!-- Welcome Header -->
        <div class="text-center mb-5 mt-5">
            <div class="mb-4">
                @if(auth()->user()->avatar)
                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}"
                         class="rounded-circle shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center shadow-sm"
                         style="width: 120px; height: 120px; font-size: 48px; color: white; font-weight: bold;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            <h2 class="mb-2">Welcome back, {{ auth()->user()->name }}! 👋</h2>
            <p class="text-muted mb-0">{{ now()->format('l, F d, Y') }}</p>
            <p class="text-muted">{{ now()->format('h:i A') }}</p>
        </div>

        <!-- Welcome Cards Grid -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="row">
                    <!-- Your Profile Card -->
                    <div class="col-md-6 mb-4">
                        <div class="theme-card text-center h-100">
                            <div class="theme-card-body p-4">
                                <div class="icon-box-lg bg-primary-light mb-3 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    <i class="fa fa-user text-primary" style="font-size: 32px;"></i>
                                </div>
                                <h5 class="mb-2">Your Profile</h5>
                                <p class="text-muted mb-3">View and manage your account information</p>
                                <div class="mt-3">
                                    <p class="mb-1"><strong>Email:</strong> {{ auth()->user()->email }}</p>
                                    <p class="mb-1"><strong>Phone:</strong> {{ auth()->user()->phone ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong>Role:</strong>
                                        @if(auth()->user()->roles->isNotEmpty())
                                            <span class="badge bg-primary">{{ auth()->user()->roles->first()->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">No Role</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Access Card -->
                    <div class="col-md-6 mb-4">
                        <div class="theme-card text-center h-100">
                            <div class="theme-card-body p-4">
                                <div class="icon-box-lg bg-success-light mb-3 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    <i class="fa fa-bolt text-success" style="font-size: 32px;"></i>
                                </div>
                                <h5 class="mb-2">Quick Access</h5>
                                <p class="text-muted mb-3">Access your available features</p>
                                <div class="d-grid gap-2 mt-3">
                                    @can('manage_sale')
                                        <a href="{{ route('invoice.index') }}" class="btn btn-primary btn-sm">
                                            <i class="fa fa-shopping-cart"></i> Manage Sales
                                        </a>
                                    @endcan
                                    @can('manage_product')
                                        <a href="{{ route('admin.products.index') }}" class="btn btn-success btn-sm">
                                            <i class="fa fa-cubes"></i> Manage Products
                                        </a>
                                    @endcan
                                    @can('manage_customer')
                                        <a href="{{ route('customers.index') }}" class="btn btn-info btn-sm">
                                            <i class="fa fa-users"></i> Manage Customers
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Store Information Card -->
                    <div class="col-md-6 mb-4">
                        <div class="theme-card text-center h-100">
                            <div class="theme-card-body p-4">
                                <div class="icon-box-lg bg-info-light mb-3 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    <i class="fa fa-building text-info" style="font-size: 32px;"></i>
                                </div>
                                <h5 class="mb-2">Your Store</h5>
                                <p class="text-muted mb-3">Current store information</p>
                                <div class="mt-3">
                                    @if(auth()->user()->store)
                                        <h4 class="text-primary mb-1">{{ auth()->user()->store->name }}</h4>
                                        <p class="text-muted mb-0">{{ auth()->user()->store->address ?? 'No address available' }}</p>
                                    @else
                                        <p class="text-muted">No store assigned</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Help & Support Card -->
                    <div class="col-md-6 mb-4">
                        <div class="theme-card text-center h-100">
                            <div class="theme-card-body p-4">
                                <div class="icon-box-lg bg-warning-light mb-3 mx-auto" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%;">
                                    <i class="fa fa-life-ring text-warning" style="font-size: 32px;"></i>
                                </div>
                                <h5 class="mb-2">Need Help?</h5>
                                <p class="text-muted mb-3">Contact support or view documentation</p>
                                @if(auth()->user()->store)
                                <div class="mt-3">
                                    <p class="mb-2"><i class="fa fa-phone text-success"></i> Support: <strong>{{auth()->user()->store->phone_number}}</strong></p>
                                    <p class="mb-0"><i class="fa fa-envelope text-primary"></i> Email: <strong>{{auth()->user()->store->email}}</strong></p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information Alert -->
                <div class="alert alert-info mt-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-info-circle me-3" style="font-size: 24px;"></i>
                        <div>
                            <h6 class="mb-1">Limited Access</h6>
                            <p class="mb-0">You have limited access to dashboard features. Please contact your administrator if you need access to additional features.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
@endsection

@push('css')
    <style>
        .icon-box-lg {
            transition: transform 0.3s ease;
        }

        .theme-card:hover .icon-box-lg {
            transform: scale(1.1);
        }

        .bg-primary-light {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-success-light {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-info-light {
            background-color: rgba(13, 202, 240, 0.1);
        }

        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1);
        }
    </style>
@endpush
