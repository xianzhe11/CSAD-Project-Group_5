<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['pid']) && isset($_POST['email'])) {
        $pid = intval($_POST['pid']);
        $pname = $conn->real_escape_string($_POST['pname']);
        $pprice = floatval($_POST['pprice']);
        $pqty = intval($_POST['pqty']);
        $pimage = $conn->real_escape_string($_POST['pimage']);
        $pcode = $conn->real_escape_string($_POST['pcode']);
        $email = $conn->real_escape_string($_POST['email']);

        // Check if user already has this item in cart
        $checkQuery = "SELECT * FROM cart WHERE user_email='$email' AND product_id='$pid'";
        $checkResult = $conn->query($checkQuery);

        if ($checkResult && $checkResult->num_rows > 0) {
            // Update quantity if item already exists
            $updateQuery = "UPDATE cart SET quantity = quantity + $pqty WHERE user_email='$email' AND product_id='$pid'";
            if ($conn->query($updateQuery) === TRUE) {
                echo "<div class='alert alert-success'>Item quantity updated in the cart!</div>";
            } else {
                echo "<div class='alert alert-danger'>Failed to update item in the cart.</div>";
            }
        } else {
            // Insert new item into cart
            $insertQuery = "INSERT INTO cart (user_email, product_id, product_name, product_price, quantity, product_image, product_code) VALUES ('$email', '$pid', '$pname', '$pprice', '$pqty', '$pimage', '$pcode')";
            if ($conn->query($insertQuery) === TRUE) {
                echo "<div class='alert alert-success'>Item added to cart successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Failed to add item to the cart.</div>";
            }
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['cartItem']) && $_GET['cartItem'] == 'cart_item') {
        $email = isset($_SESSION['email']) ? $conn->real_escape_string($_SESSION['email']) : '';
        if ($email) {
            // Fetch cart item count
            $countQuery = "SELECT COUNT(*) as count FROM cart WHERE user_email='$email'";
            $countResult = $conn->query($countQuery);
            if ($countResult) {
                $countRow = $countResult->fetch_assoc();
                echo $countRow['count'];
            } else {
                echo "0";
            }
        } else {
            echo "0";
        }
    }
}
?>
