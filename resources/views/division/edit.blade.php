@extends('layouts.main')
@section('page')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card p-3">
                    <div class="card-header">
                        <h4 class="card-title my-auto">Edit Division : {{ $division->name }}</h4>
                    </div>
                    <form class="forms-sample" method="post"
                        action="{{ route('division.update', ['id' => $division->id]) }}">
                        @csrf
                        @method('patch')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ $division->name }}" required>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="form-group text-end mt-2">
                                <a href="{{ route('division.index') }}" class="btn btn-sm btn-danger">
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
