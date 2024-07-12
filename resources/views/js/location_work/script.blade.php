<script>
    $("form").submit(function(e) {
        e.preventDefault();
        formSubmit();
    });

    function datatable() {
        const url = $('#datatable_route').val();
        $('#datatable-location-work').DataTable({
            autoWidth: false,
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: url,
                error: function(xhr, error, code) {
                    swalError(xhr.statusText);
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    width: '5%',
                    searchable: false
                },
                {
                    data: 'name',
                    defaultContent: '-',
                },
                {
                    data: 'latlong',
                    defaultContent: '-',
                },
                {
                    data: 'radius',
                    defaultContent: '-',
                },
                {
                    data: 'address',
                    defaultContent: '-',
                },
                {
                    data: 'action',
                    width: '15%',
                    className: 'text-center',
                    defaultContent: '-',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    }

    function destroy(id) {
        const url = '{{ url('location-work') }}/' + id;
        destroyRecord(url)
    }
</script>
