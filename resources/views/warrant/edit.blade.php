@extends('layouts.main')
@section('page')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card p-3">
                    <div class="card-header">
                        <h4 class="card-title my-auto">Edit Warrant : {{ $warrant->name }}</h4>
                    </div>
                    <form class="forms-sample" method="post" action="{{ route('warrant.update', ['id' => $warrant->id]) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('patch')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ $warrant->name }}" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_start">Date Start <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="date_start" name="date_start"
                                            min="{{ date('Y-m-d') }}" value="{{ $warrant->date_start }}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_finish">Date Finish <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="date_finish" name="date_finish"
                                            min="{{ date('Y-m-d') }}" value="{{ $warrant->date_finish }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="attachment">Attachment <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="attachment" name="attachment">
                                <a target="_blank" href="{{ asset($warrant->attachment) }}"><i class="fas fa-download"></i>
                                    Attachment Warrant</a>
                            </div>
                            <div class="form-group">
                                <label for="location_work">Location Work <span class="text-danger">*</span></label>
                                <select class="form-control" id="location_work" name="location_work"
                                    onchange="locationChange(this)" required>
                                    <option disabled hidden selected>Choose Location Work</option>
                                    @foreach ($location_works as $location_work)
                                        @if ($warrant->location_work_id == $location_work->id)
                                            <option value="{{ $location_work->id }}" selected>{{ $location_work->name }}
                                            </option>
                                        @else
                                            <option value="{{ $location_work->id }}">{{ $location_work->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
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
                                            value="{{ $warrant->locationWork->latitude }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="longitude">Longitude <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="longitude" name="longitude"
                                            value="{{ $warrant->locationWork->longitude }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="radius">Radius <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="radius" name="radius"
                                            value="{{ $warrant->locationWork->radius }}" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" name="address" id="address" cols="10" rows="3" readonly>{{ $warrant->locationWork->address }}</textarea>
                            </div>
                            <div class="table-responsive mt-5">
                                <table class="table table-bordered datatable" id="warrant_user">
                                    <thead>
                                        <tr>
                                            <th>
                                                Name
                                            </th>
                                            <th width="20%">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="table_body">
                                        <tr id="form_user">
                                            <td>
                                                <select class="form-control" id="user">
                                                    <option disabled hidden selected>Choose User</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}">
                                                            {{ $user->employee_number . ' - ' . $user->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td align="center">
                                                <button type="button" class="btn btn-sm btn-primary"
                                                    onclick="addUser()"><i class="fas fa-plus me-2"></i>Add User</button>
                                            </td>
                                        </tr>
                                        @foreach ($warrant->warrantUser as $warrant_user)
                                            <tr id='user_{{ $warrant_user->user_id }}'>
                                                <td>
                                                    <span
                                                        id='user_name_{{ $warrant_user->user_id }}'>{{ $warrant_user->user->employee_number . ' - ' . $warrant_user->user->name }}</span>
                                                    <input type='hidden' id='detail_user_{{ $warrant_user->user_id }}'
                                                        name='warrant_user[{{ $warrant_user->user_id }}][name]'
                                                        value='{{ $warrant_user->user->employee_number . ' - ' . $warrant_user->user->name }}'>
                                                </td>
                                                <td align='center'>
                                                    <button type='button' class='delete-row btn btn-sm btn-danger'
                                                        onclick='deleteRow({{ $warrant_user->user_id }})'
                                                        title='Delete'><i class='fas fa-trash'></i></button>
                                                    <input type='hidden' class='form-control' name='user_check[]'
                                                        value='{{ $warrant_user->user_id }}'>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
        @include('js.warrant.script')
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
            });
            tileLayer.addTo(map);

            marker = L.marker([$('#latitude').val(), $('#longitude').val()]).addTo(map).bindPopup($('#address').val())
                .openPopup();
            circleMarker = L.circle([$('#latitude').val(), $('#longitude').val()], parseFloat($('#radius').val())).addTo(map);
            map.setView([$('#latitude').val(), $('#longitude').val()], 17);

            function locationChange(element) {
                $.get('{{ url('location-work') }}/' + element.value, {}).done(function(data) {

                    if (marker == null) {
                        marker = L.marker([data.latitude, data.longitude]).addTo(map).bindPopup(data.address)
                            .openPopup();
                    } else {
                        marker.setLatLng([data.latitude, data.longitude]).bindPopup(data.address).openPopup();
                    }

                    if (circleMarker == null) {
                        circleMarker = L.circle(marker.getLatLng(), data.radius).addTo(map);
                    } else {
                        map.removeLayer(circleMarker);
                        circleMarker = L.circle(marker.getLatLng(), data.radius).addTo(map);
                    }

                    map.setView(marker.getLatLng(), 17);

                    $('#latitude').val(data.latitude);
                    $('#longitude').val(data.longitude);
                    $('#radius').val(data.radius);
                    $('#address').val(data.address);
                }).fail(function(xhr, status, error) {
                    swalError(status);
                });
            }
        </script>
    @endpush
@endsection
