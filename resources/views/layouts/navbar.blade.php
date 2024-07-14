<nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
    <div class="container-fluid">

        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
            <li class="nav-item topbar-user dropdown hidden-caret">
                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#" aria-expanded="false">
                    <div class="avatar-sm">
                        <img src="{{ asset('assets/img/user.png') }}" alt="..."
                            class="avatar-img rounded-circle" />
                    </div>
                    <span class="profile-username">
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-user animated fadeIn p-2 rounded-2">
                    <div class="dropdown-user-scroll scrollbar-outer">
                        <li>
                            <div class="user-box">
                                <div class="avatar-lg">
                                    <img src="{{ asset('assets/img/user.png') }}" alt="image profile"
                                        class="avatar-img rounded" />
                                </div>
                                <div class="u-text my-auto">
                                    <h4>{{ Auth::user()->name }}</h4>
                                    <p class="text-muted">{{ Auth::user()->employee_number }}</p>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item py-2"
                                href="{{ route('user-management.show', ['id' => Auth::user()->id]) }}"><i
                                    class="fas fa-user me-2"></i>My Profile</a>
                            <a class="dropdown-item py-2" href="{{ route('logout') }}"><i
                                    class="fas fa-sign-out-alt me-2"></i>Logout</a>
                        </li>
                    </div>
                </ul>
            </li>
        </ul>
    </div>
</nav>
