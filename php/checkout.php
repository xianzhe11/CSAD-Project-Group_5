<?php
session_start(); 

$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['quantity'] * $item['price'];
}

if (empty($cart_items)) {      
    header('Location: cart.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="../css/progressBar.css" rel='stylesheet'>
    <link href="../css/checkout.css" rel='stylesheet'>

</head>
<body>
    <?php
    if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
      include 'navbar_loggedIn.php';
    } else {
      include 'navbar.php';
    }
    ?>

    <?php $current_step = 2; include 'progressbar.php'; ?>

    <form action="receipt.php" method="POST" id="paymentForm">
        <div class="checkout-container">

            <div class="payment-box">
                <h4 class="mb-4">Choose Payment Method</h4>

                <div class="payment-option">
                    <input type="radio" id="creditCard" name="payment_method" value="credit_card" required>
                    <label for="creditCard">Credit Card</label>
                    <img src="../images/mastercard.png" alt="mastercard" style="width:40px; height:40px; margin-left: 63%;">
                    <img src="../images/visa.png" alt="visa" style="width:40px; height:40px;">
                </div>
                <div class="credit-card-details" id="creditCardDetails">
                    <input type="text" name="card_number" class="form-control" placeholder="Credit Card Number">
                    <input type="text" name="ccv" class="form-control" placeholder="CCV">
                    <input type="text" name="expiry_date" class="form-control" placeholder="Expiry Date (MM/YY)">
                    <input type="text" name="cardholder_name" class="form-control" placeholder="Cardholder Name">
                </div>

                <div class="payment-option">
                    <input type="radio" id="grabPay" name="payment_method" value="grabpay">
                    <label for="grabPay">GrabPay</label>
                    <img src="../images/grabpay.png" alt="GrabPay" style="width:100px; height:40px; margin-left: 66%;">
                </div>

                <div class="payment-option">
                    <input type="radio" id="paypal" name="payment_method" value="paypal">
                    <label for="paypal"></i>PayPal</label>
                    <img src="../images/paypal.png" alt="paypal" style="width:40px; height:40px; margin-left: 72%;">
                </div>

                <div class="payment-option">
                    <input type="radio" id="paynow" name="payment_method" value="paynow">
                    <label for="paynow">PayNow</label>
                    <img src="../images/paynow.png" alt="PayNow" style="width:100px; height:60px; margin-left: 67%;">
                </div>

                <button type="button" class="google-pay-btn">
                    <i class="fab fa-google-pay"></i> Google Pay
                </button>

                <button type="submit" class="pay-button">
                    Pay SGD <?php echo number_format($total_price, 2); ?>
                </button>
            </div>

            <div class="cart-summary">
                <h4>Cart Summary</h4>
                <ul>
                    <?php foreach ($cart_items as $item): ?>
                        <li>
                            <img src="../food_images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                            <div class="item-details">
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                <span>Quantity: <?php echo $item['quantity']; ?></span>
                                <span>Price each: SGD <?php echo number_format($item['price'], 2); ?></span>
                                <span>Total: SGD <?php echo number_format($item['quantity'] * $item['price'], 2); ?></span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <div class="order-type">
                    <label>
                        <input type="radio" name="order_type" value="dine_in" checked> Dine In
                    </label>
                    <label>
                        <input type="radio" name="order_type" value="takeaway"> Takeaway
                    </label>
                </div>

                <div id="dineInOptions" class="mt-3">
                    <label for="tableNumber">Table Number:</label>
                    <input type="text" id="tableNumber" name="table_number" class="form-control" placeholder="Enter table number (e.g. 47A)" required>
                </div>
                <div id="takeawayOptions" class="mt-3" style="display: none;">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" class="form-control" placeholder="Enter Full Address">
                </div>

                <div class="total-amount">
                    Total Order Amount: <br><strong>SGD <?php echo number_format($total_price, 2); ?></strong>
                </div>
            </div>
        </div>
    </form>

    <?php include 'footer.html'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script>
        $(document).ready(function(){
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

            $('input[name="payment_method"]').change(function(){
                if($(this).val() === 'credit_card'){
                    $('#creditCardDetails').show();
                    $('#creditCardDetails input').attr('required', true);
                }
                else{
                    $('#creditCardDetails').hide();
                    $('#creditCardDetails input').removeAttr('required');
                }
            });
            $('input[name="payment_method"]:checked').trigger('change');                    // Trigger change to set initial state
        });
    </script>
</body>
</html>
