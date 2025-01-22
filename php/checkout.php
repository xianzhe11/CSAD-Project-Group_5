<?php
session_start(); // Start the session to access cart data

// Retrieve cart items from session
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

// Calculate total price
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['quantity'] * $item['price'];
}

// Redirect to cart if no items are present
if (empty($cart_items)) {
    header('Location: cart.php');
    exit();
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Tags and Title -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    
    <!-- External Stylesheets -->
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="../css/progressBar.css" rel='stylesheet'>

    <!-- Inline CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        /* Checkout Container with 70-30% Ratio */
        .checkout-container {
            display: flex;
            flex-wrap: nowrap; /* Prevent wrapping */
            justify-content: space-between;
            align-items: stretch; /* Equal height */
            gap: 40px;
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        /* Payment Box Styling */
        .payment-box {
            background-color: #ffbda1; /* Updated Background Color */
            border: 1px solid #ddd;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            flex: 0 0 70%;
            max-width: 70%;
            display: flex;
            flex-direction: column;
            height: auto;
        }
        /* Cart Summary Styling */
        .cart-summary {
            background-color: #ffbda1; /* Updated Background Color */
            border: 1px solid #ddd;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            flex: 0 0 30%; /* 30% for Cart Summary */
            max-width: 30%;
            display: flex;
            flex-direction: column;
        }
        /* Payment Options Styling */
        .payment-option {
            margin-bottom: 20px;
            font-size: 18px;
        }
        .payment-option input {
            margin-right: 10px;
        }
        .google-pay-btn {
            background-color: #000;
            color: #fff;
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            margin: 20px 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .google-pay-btn i {
            margin-right: 10px;
        }
        .pay-button {
            background-color: #28a745;
            color: #fff;
            width: 100%;
            border: none;
            border-radius: 20px;
            padding: 15px;
            cursor: pointer;
            font-size: 20px;
            margin: 20px 0;
            display: block;
            text-align: center;
        }
        /* Cart Summary Styling */
        .cart-summary h4 {
            margin-bottom: 20px;
        }
        .cart-summary ul {
            list-style: none;
            padding: 0;
            flex-grow: 1;
            overflow-y: auto;
            max-height: 300px; /* Limit height to accommodate 3 items */
        }
        .cart-summary li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }
        .cart-summary li:last-child {
            border-bottom: none;
        }
        .cart-summary li img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            margin-right: 15px;
        }
        .cart-summary .item-details {
            flex-grow: 1;
        }
        .cart-summary .item-details strong {
            display: block;
            font-size: 16px;
            margin-bottom: 5px;
        }
        .cart-summary .item-details span {
            display: block;
            font-size: 14px;
            color: #555;
        }
        .cart-summary .total-amount {
            font-weight: bold;
            font-size: 20px;
            margin-top: 20px;
            text-align: left; /* Align to the left */
        }
        /* Order Type Styling */
        .order-type {
            margin-top: 20px;
        }
        .order-type label {
            margin-right: 20px;
            font-size: 18px;
        }
        .order-type input {
            margin-right: 10px;
        }
        /* Credit Card Details Styling */
        .credit-card-details {
            margin-top: 20px;
            display: none; /* Hidden by default */
        }
        .credit-card-details input {
            margin-bottom: 15px;
        }
        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .checkout-container {
                flex-direction: column;
                padding: 20px;
            }
            .payment-box, .cart-summary {
                max-width: 100%;
                flex: 0 0 100%;
            }
            .google-pay-btn, .pay-button {
                width: 100%;
            }
            .cart-summary ul {
                max-height: none; /* Remove max-height on smaller screens */
            }
        }
    </style>
</head>
<body>

    <?php include 'navbar.php'; ?>
    <?php $current_step = 2; include 'progressbar.php'; ?>
    <?php include 'footer.html'; ?>

    <!-- Checkout Container Wrapped Inside Form -->
    <form action="receipt.php" method="POST" id="paymentForm">
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="checkout-container">
            <!-- Payment Options Column -->
            <div class="payment-box">
                <h4 class="mb-4">Choose Payment Method</h4>

                <!-- Payment Options -->
                <div class="payment-option">
                    <input type="radio" id="creditCard" name="payment_method" value="credit_card" required>
                    <label for="creditCard">Credit Card</label>
                </div>

                <!-- Credit Card Details (Inside Form) -->
                <div class="credit-card-details" id="creditCardDetails">
                    <input type="text" name="card_number" class="form-control" placeholder="Credit Card Number">
                    <input type="text" name="ccv" class="form-control" placeholder="CCV">
                    <input type="text" name="expiry_date" class="form-control" placeholder="Expiry Date (MM/YY)">
                    <input type="text" name="cardholder_name" class="form-control" placeholder="Cardholder Name">
                </div>

                <div class="payment-option">
                    <input type="radio" id="grabPay" name="payment_method" value="grabpay">
                    <label for="grabPay">GrabPay</label>
                </div>
                <div class="payment-option">
                    <input type="radio" id="paypal" name="payment_method" value="paypal">
                    <label for="paypal">PayPal</label>
                </div>
                <div class="payment-option">
                    <input type="radio" id="paynow" name="payment_method" value="paynow">
                    <label for="paynow">PayNow</label>
                </div>
            
                <!-- Google Pay Button -->
                <button type="button" class="google-pay-btn">
                    <i class="fab fa-google-pay"></i> Google Pay
                </button>
                
                <!-- Pay Button -->
                <button type="submit" class="pay-button">
                    Pay SGD <?php echo number_format($total_price, 2); ?>
                </button>
            </div>

            <!-- Cart Summary Column -->
            <div class="cart-summary">
                <h4>Cart Summary</h4>
                <ul>
                    <?php foreach ($cart_items as $item): ?>
                        <li>
                            <img src="../images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            <div class="item-details">
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                <span>Quantity: <?php echo $item['quantity']; ?></span>
                                <span>Price each: SGD <?php echo number_format($item['price'], 2); ?></span>
                                <span>Total: SGD <?php echo number_format($item['quantity'] * $item['price'], 2); ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Order Type Selection (Inside Form) -->
                <div class="order-type">
                    <label>
                        <input type="radio" name="order_type" value="dine_in" checked> Dine In
                    </label>
                    <label>
                        <input type="radio" name="order_type" value="takeaway"> Takeaway
                    </label>
                </div>

                <!-- Dine In Input -->
                <div id="dineInOptions" class="mt-3">
                    <label for="tableNumber">Table Number:</label>
                    <input type="text" id="tableNumber" name="table_number" class="form-control" placeholder="Enter table number (e.g. 47A)" required>
                </div>

                <!-- Takeaway Input -->
                <div id="takeawayOptions" class="mt-3" style="display: none;">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" class="form-control" placeholder="Enter Full Address">
                </div>

                <!-- Total Amount -->
                <div class="total-amount">
                    Total Order Amount: <br><strong>SGD <?php echo number_format($total_price, 2); ?></strong>
                </div>
            </div>
        </div>
    </form>

    <!-- Footer -->
    <?php include 'footer.html'; ?>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script>
        $(document).ready(function(){
            // Toggle between Dine In and Takeaway options
            $('input[name="order_type"]').change(function(){
                if($(this).val() === 'dine_in'){
                    $('#dineInOptions').show();
                    $('#dineInOptions input').attr('required', true);
                    $('#takeawayOptions').hide();
                    $('#takeawayOptions input').removeAttr('required');
                }
                else{
                    $('#dineInOptions').hide();
                    $('#dineInOptions input').removeAttr('required');
                    $('#takeawayOptions').show();
                    $('#takeawayOptions input').attr('required', true);
                }
            });

            // Toggle Credit Card Details and Handle 'required' Attributes
            $('input[name="payment_method"]').change(function(){
                if($(this).val() === 'credit_card'){
                    $('#creditCardDetails').show();
                    // Add 'required' to credit card fields
                    $('#creditCardDetails input').attr('required', true);
                }
                else{
                    $('#creditCardDetails').hide();
                    // Remove 'required' from credit card fields
                    $('#creditCardDetails input').removeAttr('required');
                }
            });

            // Trigger change to set initial state
            $('input[name="payment_method"]:checked').trigger('change');
        });
    </script>
</body>
</html>
