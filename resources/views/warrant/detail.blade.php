@extends('layouts.main')
@section('page')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card p-3">
                    <div class="card-header">
                        <h4 class="card-title my-auto">Detail Warrant {{ $warrant->name }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $warrant->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Duration Warrant</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d F Y', strtotime($warrant->date_start)) . ' - ' . date('d F Y', strtotime($warrant->date_finish)) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Attachment</label>
                            <div class="col-sm-9 col-form-label">
                                <a target="_blank" href="{{ asset($warrant->attachment) }}"><i class="fas fa-download"></i>
                                    Attachment Warrant</a>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Location Work</label>
                            <div class="col-sm-9 col-form-label">
                                <a target="_blank"
                                    href="{{ route('location-work.show', ['id' => $warrant->location_work_id]) }}">
                                    {{ $warrant->locationWork->name }}<i class="fas fa-external-link-alt ms-1"></i></a>
                                <input type="hidden" id="latitude" value="{{ $warrant->locationWork->latitude }}">
                                <input type="hidden" id="longitude" value="{{ $warrant->locationWork->longitude }}">
                                <input type="hidden" id="radius" value="{{ $warrant->locationWork->radius }}">
                                <input type="hidden" id="address" value="{{ $warrant->locationWork->address }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated By</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $warrant->updatedBy->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated At</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d F Y H:i:s', strtotime($warrant->updated_at)) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div id="map">
                            </div>
                        </div>
                        <h4 class="card-title my-auto mt-5 pb-0">Warrant User</h4>
                        <div class="table-responsive mt-5">
                            <table class="table table-bordered datatable" id="warrant_user">
                                <thead>
                                    <tr>
                                        <th width="10%">
                                            No
                                        </th>
                                        <th width="40%">
                                            Employee Number
                                        </th>
                                        <th>
                                            Name
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="table_body">
                                    @foreach ($warrant->warrantUser as $index => $warrant_user)
                                        <tr>
                                            <td>
                                                {{ $index + 1 }}
                                            </td>
                                            <td>
                                                {{ $warrant_user->user->employee_number }}
                                            </td>
                                            <td>
                                                {{ $warrant_user->user->name }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($show_history)
                            <div class="card-header d-flex justify-content-between">
                                <h4 class="card-title mt-5 pb-0">History Presence User</h4>
                                <div class="mt-5">
                                    <a href="{{ route('warrant.presenceWarrantExport', ['id' => $warrant->id]) }}"
                                        class="btn btn-sm btn-success">
                                        Export Excel
                                        <i class="fas fa-file-excel ms-1"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive mt-5">
                                <table class="table table-bordered datatable" id="warrant_presence_user">
                                    <thead>
                                        <tr>
                                            <th width="10%">
                                                No
                                            </th>
                                            <th>
                                                Date Time
                                            </th>
                                            <th>
                                                Lat-Long
                                            </th>
                                            <th>
                                                Address
                                            </th>
                                            <th>
                                                Attachment
                                            </th>
                                            <th>
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="table_body">
                                        @foreach ($warrant->presence as $index => $presence)
                                            <tr>
                                                <td width="5%">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td>
                                                    {{ date('d F Y H:i:s', strtotime($presence->created_at)) }}
                                                </td>
                                                <td width="20%">
                                                    <a target="_blank"
                                                        href="{{ 'https://www.google.com/maps/' . '@' . $presence->latitude . ',' . $presence->longitude . ',15z?entry=ttu' }}">
                                                        {{ $presence->latitude . ',' . $presence->longitude }}<i
                                                            class="fas fa-external-link-alt ms-1"></i></a>
                                                </td>
                                                <td>
                                                    {{ $presence->address }}
                                                </td>
                                                <td align="center">
                                                    <img width="45%" alt="upload"
                                                        src="{{ asset($presence->attachment) }}"
                                                        class="rounded-3 border border-1-default">
                                                </td>
                                                <td align="center" width="10%">
                                                    <a target="_blank"
                                                        href="{{ route('presence.show', ['id' => $presence->id]) }}"
                                                        class="btn btn-sm btn-primary">Detail<i
                                                            class="fas fa-external-link-alt ms-1"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="form-group text-end mt-2">
                            <a href="{{ route('warrant.index') }}" class="btn btn-sm btn-danger">
                                <i class="fas fa-arrow-left me-1"></i>
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('js.warrant.script')
        <script>
            let map = L.map('map', {
                editable: true,
                minZoom: 2.4,
                maxZoom: 22,
                attributionControl: false,
                drawControl: true,
                cursor: true,
                maxBounds: [
                    [90, -180],
                    [-90, 180]
                ]
            });
            tileLayer.addTo(map);

            L.marker([$('#latitude').val(), $('#longitude').val()]).addTo(map).bindPopup($('#address').val()).openPopup();
            L.circle([$('#latitude').val(), $('#longitude').val()], parseFloat($('#radius').val())).addTo(map);
            map.setView([$('#latitude').val(), $('#longitude').val()], 17);

            detailDatatables();
        </script>
    @endpush
@endsection
