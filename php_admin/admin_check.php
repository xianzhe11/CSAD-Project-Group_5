<?php
session_start();
include 'db_connection.php';

// Query to count total orders with total_price = 0.00
$sql = "SELECT COUNT(*) AS count FROM orders WHERE total_price = 0.00";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["error" => "Query failed", "message" => $conn->error]);
    exit;
}

$row = $result->fetch_assoc();
$newOrderCount = $row['count'];

// Get previous order count from session
$previousOrderCount = isset($_SESSION['order_count']) ? $_SESSION['order_count'] : 0;

// Update session with new count
$_SESSION['order_count'] = $newOrderCount;

// Check if a new order was added
$response = ["new_orders" => $newOrderCount > $previousOrderCount];

echo json_encode($response);
exit;
?>
