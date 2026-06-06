<?php
// ============================================================
//  interest.php  –  AJAX Handler for "Mark Interest"
//  Called by JavaScript fetch() from property-detail.php
//  Returns JSON response
// ============================================================
session_start();
include 'db.php';

// Set response type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'login_required']);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$user_id     = (int)$_SESSION['user_id'];
$property_id = isset($_POST['property_id']) ? (int)$_POST['property_id'] : 0;

if ($property_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid property']);
    exit;
}

// Check if already marked interested
$check = mysqli_query($conn,
    "SELECT * FROM interested_users
     WHERE user_id = $user_id AND property_id = $property_id"
);

if (mysqli_num_rows($check) > 0) {
    // Already interested → REMOVE it (toggle off)
    mysqli_query($conn,
        "DELETE FROM interested_users
         WHERE user_id = $user_id AND property_id = $property_id"
    );
    echo json_encode(['status' => 'removed']);
} else {
    // Not interested yet → ADD it (toggle on)
    mysqli_query($conn,
        "INSERT INTO interested_users (user_id, property_id)
         VALUES ($user_id, $property_id)"
    );
    echo json_encode(['status' => 'added']);
}

mysqli_close($conn);
?>
