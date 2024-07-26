<script>
    $("form").submit(function(e) {
        e.preventDefault();
        if ($("input[name='user_check[]']").val() === undefined) {
            swalWarning('Please Complete The Record!');
        } else {
            formSubmit();
        }
    });

    function datatable() {
        const url = $('#datatable_route').val();
        $('#datatable-warrant').DataTable({
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
                    data: 'duration',
                    defaultContent: '-',
                },
                {
                    data: 'status',
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

    function addUser() {
        let user = $('#user');

        if (user.val() != null) {

            let user_id = user.val();
            let user_name = $("#user option:selected").text();

            let form_user = $("#form_user");
            let tr = $("<tr id='user_" + user_id + "'></tr>");
            let td_name = $("<td>" +
                "<span id='user_name_" + user_id + "'>" + user_name + "</span>" +
                "<input type='hidden' id='detail_user_" + user_id + "' name='warrant_user[" + user_id +
                "][name]' value='" +
                user_name +
                "'>" +
                "</td>");

            let td_user_action = $(
                "<td align='center'>" +
                "<button type='button' class='btn btn-sm btn-danger' onclick='deleteRow(" + user_id +
                ")' title='Delete'><i class='fas fa-trash'></i></button>" +
                "<input type='hidden' class='form-control' name='user_check[]' value='" +
                user_id +
                "'>" +
                "</td>"
            );

            (tr.append(td_name).append(td_user_action)).insertAfter(form_user);

            // Reset Field Value
            $('#user option[value=' + user_id + ']').each(function() {
                $(this).remove();
            });
            $('#user').val('');
        } else {
            swalWarning('Please Complete The Record!');
        }
    }

    function deleteRow(id) {
        $('#user').append($('<option>', {
            value: $('#detail_user_' + id).val(),
            text: $('#user_name_' + id).text()
        }));
        $('#user_' + id).remove();
    }

    function detailDatatables() {
        $('#warrant_user').DataTable({
            autoWidth: false,
            responsive: true,
        });

        $('#warrant_presence_user').DataTable({
            autoWidth: false,
            responsive: true,
        });
    }

    function destroy(id) {
        const url = '{{ url('warrant') }}/' + id;
        destroyRecord(url)
    }
</script>
