@extends('layouts.main')
@section('page')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card p-3">
                    <div class="card-header">
                        <h4 class="card-title my-auto">Add User</h4>
                    </div>
                    <form class="forms-sample" method="post" action="{{ route('user-management.store') }}">
                        <div class="card-body">
                            @csrf
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="employee_number">Employee Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="employee_number" name="employee_number"
                                    value="{{ old('employee_number') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="username">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="{{ old('username') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="roles">Role <span class="text-danger">*</span></label>
                                <select class="form-control" id="roles" name="roles" required>
                                    <option disabled hidden selected>Choose Role</option>
                                    @foreach ($roles as $role)
                                        @if (!is_null(old('roles')) && old('roles') == $role->name)
                                            <option value="{{ $role->name }}" selected>{{ $role->name }}</option>
                                        @else
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="division">Division <span class="text-danger">*</span></label>
                                <select class="form-control" id="division" name="division" required>
                                    <option disabled hidden selected>Choose Division</option>
                                    @foreach ($divisions as $division)
                                        @if (!is_null(old('division')) && old('division') == $division->id)
                                            <option value="{{ $division->id }}" selected>{{ $division->name }}</option>
                                        @else
                                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="password">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="re_password">Re Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="re_password" name="re_password" required>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="form-group text-end mt-2">
                                <a href="{{ route('user-management.index') }}" class="btn btn-sm btn-danger">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Back
                                </a>
                                <button type="submit" class="btn btn-sm btn-primary">Submit<i
                                        class="fas fa-check ms-1"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('js.user_management.script')
    @endpush
@endsection
