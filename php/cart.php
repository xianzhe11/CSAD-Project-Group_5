<?php
session_start();
$current_step=1;
// Handle quantity updates and item deletions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'], $_POST['item_id'])) {
        $action = $_POST['action'];
        $itemId = $_POST['item_id'];

        // Ensure the cart exists
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $index => &$item) {
                if ($item['id'] == $itemId) {
                    if ($action === 'increase') {
                        $item['quantity'] += 1;
                    } elseif ($action === 'decrease') {
                        $item['quantity'] -= 1;
                        if ($item['quantity'] <= 0) {
                            // Remove item from cart if quantity is zero or less
                            array_splice($_SESSION['cart'], $index, 1);
                        }
                    } elseif ($action === 'delete') {
                        // Remove the item from the cart
                        array_splice($_SESSION['cart'], $index, 1);
                    }
                    break;
                }
            }
        }

        // Redirect to avoid form resubmission
        header('Location: cart.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- (Head content remains unchanged) -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="../css/progressBar.css" rel='stylesheet'>
    <link href="../css/cart.css" rel='stylesheet'>
</head>
<body>

    <?php include 'navbar.php'; ?>
    <?php include 'progressBar.php'; ?>

    <div class="cart-container">
        <!--<h2 class="text-center mb-4">Your Cart</h2>-->

        <?php
        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0):
            $total = 0;
            foreach ($_SESSION['cart'] as $item):
                $itemTotal = $item['price'] * $item['quantity'];
                $total += $itemTotal;
        ?>
            <div class="cart-item">
                <div class="price-section">
                    <div class="price">SGD <?= number_format($item['price'], 2) ?></div>
                </div>
                <div class="details">
                    <img src="../images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    <div class="info">
                        <div class="name"><?= htmlspecialchars($item['name']) ?></div>
                        <div class="description"><?= htmlspecialchars($item['description']) ?></div>
                    </div>
                </div>
                <div class="quantity-section">
                    <div class="quantity-controls">
                        <!-- Decrease Quantity Form -->
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="decrease">
                            <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                            <button type="submit"><i class="fas fa-minus"></i></button>
                        </form>
                        <span><?= $item['quantity'] ?></span>
                        <!-- Increase Quantity Form -->
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="increase">
                            <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                            <button type="submit"><i class="fas fa-plus"></i></button>
                        </form>
                    </div>
                </div>
                <!-- Delete Button -->
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                    <button type="submit" class="delete-button" title="Remove Item">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </div>
        <?php
            endforeach;
        else:
        ?>
            <p class="text-center">Your cart is empty.</p>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            <div class="pay-button-container">
                <form action="checkout.php" method="GET">
                    <button type="submit" class="pay-button">
                        <div class="total-price">
                            Pay<br> SGD <?= number_format($total, 2) ?> 
                        </div>
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.html'; ?>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js'></script>
    <script>
        // Optional: Handle Pay button click if using JavaScript
        /*
        document.querySelector('.pay-button').addEventListener('click', function() {
            // Redirect to checkout.php
            window.location.href = 'checkout.php';
        });
        */
    </script>
</body>
</html>
