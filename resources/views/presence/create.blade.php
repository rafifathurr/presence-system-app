@extends('layouts.main')
@section('page')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card p-3">
                    <div class="card-header">
                        <h4 class="card-title my-auto">Presence</h4>
                    </div>
                    <form class="forms-sample" method="post" action="{{ route('presence.store') }}"
                        enctype="multipart/form-data">
                        <div class="card-body">
                            @csrf
                            <div class="p-2 mb-2 rounded-3 border border-default-1">
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
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Warrant</label>
                                    <div class="col-sm-9 col-form-label">
                                        <a target="_blank" href="{{ route('warrant.show', ['id' => $warrant->id]) }}">
                                            {{ $warrant->name }}<i class="fas fa-external-link-alt ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="hidden" name="warrant" value="{{ $warrant->id }}">
                                        <label for="attachment">Photo <span class="text-danger">*</span></label>
                                        <div class="video-container">
                                            {{-- <video id="video" class="form-control w-100 h-auto"></video> --}}
                                            <video id="video" height="500" width="500" class="form-control"
                                                autoplay muted></video>
                                        </div>
                                        <canvas id="canvas" class="form-control w-100 h-auto d-none"></canvas>
                                        <input type="hidden" name="attachment" id="imageInput">
                                        <div class="bg-warning text-center py-2 fw-bold" id="warning-text">
                                            Please Take Your Face to Camera
                                        </div>
                                        <div class="bg-success text-center text-white py-2 fw-bold d-none"
                                            id="success-text">
                                            Capture Face Success
                                        </div>
                                        <div class="text-center mt-2">
                                            <button type="button" class="btn btn-sm btn-warning mt-2" id="reset"
                                                disabled><i class="fas fa-undo me-1"></i> Reset</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Map Location <span class="text-danger">*</span></label>
                                        <div id="map"></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="latitude">Latitude <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="latitude" name="latitude"
                                                    value="{{ old('latitude') }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="longitude">Longitude <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="longitude" name="longitude"
                                                    value="{{ old('longitude') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Address</label>
                                        <textarea class="form-control" name="address" id="address" cols="10" rows="3" readonly>{{ old('address') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="warrant_latitude" value="{{ $warrant->locationWork->latitude }}">
                        <input type="hidden" id="warrant_longitude" value="{{ $warrant->locationWork->longitude }}">
                        <input type="hidden" id="warrant_radius" value="{{ $warrant->locationWork->radius }}">
                        <input type="hidden" id="warrant_address" value="{{ $warrant->locationWork->address }}">
                        <div class="card-footer">
                            <div class="form-group text-end mt-2">
                                <a href="{{ url()->previous() }}" class="btn btn-sm btn-danger">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Back
                                </a>
                                <button type="submit" class="btn btn-sm btn-primary" id="submit-button" disabled>Submit<i
                                        class="fas fa-check ms-1"></i></button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('js.presence.script')
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

                    let address = data.address.road + ', ' + data.address.city_district +
                        ', ' + data.address.city + ', ' + data.address.country;

                    const popup = L.popup({
                        closeOnClick: false,
                        autoClose: false
                    }).setContent('<b>Current Location : </b>' + address);

                    if (marker == null) {
                        marker = L.marker(e.latlng).addTo(map).bindPopup(popup).openPopup().off('click');
                    } else {
                        marker.setLatLng(e.latlng);
                    }

                    $('#latitude').val(e.latlng.lat);
                    $('#longitude').val(e.latlng.lng);
                    $('#address').val(address);

                    warrantCompareCurrent();

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

            function warrantCompareCurrent() {

                const popup = L.popup({
                    closeOnClick: false,
                    autoClose: false
                }).setContent('<b>Warrant Location : </b>' + $(
                    '#warrant_address').val());

                let markerWarrant = L.marker([$('#warrant_latitude').val(), $('#warrant_longitude').val()]).addTo(map)
                    .bindPopup(popup)
                    .openPopup();
                let circleMarkerWarrant = L.circle([$('#warrant_latitude').val(), $('#warrant_longitude').val()], parseFloat($(
                    '#warrant_radius').val())).addTo(map);

                let markerLatLng = marker.getLatLng();
                let circleLatLng = circleMarkerWarrant.getLatLng();
                let radius = circleMarkerWarrant.getRadius();

                map.fitBounds([marker.getLatLng(), markerWarrant.getLatLng()]);

                let distance = markerLatLng.distanceTo(circleLatLng);

                if (distance <= radius) {
                    $('#submit-button').attr('disabled', false);
                } else {
                    $('#submit-button').attr('disabled', true);
                    swalWarning('Your Location Far From Location');
                }
            }

            let video = document.getElementById('video');
            let canvas = document.getElementById('canvas');
            let context = canvas.getContext('2d');
            let resetButton = document.getElementById('reset');
            let baseUrlPresence = document.URL.substr(0, document.URL.lastIndexOf('/'));
            let baseUrl = baseUrlPresence.split('/presence').join('');

            Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(baseUrl + "/models"),
                faceapi.nets.faceLandmark68Net.loadFromUri(baseUrl + "/models"),
                faceapi.nets.faceRecognitionNet.loadFromUri(baseUrl + "/models"),
                faceapi.nets.faceExpressionNet.loadFromUri(baseUrl + "/models"),
                faceapi.nets.ageGenderNet.loadFromUri(baseUrl + "/models")
            ]).then(startVideo);

            function startVideo() {
                navigator.getUserMedia({
                        video: {}
                    },
                    stream => (video.srcObject = stream),
                    err => console.error(err)
                );
            }

            video.addEventListener("playing", () => {
                console.log("playing called");
                const canvas = faceapi.createCanvasFromMedia(video);
                let container = document.querySelector(".container");
                container.append(canvas);

                const displaySize = {
                    width: video.width,
                    height: video.height
                };
                faceapi.matchDimensions(canvas, displaySize);

                let encode_face;

                setInterval(async () => {
                    const detections = await faceapi.detectAllFaces(video, new faceapi
                        .TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();

                    if (detections.map(d => d.descriptor).length > 0) {
                        console.log(detections.map(d => d.descriptor));
                        snapCapture();
                        return;
                    }
                }, 1000);
            });

            function adjustVideoCanvas() {
                const container = document.querySelector('.video-container');
                const width = container.clientWidth;
                const height = container.clientHeight;

                video.width = width;
                video.height = height;
                canvas.width = width;
                canvas.height = height;
            }

            function snapCapture() {
                adjustVideoCanvas();
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                let dataURL = canvas.toDataURL('image/png');
                resetButton.disabled = false;
                video.classList.add('d-none');
                canvas.classList.remove('d-none');
                $('#imageInput').val(dataURL);
                $('#warning-text').addClass('d-none');
                $('#success-text').removeClass('d-none');
            }

            resetButton.addEventListener('click', function() {
                resetButton.disabled = true;
                video.classList.remove('d-none');
                canvas.classList.add('d-none');
                $('#imageInput').val('');
                $('#warning-text').removeClass('d-none');
                $('#success-text').addClass('d-none');
            });

            map.dragging.disable();
            map.touchZoom.disable();
            map.doubleClickZoom.disable();
            map.scrollWheelZoom.disable();
            map.boxZoom.disable();
            map.keyboard.disable();
            if (map.tap) map.tap.disable();
            document.getElementById('map').style.cursor = 'default';
        </script>
    @endpush
@endsection
