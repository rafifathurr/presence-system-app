<script>
    $("form").submit(function(e) {
        e.preventDefault();
        if ($("#imageInput").val() == '') {
            swalWarning('Please Capture Image!');
        } else {
            formSubmit();
        }
    });

    function datatable() {
        const url = $('#datatable_route').val();
        $('#datatable-presence').DataTable({
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
                    data: 'created_at',
                    defaultContent: '-',
                },
                {
                    data: 'created_by',
                    defaultContent: '-',
                },
                {
                    data: 'warrant',
                    className: 'text-center',
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
            ],
            order: [
                [0, 'desc']
            ]
        });
    }
</script>
