<?php
session_start();
include 'db_connection.php';
$_SESSION['prev_page'] = $_SERVER['REQUEST_URI'];
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
if (isset($_SESSION['userloggedin'])) {
    include 'navbar_loggedIn.php';
} else {
    include 'navbar.php';
}
?>
<?php

if (isset($_SESSION['userloggedin']) && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    
    $sql = "SELECT id,order_id, order_status, order_type, payment_method, table_number, total_price, address FROM orders WHERE user_id = $user_id";
    $result = $conn->query($sql);

    if ($result) {
        $orders = []; 
        $items = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                
                $orders[] = $row;
            }
            
           
            $orders = array_reverse($orders);
            $jsonOrders= json_encode($orders);

            $dineInCount = 0;
            $takeawayCount = 0;

            
            foreach ($orders as $count) {
                if ($count['order_type'] == 'dine_in') {
                    $dineInCount++;
                } elseif ($count['order_type'] == 'takeaway') {
                    $takeawayCount++;
                }
            }
            ?>
            <img src = "../images/cover.png" class = "cover"></img>
            <span class = "label">Your Orders :</span>
            <?php
            foreach ($orders as $info) {
                $sql ="SELECT order_id,item_name,quantity,price_each, total_price FROM order_items WHERE order_id = " . $info['id'];
                $result = $conn->query($sql);
                while ($rows = $result->fetch_assoc()) {
                    
                    $items[] = $rows;
                    
                }
                
?>
                <div class="order-container">
                    <h2 class="orderno">Order Number: <?php echo $info['order_id']; ?></h2>
                    <button class="view-order" data-orderid="<?php echo $info['order_id']; ?>"
                     data-id="<?php echo $info['id']; ?>">Order Details</button>

                    <img src="<?php echo $info['order_type'] === 'takeaway' ? '../images/takeaway.png' : '../images/dinein.png'; ?>" class="contimg"></img>
                    <div class="<?php
                        switch ($info['order_status']) {
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
                        <?php echo htmlspecialchars($info['order_status']); ?>
                    </div>
                </div>
<?php
            }   $jsonItems= json_encode($items);
             ?>
            <div class="popup" id="order-popup">
                <h2 id ="order-label"></h2>
                <div id="order-details">
                    <h2>Thank You For Your Support!</h2></br>
                    <span>Total number of orders : <?php echo count($orders); ?><span> </br>
                    <span>Your Dine Ins: <?php echo ($dineInCount); ?><span> </br>
                    <span>Your Takeaways : <?php echo ($takeawayCount); ?><span> </br>
                </div>
                <button id="close-popup">X</button>
            </div>
        <?php
        } else {
            
            echo '<div class="noinfo">No order information available.</div>';
        }
    } else {
        echo "Query Error" . $conn->error;
    }
} else {
    echo '<div class="noinfo">User is not logged in. Please log in to continue.</div>';
}
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewOrderButtons = document.querySelectorAll('.view-order');
    const orderPopup = document.getElementById('order-popup');
    const orderDetails = document.getElementById('order-details');
    const orderLabel = document.getElementById('order-label');
    const closePopupButton = document.getElementById('close-popup');

    var jsonOrders = '<?php echo $jsonOrders; ?>';
    var jsonItems = '<?php echo $jsonItems; ?>';
    var Ordersarray = JSON.parse(jsonOrders);
    var Itemsarray = JSON.parse(jsonItems);

    viewOrderButtons.forEach(button => {
    button.addEventListener('click', function() {
        const orderID = this.getAttribute('data-orderid');
        const id = this.getAttribute('data-id');

        const info = Ordersarray.find(order => order.order_id == orderID);
        const itemsInfo = Itemsarray.filter(item => item.order_id == id);

        if (info) {
            orderLabel.innerText = "Order Details";
            orderDetails.innerHTML = `
                <p><strong>Order ID:</strong> ${info.order_id}</p>
                <p><strong>Payment Method:</strong> ${info.payment_method}</p>
                <p><strong>Order Type:</strong> ${info.order_type}</p>
                <p><strong>Table Number:</strong> ${info.table_number}</p>
                <p><strong>Total Price:</strong> ${info.total_price}</p>
                <p><strong>Address:</strong> ${info.address}</p>
                <h3>Order Items:</h3>
            `;

            itemsInfo.forEach(item => {
                orderDetails.innerHTML += `
                <table>
                <tr>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Price Each</th>
                    <th>Total Price</th>
                </tr>
                <tr>
                    <td>${item.item_name}</td>
                    <td>${item.quantity}</td>
                    <td>${item.price_each}</td>
                    <td>${item.total_price}</td>
                </tr>
                </table>
             `;
            });

            orderPopup.style.display = 'block';
        } else {
            console.error('Order not found');
        }
    });
});

    closePopupButton.addEventListener('click', function() {
        orderPopup.style.display = 'none';
    });
});

</script>

</body>
</html>