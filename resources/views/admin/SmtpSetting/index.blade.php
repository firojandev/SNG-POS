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
                        SMTP Settings
                    </li>
                </ol>
            </nav>
        </div>

        <div class="theme-card">
            <div class="theme-card-header">
                <h6 class="theme-card-title">SMTP Email Configuration</h6>
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

                <form action="{{ route('admin.smtp-setting.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_driver" class="form-label">Mail Driver <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="mail_driver" name="mail_driver"
                                       value="{{ old('mail_driver', $smtpSetting->mail_driver ?? 'smtp') }}" required readonly>
                                <small class="form-text text-muted">Default: smtp</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_host" class="form-label">Mail Host <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="mail_host" name="mail_host"
                                       value="{{ old('mail_host', $smtpSetting->mail_host ?? '') }}"
                                       placeholder="e.g., smtp.gmail.com" required>
                                <small class="form-text text-muted">SMTP server address</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_port" class="form-label">Mail Port <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="mail_port" name="mail_port"
                                       value="{{ old('mail_port', $smtpSetting->mail_port ?? 587) }}"
                                       placeholder="587" required>
                                <small class="form-text text-muted">Common ports: 587 (TLS), 465 (SSL), 25</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_encryption" class="form-label">Mail Encryption <span class="text-danger">*</span></label>
                                <select class="form-select" id="mail_encryption" name="mail_encryption" required>
                                    <option value="tls" {{ ($smtpSetting->mail_encryption ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($smtpSetting->mail_encryption ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="null" {{ ($smtpSetting->mail_encryption ?? '') === null ? 'selected' : '' }}>None</option>
                                </select>
                                <small class="form-text text-muted">Recommended: TLS</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_username" class="form-label">Mail Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="mail_username" name="mail_username"
                                       value="{{ old('mail_username', $smtpSetting->mail_username ?? '') }}"
                                       placeholder="your-email@example.com" required>
                                <small class="form-text text-muted">Your SMTP username (usually your email)</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_password" class="form-label">Mail Password <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="mail_password" name="mail_password"
                                       value="{{ old('mail_password', $smtpSetting->mail_password ?? '') }}"
                                       placeholder="Enter password" {{ $smtpSetting && $smtpSetting->mail_password ? '' : 'required' }}>
                                <small class="form-text text-muted">Your SMTP password or app password</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_from_address" class="form-label">From Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="mail_from_address" name="mail_from_address"
                                       value="{{ old('mail_from_address', $smtpSetting->mail_from_address ?? '') }}"
                                       placeholder="noreply@yourcompany.com" required>
                                <small class="form-text text-muted">Email address that appears in "From" field</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mail_from_name" class="form-label">From Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="mail_from_name" name="mail_from_name"
                                       value="{{ old('mail_from_name', $smtpSetting->mail_from_name ?? '') }}"
                                       placeholder="Your Company Name" required>
                                <small class="form-text text-muted">Name that appears in "From" field</small>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-sm btn-brand-secondary">
                                <i class="fa fa-save me-2"></i>Save Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Test Email Section -->
        <div class="theme-card mt-4">
            <div class="theme-card-header">
                <h6 class="theme-card-title">Test SMTP Connection</h6>
            </div>
            <div class="theme-card-body">
                <p class="text-muted">Send a test email to verify your SMTP configuration is working correctly.</p>

                <div id="test-email-response"></div>

                <form id="testEmailForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="test_email" class="form-label">Test Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="test_email" name="test_email"
                                       placeholder="test@example.com" required>
                                <small class="form-text text-muted">Enter email address to receive test email</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-sm btn-brand-secondary" id="sendTestEmailBtn">
                                <i class="fa fa-envelope me-2"></i>Send Test Email
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </main>

    @push('scripts')
    <script>
        "use strict";
        // SMTP Setting Configuration
        window.smtpSettingRoutes = {
            testConnection: '{{ route('admin.smtp-setting.test-connection') }}'
        };

        window.smtpSettingConfig = {
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    <script src="{{ asset('admin/partial/js/smtp-setting.js') }}"></script>
    @endpush
@endsection
