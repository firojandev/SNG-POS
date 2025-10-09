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

        <div class="theme-card">
            <div class="theme-card-header d-flex justify-content-between">
                <h6 class="theme-card-title">{{@$title}}</h6>
                <div>
                    <button type="button" class="btn btn-sm w-100 btn-brand-secondary" data-bs-toggle="modal" data-bs-target="#staffModal" onclick="openCreateModal()">
                        <i class="fa fa-plus"></i>Add Staff
                    </button>
                </div>
            </div>
            <div class="theme-card-body">
                <div class="table-responsive">
                    <table id="dataTable" class="table data-table">
                        <thead>
                        <tr>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Designation</th>
                            <th>Store</th>
                            <th class="width-20-percentage text-center">Options</th>
                        </tr>
                        </thead>
                        <tbody id="staffTableBody">
                            <!-- Data will be populated via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <!-- Staff Modal -->
        <div class="modal fade" id="staffModal" tabindex="-1" aria-labelledby="staffModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staffModalLabel">Add Staff</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="staffForm">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="staffName" class="form-label">Staff Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="staffName" name="name" placeholder="e.g., John Doe" required>
                                        <div class="invalid-feedback" id="nameError"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="e.g., john@example.com" required>
                                        <div class="invalid-feedback" id="emailError"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password <span class="text-danger" id="passwordRequired">*</span></label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                                        <div class="invalid-feedback" id="passwordError"></div>
                                        <div class="form-text" id="passwordHelp">Minimum 8 characters required</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="passwordConfirmation" class="form-label">Confirm Password <span class="text-danger" id="passwordConfirmationRequired">*</span></label>
                                        <input type="password" class="form-control" id="passwordConfirmation" name="password_confirmation" placeholder="Confirm password">
                                        <div class="invalid-feedback" id="password_confirmationError"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phoneNumber" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" id="phoneNumber" name="phone" placeholder="e.g., +1234567890">
                                        <div class="invalid-feedback" id="phoneError"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="storeSelect" class="form-label">Store <span class="text-danger">*</span></label>
                                        <select class="form-select select2-dropdown" id="storeSelect" name="store_id" required>
                                            <option value="">Select Store</option>
                                            @foreach($stores as $store)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback" id="store_idError"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="designation" class="form-label">Designation</label>
                                        <input type="text" class="form-control" id="designation" name="designation" placeholder="e.g., Cashier, Manager (optional)">
                                        <div class="invalid-feedback" id="designationError"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="3" placeholder="Enter staff address (optional)"></textarea>
                                        <div class="invalid-feedback" id="addressError"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="avatar" class="form-label">Profile Image</label>
                                <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                                <div class="invalid-feedback" id="avatarError"></div>
                                <div class="form-text">Upload a profile image (JPG, PNG, GIF - Max: 2MB)</div>
                                <div id="currentAvatar" class="mt-2" style="display: none;">
                                    <small class="text-muted">Current Image:</small><br>
                                    <img id="currentAvatarImg" src="" alt="Current Avatar" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                </div>
                                <div id="avatarPreview" class="mt-2" style="display: none;">
                                    <small class="text-muted">Preview:</small><br>
                                    <img id="avatarPreviewImg" src="" alt="Avatar Preview" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                </div>
                            </div>
                            <input type="hidden" id="staffId" name="staff_id">
                            <input type="hidden" id="formMethod" value="POST">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="saveBtn">
                                <span class="spinner-border spinner-border-sm d-none" id="saveSpinner" role="status" aria-hidden="true"></span>
                                Save Staff
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </main>
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/plugin/datatable/css/dataTables.bootstrap5.min.css')}}">
@endpush

@push('js')
    <script type="text/javascript" src="{{asset('admin/plugin/datatable/js/jquery.dataTables.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin/plugin/datatable/js/dataTables.bootstrap5.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('admin/partial/js/staff.js')}}"></script>
@endpush
