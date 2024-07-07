@extends('layouts.auth')
@section('page')
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0">
                <div class="row w-100 mx-0">
                    <div class="col-lg-5 col-md-5 offset-lg-2 offset-md-3 border border-2 rounded-3">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <div class="brand-logo text-center">
                                <img src="{{ asset('assets/img/brgm-icon.png') }}" width="30%" alt="">
                                <h3 class="fw-bold mt-1">Presence System</h3>
                            </div>
                            <form class="pt-3" method="post" action="{{ route('authenticate') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="username_or_employee_number">Username or Employee Number</label>
                                    <input type="text"
                                        class="form-control form-control-lg @error('username_or_employee_number') is-invalid @enderror"
                                        name="username_or_employee_number" value="{{ old('username_or_employee_number') }}"
                                        required>
                                    @error('username_or_employee_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="username_or_employee_number">Password</label>
                                    <input type="password" class="form-control form-control-lg" id="password"
                                        name="password" value="{{ old('password') }}" required>
                                </div>
                                <label class="form-check-label my-3">
                                    <input type="checkbox" class="form-check-input" name="remember">
                                    Remember me
                                    <i class="input-helper"></i>
                                </label>
                                <div class="form-group text-end">
                                    <button type="submit"
                                        class="btn btn-block btn-primary btn-lg fw-bold auth-form-btn font-weight-bold">
                                        LOGIN
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content-wrapper ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
@endsection
