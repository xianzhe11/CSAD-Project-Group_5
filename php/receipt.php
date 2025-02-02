<?php
session_start();

// Include the database connection
require_once 'db_connection.php'; // Ensure this path is correct

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Retrieve and sanitize payment method
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : 'N/A';

    // Retrieve table number directly 
    $table_number = isset($_POST['table_number']) ? htmlspecialchars(trim($_POST['table_number'])) : 'N/A';
    $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : 'N/A';

    // Retrieve cart items from session
    $cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

    // Retrieve order type
    $order_type = isset($_POST['order_type']) ? trim($_POST['order_type']) : null;

    // Set table_number and address based on order_type
    if ($order_type === 'dine_in') {
        $table_number = isset($_POST['table_number']) && trim($_POST['table_number']) !== '' ? htmlspecialchars(trim($_POST['table_number'])) : NULL;
        $address = NULL;
    } elseif ($order_type === 'takeaway') {
        $address = isset($_POST['address']) && trim($_POST['address']) !== '' ? htmlspecialchars(trim($_POST['address'])) : NULL;
        $table_number = NULL;
    }

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
        $stmt = $conn->prepare("INSERT INTO `orders` (`order_id`, `user_id`, `payment_method`, `order_type`, `table_number`, `address`, `total_price`) VALUES (?, ?, ?, ?, ?, ?, ?)");

        if (!$stmt) {
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        // Assuming you have user authentication and a user ID
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // Bind parameters
        $stmt->bind_param(
            "sissssd",
            $order_id, // s: string
            $user_id,         // i: integer (nullable)
            $payment_method,  // s: string
            $order_type,      // s: string
            $table_number,    // s: string (nullable)
            $address,         // s: string (nullable)
            $total_price      // d: double
        );

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Get the last inserted order's ID
        $last_order_id = $conn->insert_id;

        // **Modify the SQL statement to include 'customizations'**
        // Prepare the SQL statement for inserting into 'order_items' table
        // Added 'customizations' field to the INSERT statement
        $stmt_item = $conn->prepare("INSERT INTO `order_items` (`order_id`, `item_name`, `quantity`, `price_each`, `total_price`, `customizations`) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt_item) {
            throw new Exception("Prepare statement for order_items failed: " . $conn->error);
        }

        // **Update the bind_param to match the number of parameters**
        // 's' for string (customizations)
        $stmt_item->bind_param(
            "isidds",
            $last_order_id,    // i: integer
            $item_name,        // s: string
            $quantity,         // i: integer
            $price_each,       // d: double
            $total_item_price, // d: double
            $customizations    // s: string (JSON)
        );

        // Bind and execute for each cart item
        foreach ($cart_items as $item) {
            $item_name = htmlspecialchars($item['name']);
            $quantity = (int)$item['quantity'];
            $price_each = number_format((float)$item['price'], 2, '.', '');
            $total_item_price = number_format($quantity * $item['price'], 2, '.', '');
            $customizations   = isset($item['customizations']) ? $item['customizations'] : '';

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
            'order_type'     => $order_type,
            'table_number'   => $table_number,
            'cart_items'     => $cart_items,
            'total_price'    => $total_price,
            'address'        => $address
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
    <link href="../css/receipt.css" rel='stylesheet'>

</head>
<body>
    <?php
    if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
      include 'navbar_loggedIn.php';
    } else {
      include 'navbar.php';
    }
    ?>

    <!-- Receipt Container -->
    <div class="receipt-container">
        <i class="fas fa-check-circle tick"></i> <!-- Tick Icon -->
        <div class="success-message">
            Your order was successfully placed!
        </div>

        <!-- Order Details Box -->
        <div class="order-details">
            <h4>Order Summary</h4>
            <p><strong>Order ID:</strong> <?php echo htmlspecialchars($_SESSION['order']['order_id']); ?></p>
            <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($_SESSION['order']['payment_method']); ?></p>
            <p><strong>Order Type:</strong> <?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($_SESSION['order']['order_type']))); ?></p>
            <?php if ($_SESSION['order']['order_type'] === 'dine_in' && !empty($_SESSION['order']['table_number'])): ?>
                <p><strong>Table Number:</strong> <?php echo htmlspecialchars($_SESSION['order']['table_number']); ?></p>
            <?php endif; ?>
            <?php if ($_SESSION['order']['order_type'] === 'takeaway' && !empty($_SESSION['order']['address'])): ?>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($_SESSION['order']['address']); ?></p>
            <?php endif; ?>

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
                            <?php
                                // Decode the customizations JSON
                                $customizations = json_decode($item['customizations'], true);
                                if (!empty($customizations)) {
                                    echo '<tr>';
                                    echo '<td colspan="4" class="customizations"><strong>Customizations:</strong>';
                                    echo '<ul>';
                                    foreach ($customizations as $key => $value) {
                                        echo '<li>' . htmlspecialchars($key) . ': ' . htmlspecialchars($value) . '</li>';
                                    }
                                    echo '</ul></td>';
                                    echo '</tr>';
                                }
                            ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p class="mt-3"><strong>Total Amount:</strong> SGD <?php echo number_format($_SESSION['order']['total_price'], 2); ?></p>
        </div>
    </div>

    <!-- Footer (Optional) -->
    <?php include 'footer.html'; ?>

    <!-- JavaScript -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
