@extends('layouts.main')
@section('page')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card p-3">
                    <div class="card-header">
                        <h4 class="card-title my-auto">Detail User {{ $user->name }}</h4>
                    </div>
                    <div class="card-body">
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
                        @if ($verification_status_show)
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-9 col-form-label">
                                    {!! $verification_status !!}
                                </div>
                            </div>
                            @if (!is_null($user->face_image) && $show_dataset)
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label">Image Dataset</label>
                                    <div class="col-sm-9 col-form-label">
                                        <img width="50%" alt="upload" src="{{ asset($user->face_image) }}"
                                            class="rounded-3 border border-1-default">
                                    </div>
                                </div>
                            @endif
                        @endif
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Username</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $user->username }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $user->email }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Role</label>
                            <div class="col-sm-9 col-form-label">
                                {{ $user_role }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Division</label>
                            <div class="col-sm-9 col-form-label">
                                {{ !is_null($user->division_id) ? $user->division->name : '-' }}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Updated At</label>
                            <div class="col-sm-9 col-form-label">
                                {{ date('d F Y H:i:s', strtotime($user->updated_at)) }}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="form-group text-end mt-2">
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-danger">
                                <i class="fas fa-arrow-left me-1"></i>
                                Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Create -->
    <div class="modal fade" id="verificationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form class="forms-sample" method="post"
                    action="{{ route('user-management.verificationUpdate', ['id' => $user->id]) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('patch')
                    <div class="modal-header">
                        <h4 class="modal-title" id="exampleModalLongTitle"><b>Face Verification User</b></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="attachment">Verification Face <span class="text-danger">*</span></label>
                            <div class="video-container">
                                <video id="video" height="500" width="500" class="form-control" autoplay loop muted
                                    playsinline></video>
                            </div>
                            <canvas id="canvas" class="form-control w-100 h-auto d-none"></canvas>
                            <input type="hidden" name="face_encoding" id="face_encoding">
                            <input type="hidden" name="face_image" id="face_image">
                            <div class="bg-warning text-center py-2 fw-bold" id="warning-text">
                                Please Take Your Face to Camera
                            </div>
                            <div class="bg-success text-center text-white py-2 fw-bold d-none" id="success-text">
                                Capture Face Success
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        <script>
            let video = document.getElementById('video');
            let canvas = document.getElementById('canvas');
            let context = canvas.getContext('2d');
            let baseUrlPresence = document.URL.substr(0, document.URL.lastIndexOf('/'));
            let baseUrl = baseUrlPresence.split('/user-management').join('');
            let intervalId;
            let localStream;

            $('#verificationModal').on('show.bs.modal', function(e) {
                setupCamera();
            });

            $('#verificationModal').on('hide.bs.modal', function(e) {
                stopCamera();
            });

            function setupCamera() {

                Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(baseUrl + "/models"),
                    faceapi.nets.faceLandmark68Net.loadFromUri(baseUrl + "/models"),
                    faceapi.nets.faceRecognitionNet.loadFromUri(baseUrl + "/models"),
                ]).then(startVideo);

                function startVideo() {
                    navigator.mediaDevices.getUserMedia({
                        video: true
                    }).then(function(stream) {
                        video.srcObject = stream;
                        video.play();
                        localStream = stream.getTracks();
                    });
                }

                video.addEventListener("playing", () => {
                    const canvas = faceapi.createCanvasFromMedia(video);
                    let container = document.querySelector(".container");
                    container.append(canvas);

                    const displaySize = {
                        width: video.width,
                        height: video.height
                    };
                    faceapi.matchDimensions(canvas, displaySize);

                    let encode_face;
                    detection();

                });
            }

            function detection() {
                intervalId = setInterval(async () => {
                    const detections = await faceapi.detectAllFaces(video, new faceapi
                        .TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptors();

                    if (detections.map(d => d.descriptor).length > 0) {
                        const descriptor = detections[0].descriptor;
                        $('#face_encoding').val(JSON.stringify(descriptor));
                        snapCapture();
                    }
                }, 150);
            }

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

                if (dataURL != 'data:,') {

                    $('#face_image').val(dataURL);
                    $('#warning-text').addClass('d-none');
                    $('#success-text').removeClass('d-none');

                    clearInterval(intervalId);
                    swalProcess();

                    $("form").submit();
                }
            }

            function stopCamera() {
                localStream.forEach((track) => {
                    track.stop();
                });
            }
        </script>
    @endpush
@endsection
