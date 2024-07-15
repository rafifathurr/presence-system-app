@extends('layouts.main')
@section('page')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card p-3">
                    <div class="card-header">
                        <h4 class="card-title my-auto">Detail Presence</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Name Staff</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $presence->createdBy->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Date Time</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d F Y H:i:s', strtotime($presence->created_at)) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Attachment</label>
                            <div class="col-sm-9 col-form-label">
                                <img width="25%" alt="upload" src="{{ asset($presence->attachment) }}"
                                    class="rounded-3 border border-1-default">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Latitude, Longitude</label>
                            <div class="col-sm-9 col-form-label">
                                <a target="_blank"
                                    href="{{ 'https://www.google.com/maps/' . '@' . $presence->latitude . ',' . $presence->longitude . ',15z?entry=ttu' }}">
                                    {{ $presence->latitude . ', ' . $presence->longitude }}<i
                                        class="fas fa-external-link-alt ms-1"></i></a>
                                <input type="hidden" id="latitude" value="{{ $presence->latitude }}">
                                <input type="hidden" id="longitude" value="{{ $presence->longitude }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Address</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $presence->address }}
                                <input type="hidden" id="address" value="{{ $presence->address }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated By</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $presence->updatedBy->name }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated At</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d F Y H:i:s', strtotime($presence->updated_at)) }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <div id="map">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="form-group text-end mt-2">
                            <a href="{{ route('presence.index') }}" class="btn btn-sm btn-danger">
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
        @include('js.presence.script')
    @endpush
@endsection
