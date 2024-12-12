<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $country = trim($_POST['country']);

    $query = "INSERT INTO `users` (`full_name`, `email`, `phone_number`, `country`) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Database prepare failed: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssss", $full_name, $email, $phone_number, $country);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User created successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error creating user: ' . $stmt->error]);
    }

    $stmt->close();
}
?>
