@extends('layouts.main')
@section('page')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card p-3">
                    <div class="card-header">
                        <h4 class="card-title my-auto">Add User</h4>
                    </div>
                    <form class="forms-sample" method="post"
                        action="{{ route('user-management.verificationUpdate', ['id' => $user->id]) }}">
                        @csrf
                        <div class="card-body">
                            @csrf
                            <div class="form-group">
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                <label for="attachment">Verification Face <span class="text-danger">*</span></label>
                                <div class="video-container">
                                    <video id="video" height="500" width="500" class="form-control" autoplay
                                        muted></video>
                                </div>
                                <canvas id="canvas" class="form-control w-100 h-auto d-none"></canvas>
                                <input type="hidden" name="face_encoding" id="face_encoding">
                                <div class="bg-warning text-center py-2 fw-bold" id="warning-text">
                                    Please Take Your Face to Camera
                                </div>
                                <div class="bg-success text-center text-white py-2 fw-bold d-none" id="success-text">
                                    Capture Face Success
                                </div>
                                <div class="text-center mt-2">
                                    <button type="button" class="btn btn-sm btn-warning mt-2" id="reset" disabled><i
                                            class="fas fa-undo me-1"></i> Reset</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="form-group text-end mt-2">
                                <a href="{{ url()->previous() }}" class="btn btn-sm btn-danger">
                                    <i class="fas fa-arrow-left"></i>
                                    Back
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('javascript-bottom')
        @include('js.user_management.script')
        <script>
            let video = document.getElementById('video');
            let canvas = document.getElementById('canvas');
            let context = canvas.getContext('2d');
            let resetButton = document.getElementById('reset');
            let baseUrlPresence = document.URL.substr(0, document.URL.lastIndexOf('/'));
            let baseUrl = baseUrlPresence.split('/user-management/verification').join('');
            let intervalId;

            setupCamera();

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
                }, 5000);
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
                resetButton.disabled = false;
                video.classList.add('d-none');
                canvas.classList.remove('d-none');
                $('#warning-text').addClass('d-none');
                $('#success-text').removeClass('d-none');
                clearInterval(intervalId);
                $("form").submit();
            }

            resetButton.addEventListener('click', function() {
                resetButton.disabled = true;
                video.classList.remove('d-none');
                canvas.classList.add('d-none');
                $('#warning-text').removeClass('d-none');
                $('#success-text').addClass('d-none');
                detection();
            });
        </script>
    @endpush
@endsection
