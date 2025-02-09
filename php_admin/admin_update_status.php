<?php

include 'db_connection.php';
session_start();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $order_status = $_POST['order_status'];
    $payment_status = $_POST['payment_status'];


    $valid_order_statuses = ['Pending', 'Preparing', 'Delivering', 'Completed', 'Cancelled'];
    $valid_payment_statuses = ['Unpaid', 'Paid', 'Refunded'];

    if (!in_array($order_status, $valid_order_statuses) || !in_array($payment_status, $valid_payment_statuses)) {
        echo "Invalid status values.";
        exit();
    }


    $update_sql = "UPDATE orders SET order_status = ?, payment_status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $order_status, $payment_status, $order_id);

    if ($stmt->execute()) {
        echo "Order status updated successfully.";
    } else {
        echo "Failed to update order status.";
    }
}
?>
