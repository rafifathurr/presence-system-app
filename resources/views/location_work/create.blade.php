@extends('layouts.main')
@section('page')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card p-3">
                    <div class="card-header">
                        <h4 class="card-title my-auto">Location Work</h4>
                    </div>
                    <form class="forms-sample" method="post" action="{{ route('location-work.store') }}"
                        enctype="multipart/form-data">
                        <div class="card-body">
                            @csrf
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="">Map Location <span class="text-danger">*</span></label>
                                <div id="map">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="latitude">Latitude <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="latitude" name="latitude"
                                            value="{{ old('latitude') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="longitude">Longitude <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="longitude" name="longitude"
                                            value="{{ old('longitude') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="radius">Radius <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="radius" name="radius"
                                            value="{{ old('radius') }}" oninput="setRadius(this)" readonly required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" name="address" id="address" cols="10" rows="3" readonly>{{ old('address') }}</textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="form-group text-end mt-2">
                                <a href="{{ route('warrant.index') }}" class="btn btn-sm btn-danger">
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
        @include('js.location_work.script')
        <script>
            let marker;
            let circleMarker;
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
            }).locate({
                setView: true,
                maxZoom: 20,
            });
            tileLayer.addTo(map);

            map.on('locationfound', function(e) {

                $.get('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + e.latlng.lat + '&lon=' + e
                    .latlng.lng, {}).done(function(data) {

                    let address = data.display_name;

                    if (marker == null) {
                        marker = L.marker(e.latlng).addTo(map).bindPopup('<b>Current Location </b>: ' + address)
                            .openPopup();
                    } else {
                        marker.setLatLng(e.latlng).bindPopup('<b>Current Location </b>: ' + address)
                            .openPopup();
                    }

                    if (circleMarker != null) {
                        map.removeLayer(circleMarker);
                        circleMarker = null;
                    }

                    map.setView(e.latlng, 17);

                    $('#latitude').val(e.latlng.lat);
                    $('#longitude').val(e.latlng.lng);
                    $('#address').val(address);
                    $('#radius').val('');
                    $('#radius').removeAttr('readonly');

                }).fail(function(xhr, status, error) {
                    swalError(status);
                });
            })

            map.on('locationerror', function(e) {
                var message = "Geolocation error: " + e.message;
                switch (e.code) {
                    case e.PERMISSION_DENIED:
                        message = "Geolocation error: Permission denied.";
                        break;
                    case e.POSITION_UNAVAILABLE:
                        message = "Geolocation error: Position unavailable.";
                        break;
                    case e.TIMEOUT:
                        message = "Geolocation error: Timeout expired.";
                        break;
                    default:
                        message = "Geolocation error: Unknown error.";
                }
                alert(message);
            });

            map.on('click', function(e) {

                $.get('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + e.latlng.lat + '&lon=' + e
                    .latlng.lng, {}).done(function(data) {

                    let address = data.display_name;

                    if (marker == null) {
                        marker = L.marker(e.latlng).addTo(map).bindPopup(address).openPopup();
                    } else {
                        marker.setLatLng(e.latlng).bindPopup(address).openPopup();
                    }

                    if (circleMarker != null) {
                        map.removeLayer(circleMarker);
                        circleMarker = null;
                    }

                    map.setView(e.latlng, 17);

                    $('#latitude').val(e.latlng.lat);
                    $('#longitude').val(e.latlng.lng);
                    $('#address').val(address);
                    $('#radius').val('');
                    $('#radius').removeAttr('readonly');

                }).fail(function(xhr, status, error) {
                    swalError(status);
                });

            });

            function setRadius(element) {
                if (circleMarker == null) {
                    circleMarker = L.circle(marker.getLatLng(), element.value).addTo(map);
                } else {
                    circleMarker.setRadius(element.value)
                }
            }
        </script>
    @endpush
@endsection
