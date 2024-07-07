@extends('layouts.main')
@section('page')
    <div class="px-3">
        <div class="row">
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="card-title my-auto">Warrant</h4>
                        @if (Auth::user()->hasRole('admin'))
                            <div class="p-2">
                                <a href="{{ route('warrant.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-1"></i>
                                    Add Warrant
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <input type="hidden" id="datatable_route" value="{{ $datatable_route }}">
                            <table id="datatable-warrant" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Duration</th>
                                        <th>Status</th>
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
        @include('js.warrant.script')
        <script>
            datatable();
        </script>
    @endpush
@endsection
