<?php
// admin_order_details.php
session_start();

// Include database connection
include 'db_connection.php'; // Ensure this file contains your DB credentials

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    die("Order ID not specified.");
}

$order_id = intval($_GET['order_id']);

// Fetch order details
$order_sql = "SELECT orders.*, users.username, users.phone, users.profile_picture FROM orders 
             LEFT JOIN users ON orders.user_id = users.id 
             WHERE orders.id = ?";
$stmt = $conn->prepare($order_sql);
if ($stmt === false) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    die("Order not found.");
}

$order = $order_result->fetch_assoc();

// Fetch order items
$items_sql = "SELECT * FROM order_items WHERE order_id = ?";
$stmt_items = $conn->prepare($items_sql);
if ($stmt_items === false) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();

// Define progress steps
$progress_steps = [
    1 => 'Order Created',
    2 => 'In Kitchen',
    3 => 'On Delivery',
    4 => 'Order Delivered'
];

// Map order status to progress steps
$status_mapping = [
    'Pending'     => 1, // Order Created
    'Preparing'   => 2, // In Kitchen
    'Delivering'  => 3, // On Delivery
    'Completed'   => 4, // Order Delivered
    'Cancelled'   => 0  // No progress
];

$current_step = isset($status_mapping[$order['order_status']]) ? $status_mapping[$order['order_status']] : 0;

// Fetch timestamps for each step (Assuming these fields exist in your orders table)
$step_dates = [
    1 => $order['order_created_at'] ?? null,
    2 => $order['in_kitchen_at'] ?? null,
    3 => $order['on_delivery_at'] ?? null,
    4 => $order['order_delivered_at'] ?? null
];

// Determine Order Type Display
$order_type_display = ucfirst($order['order_type'] ?? '');

// Close statements and connection
$stmt->close();
$stmt_items->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details - Admin</title>
    <link href="https://fonts.googleapis.com/css?family=Lobster|Roboto:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="../css/admin_navbar.css">
    <link rel="stylesheet" href="../css/admin_order_details.css">
    <!-- Include Bootstrap CSS for better styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include "admin_navbar.php"?>

    <!-- Main Content Area -->
    <div class="main-content container-fluid">
        <div class="top-bar">
            <a href="admin_orders.php" class="breadcrumb-link"><i class="fas fa-arrow-left"></i> Back</a>
            <div>
                <span class="breadcrumb-link text-muted">Orders / </span>
                <span class="breadcrumb-link">Order Details</span>
            </div>
            <div>
                <h5> ID #<?php echo htmlspecialchars($order['order_id'] ?? ''); ?></h5>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="progressbar">
            <?php foreach ($progress_steps as $step_number => $label): ?>
                <div class="step <?php echo ($current_step >= $step_number) ? 'active' : ''; ?>">
                    <div class="circle"><?php echo $step_number; ?></div>
                    <div class="label"><?php echo $label; ?></div>
                    <?php if ($current_step >= $step_number && $step_dates[$step_number]): ?>
                        <div class="date"><?php echo date("M d, Y H:i", strtotime($step_dates[$step_number])); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Order Summary -->
        <div class="order-summary">
            <h5 style="margin-left: 20px;">Order Information</h5>
            <div class="row" style="margin-left: 20px;">
                <div class="col-md-6 mb-3">
                    <p><strong>User:</strong> <?php echo !empty($order['username']) ? htmlspecialchars($order['username']) : 'Guest'; ?></p>
                    <p><strong>Order Type:</strong> <?php echo ($order['order_type'] === 'dine_in') ? 'Dine-In' : 'Takeaway'; ?></p>
                    <?php if ($order['order_type'] === 'dine_in' && !empty($order['table_number'])): ?>
                        <p><strong>Table Number:</strong> <?php echo htmlspecialchars($order['table_number']); ?></p>
                    <?php elseif ($order['order_type'] === 'takeaway' && !empty($order['address'])): ?>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars(ucfirst($order['payment_method'] ?? '')); ?></p>
                    <p><strong>Total Price:</strong> $<?php echo number_format($order['total_price'] ?? 0, 2); ?></p>
                    <p><strong>Order Status:</strong> <?php echo htmlspecialchars($order['order_status'] ?? ''); ?></p>
                    <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($order['payment_status'] ?? ''); ?></p>
                </div>
            </div>
        </div>

        <!-- Ordered Items -->
        <div class="order-summary mt-4">
            <h5>Ordered Items</h5>
            <div class="order-items">
                <!-- Header Row -->
                <div class="order-item header-row">
                    <div>Item Name</div>
                    <div>Quantity</div>
                    <div>Price Each</div>
                    <div>Total Price</div>
                </div>
                <!-- Items -->
                <?php if ($items_result->num_rows > 0): ?>
                    <?php while($item = $items_result->fetch_assoc()): ?>
                        <div class="order-item">
                            <div>
                                <?php 
                                    echo htmlspecialchars($item['item_name'] ?? ''); 
                                    // Display customizations if any
                                    $customizations = json_decode($item['customizations'], true);
                                    if ($customizations && is_array($customizations)) {
                                        echo "<br><small class='text-muted'>";
                                        foreach ($customizations as $key => $value) {
                                            echo htmlspecialchars($key) . ": " . htmlspecialchars($value) . "<br>";
                                        }
                                        echo "</small>";
                                    }
                                ?>
                            </div>
                            <div><?php echo htmlspecialchars($item['quantity'] ?? ''); ?></div>
                            <div>$<?php echo number_format($item['price_each'] ?? 0, 2); ?></div>
                            <div>$<?php echo number_format($item['total_price'] ?? 0, 2); ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center">No items found for this order.</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- User Information Panel -->
        <div class="user-info-panel">
            <div class="user-info-left">
                <?php
                    // Determine if user is a guest
                    $is_guest = empty($order['username']);
                    // Set profile picture
                    $profile_picture = $is_guest ? '../images/empty_pfp.png' : ( !empty($order['profile_picture']) ? htmlspecialchars($order['profile_picture']) : '../images/empty_pfp.png' );
                ?>
                <img src="<?php echo $profile_picture; ?>" alt="">
                <div class="user-details">
                    <span class="username"><?php echo $is_guest ? 'Guest' : htmlspecialchars($order['username']); ?></span>
                    <?php if (!$is_guest || $is_guest): ?>
                        <span class="customer-tag">Customer</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="user-info-right">
                <?php if ($order['order_type'] === 'dine_in'): ?>
                    <div class="phone-box"> 
                        <i class="fas fa-utensils"></i> Table: <?php echo htmlspecialchars($order['table_number']); ?>
                    </div>
                <?php elseif ($order['order_type'] === 'takeaway'): ?>
                    <div class="phone-box"> 
                        <i class="fas fa-map-marker-alt"></i> Address: <?php echo htmlspecialchars($order['address']); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($order['phone'])): ?>
                    <div class="phone-box">
                        <i class="fas fa-phone-alt"></i> Telephone: <?php echo htmlspecialchars($order['phone']); ?>
                    </div>
                <?php else: ?>
                    <div class="phone-box">
                        <i class="fas fa-phone-alt"></i> Telephone: N/A
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
