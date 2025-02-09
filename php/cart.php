<?php
session_start();
$current_step = 1;

function isUserLoggedIn() {
    return isset($_SESSION['userloggedin']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'], $_POST['item_id'])) {
        $action = $_POST['action'];
        $itemId = $_POST['item_id'];

        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $index => &$item) {
                if ($item['id'] == $itemId) {
                    if ($action === 'increase') {
                        $item['quantity'] += 1;
                    } elseif ($action === 'decrease') {
                        $item['quantity'] -= 1;
                        if ($item['quantity'] <= 0) {            
                            array_splice($_SESSION['cart'], $index, 1);  
                        }
                    } elseif ($action === 'delete') {
                        array_splice($_SESSION['cart'], $index, 1);
                    }
                    break;
                }
            }
        }
        header('Location: cart.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
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
    <?php
    if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
      include 'navbar_loggedIn.php';
    } else {
      include 'navbar.php';
    }
    ?>
    <?php include 'progressBar.php'; ?>
    <?php $_SESSION['prev_page'] = $_SERVER['REQUEST_URI'];?>

    <div class="container cart-container">
        <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
            <?php
                $total = 0;
                foreach ($_SESSION['cart'] as $item):
                    $itemTotal = $item['price'] * $item['quantity'];
                    $total += $itemTotal; ?>
                <div class="cart-item">
                    <div class="price-section">
                        <div class="price">SGD <?= number_format($item['price'], 2) ?></div>
                    </div>
                    <div class="details">
                        <img src="../food_images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="info">
                            <div class="info-left">
                                <div class="name"><?= htmlspecialchars($item['name']) ?></div>
                                <div class="description">
                                    <?= htmlspecialchars($item['description']) ?>
                                </div>
                            </div>
                            <?php if (!empty($item['customizations'])): ?>
                                <?php $customizations = json_decode($item['customizations'], true);?>
                                <div class="info-right">
                                    <div class="customizations">
                                        <strong>Customizations:</strong>
                                        <ul>
                                            <?php foreach ($customizations as $key => $value): ?>
                                                <li><?= htmlspecialchars($key) . ': ' . htmlspecialchars($value) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (floatval($item['price']) > 0): ?>
                    <div class="quantity-section">
                        <div class="quantity-controls">

                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="decrease">
                                <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                                <button type="submit" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </form>
                            <span class="mx-2"><?= $item['quantity'] ?></span>

                            <form method="POST" class="d-inline">
                                <input type="hidden" name="action" value="increase">
                                <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                                <button type="submit" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <form method="POST" class="delete-form">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['id']) ?>">
                        <button type="submit" class="delete-button" title="Remove Item">    
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                    <?php else: ?>
                        <div class="quantity-section">
                            <span class="mx-2"><Strong>Quantity: </Strong> <?= $item['quantity'] ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="pay-button-container">
                <?php if (isUserLoggedIn()): ?>
                    <form action="checkout.php" method="GET">
                        <button type="submit" class="pay-button">
                            Checkout &nbsp;<i class="fas fa-shopping-cart"></i>
                        </button>
                    </form>

                <?php else: ?>
                    <button type="button" class="pay-button" data-toggle="modal" data-target="#loginPromptModal">
                        Checkout &nbsp;<i class="fas fa-shopping-cart"></i>
                    </button>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="text-center">Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="loginPromptModal" tabindex="-1" aria-labelledby="loginPromptModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            
          <div class="modal-header">
            <h5 class="modal-title" id="loginPromptModalLabel">Proceed to Checkout</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <p>You need to sign in to your account or continue as a guest to proceed with the checkout.</p>
          </div>
          <div class="modal-footer">
            <a href="login.php" class="btn btn-primary" style="transform: translateX(-80%);">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </a>
            <a href="checkout.php?guest=true" class="btn btn-success" style="transform: translateX(-40%);">
                <i class="fas fa-user-secret"></i> Checkout as Guest
            </a>
          </div>
        </div>
      </div>
    </div>

    <?php include 'footer.html'; ?>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js'></script>
</body>
</html>
