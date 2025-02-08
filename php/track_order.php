<?php
session_start();
include 'db_connection.php';
if (!isset($_SESSION['orderlog'])) {
    $_SESSION['orderlog'] = []; // Initialize an empty array to store orders
}

// Check if the 'order' session variable is set
if (isset($_SESSION['order'])) {
    // Get the order information from the session
    $order = $_SESSION['order'];
    $ordercodeExist = false;
    foreach ($_SESSION['orderlog'] as $existingOrder) {
        if ($existingOrder['order_id'] == $order['order_id']) {
            $ordercodeExist = true;
            break;
        }
    }
    if (!$ordercodeExist) {
        $_SESSION['orderlog'][] = $order;
    }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>  
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/trackorder.css">
<title>Track Order</title>
</head>
<body>

<?php

include 'db_connection.php';
if (isset($_SESSION['userloggedin'])) {
    include 'navbar_loggedIn.php';
} else {
    include 'navbar.php';
} 
$sql = "SELECT order_id, order_status FROM orders";
$result = $conn->query($sql);

// Store the order statuses in an associative array for easier lookup
$orderStatuses = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $orderStatuses[$row['order_id']] = $row['order_status'];
    }
}?>



<?php
// Check if there are orders stored in the session
if (!empty($_SESSION['orderlog'])) {
    $orderlogReversed = array_reverse($_SESSION['orderlog']); // Reverse the order of the array?>
    <img src = "../images/cover.png" class = "cover"></img>
    <span class = "label">Your Orders :</span>
<?php foreach ($orderlogReversed as $orders) {
        $orderStatus = isset($orderStatuses[$orders['order_id']]) ? $orderStatuses[$orders['order_id']] : ''; ?>
 
       <div class="order-container">
            <h2 class = "orderno">Order Number: <?php echo $orders['order_id']; ?></h2>
            <button class="view-order" data-orderid="<?php echo $orders['order_id']; ?>">Order Details</button>
            <img src="<?php echo $orders['order_type'] === 'takeaway' ? '../images/takeaway.png' : '../images/dinein.png'; ?>
            " class="contimg"></img>
            <div class="<?php 
                                    switch($orderStatus) {
                                        case 'Pending':
                                            echo 'status-pending';
                                            break;
                                        case 'Preparing':
                                            echo 'status-preparing';
                                            break;
                                        case 'Delivering':
                                            echo 'status-delivering';
                                            break;
                                        case 'Completed':
                                            echo 'status-completed';
                                            break;
                                        case 'Cancelled':
                                            echo 'status-cancelled';
                                            break;
                                        default:
                                            echo '';
                                    }
                                ?>">
                                    <?php echo htmlspecialchars($orderStatus ?? ''); ?>
                                </div>
        </div>
    <?php
    }
    ?>
</div>
<div class="popup" id="order-popup">
    <h2 id ="order-label"></h2>
    <div id="order-details"></div>
    <button id="close-popup">X</button>
</div>
<?php
} else {?>
    <div class = "noinfo"> No order information available..</div>
<?php
}?>

</body>

</html>

<script>

document.addEventListener('DOMContentLoaded', function() {
    const viewOrderButtons = document.querySelectorAll('.view-order');
    const orderPopup = document.getElementById('order-popup');
    const orderDetails = document.getElementById('order-details');
    const orderlabel = document.getElementById('order-label');

    viewOrderButtons.forEach(button => {
        button.addEventListener('click', function() {
            const orderID = this.getAttribute('data-orderid');
            const order = <?php echo json_encode($_SESSION['orderlog']); ?>;
            const selectedOrder = order.find(order => order.order_id == orderID);
            orderlabel.innerText = "Order Details";
            orderDetails.innerHTML = `
                <p><strong>Order ID:</strong> ${selectedOrder.order_id}</p>
                <p><strong>Payment Method:</strong> ${selectedOrder.payment_method}</p>
                <p><strong>Order Type:</strong> ${selectedOrder.order_type}</p>
                <p><strong>Table Number:</strong> ${selectedOrder.table_number}</p>
                <p><strong>Total Price:</strong> ${selectedOrder.total_price}</p>
                <p><strong>Address:</strong> ${selectedOrder.address}</p>
            `;
            orderPopup.style.display = 'block';
        });
    });

    document.getElementById('close-popup').addEventListener('click', function() {
        orderPopup.style.display = 'none';
    });
});
</script>








