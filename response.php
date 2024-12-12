<?php
include 'includes/db.php';

$requestData = $_REQUEST;

// Define the columns for sorting
$columns = array(
    0 => 'id',
    1 => 'full_name',
    2 => 'email',
    3 => 'phone_number',
    4 => 'country'
);

// TODO: Add error handling for database connection
// Query for fetching users data with optional search functionality
$sql = "SELECT users.id, users.full_name, users.email, users.phone_number, users.country, 
               user_details.age, user_details.gender 
        FROM users 
        LEFT JOIN user_details ON users.id = user_details.user_id";

// Execute the initial query to get total data
$query = mysqli_query($conn, $sql);

// TODO: Handle query failure gracefully (e.g., check if $query is false)
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;

// Search filter functionality
$searchKeyWord = htmlspecialchars($requestData['search']['value']);
if (!empty($searchKeyWord)) {
    // Modify query to include search criteria
    $sql .= " WHERE users.full_name LIKE '".$searchKeyWord."%' 
              OR users.email LIKE '".$searchKeyWord."%' 
              OR users.phone_number LIKE '".$searchKeyWord."%' 
              OR users.country LIKE '".$searchKeyWord."%'";
    
    // TODO: Consider using prepared statements to prevent SQL injection
    $query = mysqli_query($conn, $sql);
    $totalFiltered = mysqli_num_rows($query);
}

// Sorting and pagination functionality
$sql .= " ORDER BY ".$columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']." 
          LIMIT ".$requestData['start']." ,".$requestData['length'];

// TODO: Ensure the LIMIT clause works as expected, and check if pagination is functioning correctly
$query = mysqli_query($conn, $sql);

$data = array();
while ($row = mysqli_fetch_array($query)) {
    // Prepare data to return
    $data[] = array(
        'id' => $row['id'],
        'full_name' => $row['full_name'],
        'email' => $row['email'],
        'phone' => $row['phone_number'],
        'country' => $row['country'],
        'age' => !empty($row['age']) ? $row['age'] : 'N/A', // TODO: Consider if 'N/A' is appropriate default for age
        'gender' => !empty($row['gender']) ? $row['gender'] : 'N/A' // TODO: Handle gender more gracefully if null
    );
}

// Prepare the JSON response with data
$json_data = array(
    "draw" => intval($requestData['draw']),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
);

// TODO: Log the response data for debugging purposes, especially if no data is found

echo json_encode($json_data); // Return JSON data
?>
