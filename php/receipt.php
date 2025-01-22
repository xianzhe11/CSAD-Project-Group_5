<?php
session_start();

// Include the database connection
require_once 'db_connection.php'; // Ensure this path is correct

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validate CSRF Token (Optional but Recommended)
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo 'Invalid CSRF token.';
        exit();
    }

    // Retrieve and sanitize payment method
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : 'N/A';

    // Retrieve table number directly (since order_type is not used)
    $table_number = isset($_POST['table_number']) ? htmlspecialchars(trim($_POST['table_number'])) : 'N/A';

    // Retrieve cart items from session
    $cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

    // Check if cart is not empty
    if (empty($cart_items)) {
        header('Location: cart.php');
        exit();
    }

    // Calculate total price
    $total_price = 0;
    foreach ($cart_items as $item) {
        $total_price += $item['quantity'] * $item['price'];
    }

    // Generate a unique order ID (e.g., using timestamp and random number)
    $order_id = 'ORD' . time() . rand(1000, 9999);

    // Begin Transaction
    $conn->begin_transaction();

    try {
        // Prepare the SQL statement for inserting into 'orders' table
        $stmt = $conn->prepare("INSERT INTO `orders` (`order_id`, `user_id`, `payment_method`, `table_number`, `total_price`) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        // Assuming you have user authentication and a user ID
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // Bind parameters
        $stmt->bind_param(
            "sissd",
            $order_id,       // s: string
            $user_id,        // i: integer (nullable)
            $payment_method, // s: string
            $table_number,   // s: string (nullable)
            $total_price     // d: double
        );

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Get the last inserted order's ID
        $last_order_id = $conn->insert_id;

        // Prepare the SQL statement for inserting into 'order_items' table
        $stmt_item = $conn->prepare("INSERT INTO `order_items` (`order_id`, `item_name`, `quantity`, `price_each`, `total_price`) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt_item) {
            throw new Exception("Prepare statement for order_items failed: " . $conn->error);
        }

        // Bind parameters for order_items
        foreach ($cart_items as $item) {
            $item_name = htmlspecialchars($item['name']);
            $quantity = (int)$item['quantity'];
            $price_each = number_format((float)$item['price'], 2, '.', '');
            $total_item_price = number_format($quantity * $item['price'], 2, '.', '');

            $stmt_item->bind_param(
                "isidd",
                $last_order_id,    // i: integer
                $item_name,        // s: string
                $quantity,         // i: integer
                $price_each,       // d: double
                $total_item_price  // d: double
            );

            // Execute the statement
            if (!$stmt_item->execute()) {
                throw new Exception("Execute failed for order_items: " . $stmt_item->error);
            }
        }

        // Commit the transaction
        $conn->commit();

        // Store order details in session for display
        $_SESSION['order'] = [
            'order_id'       => $order_id,
            'payment_method' => $payment_method,
            'table_number'   => $table_number,
            'cart_items'     => $cart_items,
            'total_price'    => $total_price
        ];

        // Clear the cart
        unset($_SESSION['cart']);
    } catch (Exception $e) {
        // Rollback the transaction if something failed
        $conn->rollback();
        echo 'Failed to place the order: ' . $e->getMessage();
        exit();
    }

} else {
    // If accessed directly without POST data, redirect to checkout
    header('Location: checkout.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags and Title -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    
    <!-- External Stylesheets -->
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Inline CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .receipt-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 40px 20px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .tick {
            color: #28a745;
            font-size: 160px;
            margin-bottom: 20px;
        }
        .success-message {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 40px;
        }
        .order-details {
            border: 2px solid #F05D5F;
            border-radius: 10px;
            padding: 20px;
            text-align: left;
        }
        .order-details h4 {
            margin-bottom: 20px;
            text-align: center;
            color: #F05D5F;
        }
        .order-details p {
            margin: 10px 0;
        }
        .order-items {
            margin-top: 20px;
        }
        .order-items table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-items th, .order-items td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .order-items th {
            background-color: #F05D5F;
            color: white;
        }
        @media (max-width: 576px) {
            .tick {
                font-size: 100px;
            }
            .success-message {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Navbar (Optional) -->
    <?php include 'navbar.php'; ?>

    <!-- Receipt Container -->
    <div class="receipt-container">
        <!-- Big Tick Icon -->
        <i class="fas fa-check-circle tick"></i>

        <!-- Success Message -->
        <div class="success-message">
            Your order was successfully placed!
        </div>

        <!-- Order Details Box -->
        <div class="order-details">
            <h4>Order Summary</h4>
            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($_SESSION['order']['order_id']); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($_SESSION['order']['payment_method']); ?></p>
            
            <div class="order-items">
                <strong>Order Items:</strong>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price Each (SGD)</th>
                            <th>Total (SGD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['order']['cart_items'] as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo (int)$item['quantity']; ?></td>
                                <td><?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p class="mt-3"><strong>Total Amount:</strong> SGD <?php echo number_format($_SESSION['order']['total_price'], 2); ?></p>

            <p><strong>Table Number:</strong> <?php echo htmlspecialchars($_SESSION['order']['table_number']); ?></p>
        </div>
    </div>

    <!-- Footer (Optional) -->
    <?php include 'footer.html'; ?>

    <!-- JavaScript -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
