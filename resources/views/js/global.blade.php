@if (Session::has('success'))
    <script type="text/javascript">
        Swal.fire({
            icon: 'success',
            title: '{{ Session::get('success') }}',
            customClass: {
                confirmButton: 'btn btn-primary mb-3',
            },
            buttonsStyling: false,
            timer: 3000,
        });
    </script>
    @php
        Session::forget('success');
    @endphp
@elseif(Session::has('failed'))
    <script type="text/javascript">
        Swal.fire({
            icon: 'error',
            title: '{{ Session::get('failed') }}',
            customClass: {
                confirmButton: 'btn btn-primary mb-3',
            },
            buttonsStyling: false,
            timer: 3000,
        });
    </script>
    @php
        Session::forget('failed');
    @endphp
@endif
<script>
    const tileLayer = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        noWrap: true,
        maxZoom: 22,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    });

    function swalProcess() {
        Swal.fire({
            title: 'Please Waiting',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });
    }

    function swalSuccess(message) {
        Swal.fire({
            icon: 'success',
            title: message,
            customClass: {
                confirmButton: 'btn btn-primary mb-3',
            },
            buttonsStyling: false,
            timer: 3000,
        });
    }

    function swalError(message) {
        Swal.fire({
            icon: 'error',
            title: message,
            customClass: {
                confirmButton: 'btn btn-primary mb-3',
            },
            buttonsStyling: false,
            timer: 3000,
        });
    }

    function swalWarning(message) {
        Swal.fire({
            icon: 'warning',
            title: message,
            customClass: {
                confirmButton: 'btn btn-primary mb-3',
            },
            buttonsStyling: false,
            timer: 3000,
        });
    }

    function formSubmit() {
        Swal.fire({
            title: 'Are You Sure Want To Submit Record?',
            icon: 'question',
            showCancelButton: true,
            allowOutsideClick: false,
            customClass: {
                confirmButton: 'btn btn-primary me-1 mb-3',
                cancelButton: 'btn btn-danger mb-3',
            },
            buttonsStyling: false,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Back'
        }).then((result) => {
            if (result.isConfirmed) {
                swalProcess();
                $('form').unbind('submit').submit();
            }
        })
    }

    function destroyRecord(url) {
        let token = $('meta[name="token"]').attr('content');

        Swal.fire({
            title: 'Are You Sure Want To Delete Record?',
            icon: 'question',
            showCancelButton: true,
            allowOutsideClick: false,
            customClass: {
                confirmButton: 'btn btn-primary me-1 mb-3',
                cancelButton: 'btn btn-danger mb-3',
            },
            buttonsStyling: false,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Back'
        }).then((result) => {
            if (result.isConfirmed) {
                swalProcess();
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    cache: false,
                    data: {
                        _token: token
                    },
                    success: function(data) {
                        location.reload();
                    },
                    error: function(xhr, error, code) {
                        swalError(error);
                    }
                });
            }
        })
    }
</script>
