@extends('layouts.main')
@section('page')
    <div class="px-3">
        <div class="form-group">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                <div class="my-auto">
                    <h3 class="fw-bold">Welcome {{ Auth::user()->name }} !</h3>
                </div>
                <div class="ms-lg-3 my-auto">
                    <div class="bg-success rounded-3 p-2">
                        <h5 id="timestamp" class="text-white text-center fw-bold my-auto px-1"></h5>
                    </div>
                </div>
            </div>
        </div>
        @if ($presence)
            <div class="form-group">
                <a href="{{ route('presence.create') }}" class="btn btn-lg btn-primary fw-bold" disabled>
                    <i class="fas fa-plus me-1"></i>
                    Presence
                </a>
            </div>
        @endif
    </div>
    @push('javascript-bottom')
        <script>
            function updateTimestamp() {
                var now = new Date();
                var formattedTime = now.toLocaleTimeString();
                $('#timestamp').text(formattedTime);
            }

            updateTimestamp(); // Initial call to set the time immediately
            setInterval(updateTimestamp, 1000); // Update every second
        </script>
    @endpush
@endsection
