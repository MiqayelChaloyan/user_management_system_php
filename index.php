<?php
// Include the database connection
include 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css">
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Include Bootstrap CSS (Optional for styling buttons and other elements) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Users List</h2>
        <table id="users" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Country</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>

        <!-- TODO: Add button to redirect to the Add User page -->
        <a class="btn btn-primary btn-md" href="process/add-user.php">Add User</a>
    </div>
    
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js"></script>
    <!-- Include Bootstrap JS (Optional for buttons) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Sweet Alert for delete confirmation -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript" language="javascript">
    $(document).ready(function() {
        // TODO: Initialize the DataTable for displaying user data
        var dataTable = $('#users').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "response.php", // Your server-side PHP script that handles the data
                type: "POST",
                error: function() {
                    $(".users-error").html("");
                    $("#users").append('<tbody class="users-error"><tr><th colspan="8">No data found in the server</th></tr></tbody>');
                    $("#users_processing").css("display", "none");
                }
            },
            "columns": [
                { "data": "id" },
                { "data": "full_name" },
                { "data": "email", "orderable": false },
                { "data": "phone" },
                { "data": "country" },
                { "data": "age" },
                { "data": "gender" },
            ],
            "columnDefs": [
                {
                    "targets": 7,
                    "render": function(data, type, row, meta) {
                        return '<a href="process/update-user.php?id=' + row.id + '" class="mx-1 btn btn-primary btn-sm"><i class="fa fa-exchange"></i></a>' +
                               '<a href="process/delete-user.php?id=' + row.id + '" class="mx-1 btn btn-danger btn-sm delete-btn"><i class="fa fa-trash-o"></i></a>';
                    }
                }
            ]
        });

        // TODO: Add functionality for the delete button with confirmation
        $('#users').on('click', '.delete-btn', function(event) {
            event.preventDefault();
            const url = this.href;
            const row = $(this).closest('tr'); // Capture the row to be removed later

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to undo this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // TODO: Perform AJAX request to delete the user
                    $.ajax({
                        type: 'POST',
                        url: url,
                        dataType: 'json',
                        success: function(data) {
                            if (data.status === 'success') {
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: data.message,
                                    icon: 'success',
                                    showConfirmButton: false,
                                    timer: 1500,
                                    timerProgressBar: true
                                }).then(() => {
                                    row.remove(); // Remove the row from the table
                                });
                            } else {
                                Swal.fire(
                                    'Error!',
                                    data.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'Error!',
                                'There was an error deleting the user.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
    </script>
</body>
</html>
