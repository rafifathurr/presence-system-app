@extends('layouts.main')
@section('page')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card p-3">
                    <div class="card-header">
                        <h4 class="card-title my-auto">Detail Location Work {{ $location_work->name }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $location_work->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Latitude, Longitude</label>
                            <div class="col-sm-9 col-form-label">
                                <a target="_blank"
                                    href="{{ 'https://www.google.com/maps/' . '@' . $location_work->latitude . ',' . $location_work->longitude . ',15z?entry=ttu' }}">
                                    {{ $location_work->latitude . ', ' . $location_work->longitude }}<i
                                        class="fas fa-external-link-alt ms-1"></i></a>
                                <input type="hidden" id="latitude" value="{{ $location_work->latitude }}">
                                <input type="hidden" id="longitude" value="{{ $location_work->longitude }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Radius</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $location_work->radius }} Meter
                                <input type="hidden" id="radius" value="{{ $location_work->radius }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Address</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $location_work->address }}
                                <input type="hidden" id="address" value="{{ $location_work->address }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated By</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $location_work->updatedBy->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated At</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d F Y H:i:s', strtotime($location_work->updated_at)) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div id="map">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="form-group text-end mt-2">
                            <a href="{{ route('location-work.index') }}" class="btn btn-sm btn-danger">
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
        @include('js.location_work.script')
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
            L.circle([$('#latitude').val(), $('#longitude').val()], parseFloat($('#radius').val())).addTo(map)
            map.setView([$('#latitude').val(), $('#longitude').val()], 17);
        </script>
    @endpush
@endsection
