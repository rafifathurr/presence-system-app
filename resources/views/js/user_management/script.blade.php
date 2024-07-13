<script>
    $("form").submit(function(e) {
        e.preventDefault();
        formSubmit();
    });

    function datatable() {
        const url = $('#datatable_route').val();
        $('#datatable-user-management').DataTable({
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
                    data: 'employee_number',
                    defaultContent: '-',
                },
                {
                    data: 'name',
                    defaultContent: '-',
                },
                {
                    data: 'status',
                    className: 'text-center',
                    defaultContent: '-',
                },
                {
                    data: 'role',
                    defaultContent: '-',
                },
                {
                    data: 'division',
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
        const url = '{{ url('user-management') }}/' + id;
        destroyRecord(url)
    }
</script>
