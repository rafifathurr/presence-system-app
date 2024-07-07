<script>
    $("form").submit(function(e) {
        e.preventDefault();
        formSubmit();
    });

    function datatable() {
        const url = $('#datatable_route').val();
        $('#datatable-division').DataTable({
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
                    width: '10%',
                    searchable: false
                },
                {
                    data: 'name',
                    defaultContent: '-',
                },
                {
                    data: 'action',
                    width: '20%',
                    className: 'text-center',
                    defaultContent: '-',
                    orderable: false,
                    searchable: false
                },
            ]
        });
    }

    function destroy(id) {
        const url = '{{ url('user-management') }}/' + id;
        destroyRecord(url)
    }
</script>
