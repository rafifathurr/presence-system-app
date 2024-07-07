@extends('layouts.main')
@section('page')
    <div class="px-3">
        <div class="row">
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="card-title my-auto">Location Work</h4>
                        <div class="p-2">
                            <a href="{{ route('location-work.create') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i>
                                Add Location Work
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <input type="hidden" id="datatable_route" value="{{ $datatable_route }}">
                            <table id="datatable-location-work" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Name</th>
                                        <th>Lat-Long</th>
                                        <th>Radius</th>
                                        <th>Address</th>
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
        @include('js.location_work.script')
        <script>
            datatable();
        </script>
    @endpush
@endsection
