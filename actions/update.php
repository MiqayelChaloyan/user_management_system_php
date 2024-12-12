<?php
include '../includes/db.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['user_id']) && is_numeric($_POST['user_id'])) {
        $user_id = $_POST['user_id'];

        $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : '';
        $country = isset($_POST['country']) ? trim($_POST['country']) : '';

        if (empty($full_name) || empty($email) || empty($phone_number) || empty($country)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit;
        }

        $query = "UPDATE users SET full_name = ?, email = ?, phone_number = ?, country = ? WHERE id = ?";

        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param('ssssi', $full_name, $email, $phone_number, $country, $user_id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            } else {
                $error = $stmt->error;
                echo json_encode(['success' => false, 'message' => 'Failed to update user', 'error' => $error]);
            }

            $stmt->close();
        } else {
            $error = $conn->error;
            echo json_encode(['success' => false, 'message' => 'Failed to prepare the SQL statement', 'error' => $error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
