
<div class="wrapping-nav" id="wrappingNav">
    <nav class="navbar navbar-expand nav-aside">
        <div class="px-3">
            @if(get_option('app_logo'))
            <a class="navbar-brand text-center" href="{{route('admin.dashboard')}}">
                <img src="{{ asset('storage/' . get_option('app_logo')) }}" class="img-fluid" alt="Logo">
            </a>
            @endif
        </div>
    </nav>
    <nav class="navbar navbar-expand nav-content">
        <div class="container-fluid">
            <div class="d-flex">
                <a href="#" class="nav-link px-0 make-resize"><i class="fa fa-bars text-14"></i></a>

            </div>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <ul class="navbar-nav ms-auto mb-0 nav-menu align-items-center">
                <li class="nav-item">
                    <div class="nav-link">
                        <label class="mode-switch m-0 nav-icon-box">
                            <input type="checkbox" aria-label="input" id="darkMode">
                            <i class="fa fa-moon-o"></i>
                        </label>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" data-bs-toggle="dropdown" class="nav-link">
                        <span class="nav-user">
                            <span class="nav-user-text">{{auth()->user()->name}}</span>
                            <span class="nav-icon-box rounded-circle">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="rounded-circle" alt="Avatar">
                                @else
                                    <img src="{{ asset('admin/images/avatar/default-avatar.png') }}" class="rounded-circle" alt="Avatar">
                                @endif
                            </span>
                        </span>
                    </a>
                    <div class="dropdown-menu nav-dropdown-menu dropdown-menu-end right">
                        <div class="dropdown-content">
                            <div class="dropdown-content-header d-flex align-items-center bg-info p-2">
                                <div class="me-3">
                                    <div class="box-50 rounded-circle bg-light">
                                        @if(auth()->user()->avatar)
                                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="rounded-circle img-fit" alt="Avatar">
                                        @else
                                            <img src="{{ asset('admin/images/avatar/default-avatar.png') }}" class="rounded-circle img-fit" alt="Avatar">
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <div class="nav-user-name text-white">{{auth()->user()->name}}</div>
                                </div>
                            </div>
                            <div class="dropdown-content-body p-2">
                                <a href="{{ route('admin.profile.index') }}" class="d-flex align-items-center dropdown-item">
                                    <span class="me-3 text-info"><i class="icon-user"></i></span>
                                    <span class="text-14">Profile</span>
                                </a>
                                <a href="{{ route('admin.profile.change-password') }}" class="d-flex align-items-center dropdown-item">
                                    <span class="me-3 text-info"><i class="fa fa-lock"></i></span>
                                    <span class="text-14">Change Password</span>
                                </a>

                                <div class="dropdown-divider"></div>

                                <a href="{{url('/logout')}}" class="d-flex align-items-center dropdown-item">
                                    <span class="me-3 text-info"><i class="icon-logout"></i></span>
                                    <span class="text-14">Logout</span>
                                </a>
                            </div>
                            <div class="dropdown-content-footer">

                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</div>
