<?php
include '../includes/db.php';

// TODO: Consider using prepared statements for any dynamic user input to prevent SQL injection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    // TODO: Validate the user_id to ensure it's numeric or meets the expected format
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $full_name = $user['full_name'];
        $email = $user['email'];
        $phone_number = $user['phone_number'];
        $country = $user['country'];
    } else {
        // TODO: Handle case where user does not exist
        echo "User not found.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Update Form</title>

    <!-- TODO: Use a local copy of Bootstrap for production environments to improve page load performance -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- TODO: Ensure to validate the integrity of external JS libraries using Subresource Integrity (SRI) in production -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/nprogress@0.2.0/nprogress.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body class="bg-light">
    <div class="d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg" style="width: 100%; max-width: 600px;">
            <div class="card-body">
                <h1 class="text-center text-primary mb-4">Update Form</h1>
                <form id="user_form_update" action="process/update-user.php" method="POST">
                    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="country" class="form-label">Country</label>
                        <input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($country); ?>" required>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary w-50" id="submit_button">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        // TODO: Add validation for specific input types like phone number and email format
        $("#user_form_update").validate({
            rules: {
                full_name: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                },
                phone_number: {
                    required: true,
                    // TODO: Add phone number validation pattern (e.g., for specific countries)
                },
                country: {
                    required: true,
                }
            },
            submitHandler: submitForm
        });

        // Function to submit the form via AJAX
        function submitForm() {
            NProgress.start();
            var data = $("#user_form_update").serialize();
            $.ajax({
                type: 'POST',
                url: '../actions/update.php', // TODO: Verify the action file path
                data: data,
                dataType: 'json',
                success: function(data) {
                    NProgress.done();
                    if (data.success) {
                        toastr.options = {
                            positionClass: 'toast-top-right'
                        };
                        toastr.success(data.message);
                        Swal.fire({
                            title: 'Success',
                            text: 'User updated successfully!',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500 
                        }).then(() => {
                            setTimeout(function() {
                                window.location.href = "../index.php"; // TODO: Redirect to appropriate page
                            }, 1500);
                        });
                    } else {
                        // TODO: Handle specific error messages from backend response
                        Swal.fire({
                            title: '',
                            text: data.message || 'An error occurred, please try again.',
                            icon: 'error',
                            showConfirmButton: false,
                            timer: 1500 
                        }).then(() => {
                            setTimeout(function() {
                                window.location.href = "../index.php"; // TODO: Redirect to the appropriate page after error
                            }, 1500);
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    NProgress.done();
                    // TODO: Log the error and possibly send to server for further analysis
                    Swal.fire({
                        title: '',
                        text: 'An error occurred, please try again.',
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 1500 
                    }).then(() => {
                        setTimeout(function() {
                            window.location.href = "../index.php"; // TODO: Handle error redirect
                        }, 1500);
                    });
                }
            });
            return false;
        }
    });
</script>

</body>
</html>
