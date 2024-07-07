<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="{{ url('/') }}" class="logo">
                <img src="{{ asset('assets/img/brgm-icon.png') }}" class="navbar-brand" width="20%" alt="">
                <h6 class="text-white fw-bold my-auto ms-2">Presence System</h6>
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                <li class="nav-item  @if (Route::currentRouteName() == 'home') active @endif">
                    <a href="{{ route('home') }}" class="collapsed" aria-expanded="false">
                        <i class="fas fa-home"></i>
                        <p>Home</p>
                    </a>
                </li>
                @if (Illuminate\Support\Facades\Auth::user()->hasRole('admin'))
                    <li class="nav-item @if (Route::currentRouteName() == 'presence.index') active @endif">
                        <a href="{{ route('presence.index') }}">
                            <i class="fas fa-map-marker-alt"></i>
                            <p>Presence</p>
                        </a>
                    </li>
                    <li class="nav-item @if (Route::currentRouteName() == 'warrant.index') active @endif">
                        <a href="{{ route('warrant.index') }}">
                            <i class="fas fa-file-alt"></i>
                            <p>Warrant</p>
                        </a>
                    </li>
                    <li class="nav-item @if (Route::currentRouteName() == 'user-management.index' || Route::currentRouteName() == 'division.index') active @endif submenu">
                        <a data-bs-toggle="collapse" href="#master">
                            <i class="fas fa-file-alt"></i>
                            <p>Master Data</p>
                            <span class="caret"></span>
                        </a>
                        <div class="collapse @if (Route::currentRouteName() == 'user-management.index' ||
                                Route::currentRouteName() == 'division.index' ||
                                Route::currentRouteName() == 'location-work.index') show @endif" id="master">
                            <ul class="nav nav-collapse">
                                <li class="@if (Route::currentRouteName() == 'location-work.index') active @endif">
                                    <a href="{{ route('location-work.index') }}">
                                        <span class="sub-item">Location Work</span>
                                    </a>
                                </li>
                                <li class="@if (Route::currentRouteName() == 'division.index') active @endif">
                                    <a href="{{ route('division.index') }}">
                                        <span class="sub-item">Divisi</span>
                                    </a>
                                </li>
                                <li class="@if (Route::currentRouteName() == 'user-management.index') active @endif">
                                    <a href="{{ route('user-management.index') }}">
                                        <span class="sub-item">User Management</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @elseif (Illuminate\Support\Facades\Auth::user()->hasRole('staff'))
                    <li class="nav-item @if (Route::currentRouteName() == 'presence.index') active @endif">
                        <a href="{{ route('presence.index') }}">
                            <i class="fas fa-map-marker-alt"></i>
                            <p>Presence</p>
                        </a>
                    </li>
                    <li class="nav-item @if (Route::currentRouteName() == 'warrant.index') active @endif">
                        <a href="{{ route('warrant.index') }}">
                            <i class="fas fa-file-alt"></i>
                            <p>Warrant</p>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</div>
