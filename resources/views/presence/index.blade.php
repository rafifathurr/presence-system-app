@extends('layouts.main')
@section('page')
    <div class="px-3">
        <div class="row">
            <div class="col-md-12">
                <div class="card p-3">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="card-title my-auto">Presence Data</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <input type="hidden" id="datatable_route" value="{{ $datatable_route }}">
                            <table id="datatable-presence" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Date Time</th>
                                        <th>User</th>
                                        <th>Warrant</th>
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
        @include('js.presence.script')
        <script>
            datatable();
        </script>
    @endpush
@endsection
