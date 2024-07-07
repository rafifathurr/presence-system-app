<!DOCTYPE html>
<html lang="en">
@include('layouts.head')

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        @include('layouts.sidebar')
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    @include('layouts.header')
                    <!-- End Logo Header -->
                </div>
                <!-- Navbar Header -->
                @include('layouts.navbar')
                <!-- End Navbar -->
            </div>

            <div class="container">
                <div class="page-inner">
                    @yield('page')
                </div>
            </div>

            @include('layouts.footer')
        </div>

    </div>
    @include('layouts.script')
</body>

</html>
