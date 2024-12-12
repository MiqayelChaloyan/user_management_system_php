<?php
include '../includes/db.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = $_GET['id'];

    $deleteUserQuery = "DELETE FROM `users` WHERE `id` = ?";
    $deleteUserDetailsQuery = "DELETE FROM `user_details` WHERE `user_id` = ?";

    $conn->begin_transaction();

    try {
        if ($stmt = $conn->prepare($deleteUserQuery)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();
        }

        if ($stmtDetails = $conn->prepare($deleteUserDetailsQuery)) {
            $stmtDetails->bind_param("i", $userId);
            $stmtDetails->execute();
            $stmtDetails->close();
        }

        $conn->commit();

        echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete user.']);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user ID']);
}
?>
