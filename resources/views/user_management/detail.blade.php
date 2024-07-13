@extends('layouts.main')
@section('page')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card p-3">
                    <div class="card-header">
                        <h4 class="card-title my-auto">Detail User {{ $user->name }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $user->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Employee Number</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $user->employee_number }}
                            </div>
                        </div>
                        @if ($verification_status_show)
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Employee Number</label>
                                <div class="col-sm-9 col-form-label">
                                    {!! $verification_status !!}
                                </div>
                            </div>
                        @endif
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Username</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $user->username }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $user->email }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Role</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $user_role }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Division</label>
                            <div class="col-sm-9 col-form-label">
                                {{ !is_null($user->division_id) ? $user->division->name : '-' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated At</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d F Y H:i:s', strtotime($user->updated_at)) }}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="form-group text-end mt-2">
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-danger">
                                <i class="fas fa-arrow-left me-1"></i>
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
