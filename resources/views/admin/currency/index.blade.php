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

        <!-- Current Currency Selection -->
        <div class="theme-card mb-4">
            <div class="theme-card-header">
                <h6 class="theme-card-title">Current Currency</h6>
            </div>
            <div class="theme-card-body">
                <form action="{{ route('admin.currency.set') }}" method="POST" class="d-flex align-items-end gap-3">
                    @csrf
                    <div class="flex-grow-1">
                        <label for="currency_symbol" class="form-label">Select Currency</label>
                        <select class="form-select" id="currency_symbol" name="currency_symbol" required>
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->symbol }}" {{ $currentCurrency == $currency->symbol ? 'selected' : '' }}>
                                    {{ $currency->symbol }} ({{ $currency->name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-brand-primary">
                            <i class="fa fa-check me-2"></i>Set Currency
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Currency List -->
        <div class="theme-card">
            <div class="theme-card-header d-flex justify-content-between">
                <h6 class="theme-card-title">Currency List</h6>
                <div>
                    <button type="button" class="btn btn-sm w-100 btn-brand-secondary" data-bs-toggle="modal" data-bs-target="#currencyModal">
                        <i class="fa fa-plus"></i>Add Currency
                    </button>
                </div>
            </div>
            <div class="theme-card-body">
                <div class="table-responsive">
                    <table class="table data-table">
                        <thead>
                        <tr>
                            <th>Short Name</th>
                            <th>Currency Symbol</th>
                            <th>Status</th>
                            <th class="width-20-percentage text-center">Options</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($currencies as $currency)
                            <tr>
                                <td>{{ $currency->name }}</td>
                                <td>{{ $currency->symbol }}</td>
                                <td>
                                    @if($currentCurrency == $currency->symbol)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($currentCurrency != $currency->symbol)
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-currency" 
                                                data-id="{{ $currency->id }}" 
                                                data-name="{{ $currency->name }}"
                                                data-symbol="{{ $currency->symbol }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">Current Currency</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No currencies found</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Currency Modal -->
        <div class="modal fade" id="currencyModal" tabindex="-1" aria-labelledby="currencyModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="currencyModalLabel">Add New Currency</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.currency.store') }}" method="POST" id="currencyForm">
                        @csrf
                        <div class="modal-body">
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="name" class="form-label">Short Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                       placeholder="e.g., USD, EUR, BDT" maxlength="10" required>
                                <small class="form-text text-muted">Currency code (e.g., USD, EUR, BDT)</small>
                            </div>

                            <div class="mb-3">
                                <label for="symbol" class="form-label">Currency Symbol <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="symbol" name="symbol"
                                       placeholder="e.g., $, €, ৳" maxlength="5" required>
                                <small class="form-text text-muted">Symbol to display (e.g., $, €, ৳)</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-brand-primary">
                                <i class="fa fa-save me-2"></i>Add Currency
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>

@endsection

@push('js')
    <script type="text/javascript" src="{{asset('admin/partial/js/currency.js')}}"></script>
@endpush
