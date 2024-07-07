<!DOCTYPE html>
<html lang="en">
@include('layouts.head')

<body>
    <div class="wrapper">

        <div class="main-panel">

            <div class="container">
                <div class="page-inner">
                    @yield('page')
                </div>
            </div>

        </div>

    </div>
    @include('layouts.script')
</body>

</html>
