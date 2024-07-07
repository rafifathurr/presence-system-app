@extends('layouts.main')
@section('page')
    <div class="px-3">
        <div class="row">
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="card-title my-auto">Division</h4>
                        <div class="p-2">
                            <a href="{{ route('division.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i>
                                Add Division
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <input type="hidden" id="datatable_route" value="{{ $datatable_route }}">
                            <table id="datatable-division" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('js.division.script')
        <script>
            datatable();
        </script>
    @endpush
@endsection
