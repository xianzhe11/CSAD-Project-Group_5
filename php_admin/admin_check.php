<?php
session_start();
include 'db_connection.php';

$sql = "SELECT COUNT(*) AS count FROM orders WHERE total_price = 0.00";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["error" => "Query failed", "message" => $conn->error]);
    exit;
}

$row = $result->fetch_assoc();
$newOrderCount = $row['count'];

$previousOrderCount = isset($_SESSION['order_count']) ? $_SESSION['order_count'] : 0;

$_SESSION['order_count'] = $newOrderCount;

$response = ["new_orders" => $newOrderCount > $previousOrderCount];

echo json_encode($response);
exit;
?>
