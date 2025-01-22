<?php
session_start();
include 'db_connection.php';

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if (!isset($_GET['item_id'])) {
    // If item_id is missing, redirect to menu or show error
    header('Location: menu.php');
    exit;
}

$item_id = (int) $_GET['item_id'];

// 2) Fetch item details from the DB
$sql = "SELECT * FROM menu_items WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    // If no item found, redirect or show error
    header('Location: menu.php');
    exit;
}
$stmt->close();

// 3) Handle form submission to add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_add_to_cart'])) {
    // Ensure cart session array is initialized
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Grab and sanitize posted customizations
    $custom_extra_cheese = isset($_POST['extra_cheese']) ? 'Yes' : 'No';
    $custom_no_onion     = isset($_POST['no_onion'])     ? 'Yes' : 'No';
    $custom_spice_level  = sanitize_input($_POST['spice_level']) ?? 'Normal';

    // You can add more customization options here

    // Build customization array (can be stored as JSON)
    $customizations = [
        'Extra Cheese' => $custom_extra_cheese,
        'No Onion'     => $custom_no_onion,
        'Spice Level'  => $custom_spice_level
    ];

    // Encode customizations as JSON
    $customization_json = json_encode($customizations);

    // Check if item with the same customizations already exists in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$cartItem) {
        // We'll consider it "the same" item if same ID + same customizations
        if ($cartItem['id'] == $item_id && $cartItem['customizations'] == $customization_json) {
            $cartItem['quantity'] += 1;
            $found = true;
            break;
        }
    }
    if (!$found) {
        // Add a new array entry
        $_SESSION['cart'][] = [
            'id'            => $item['id'],
            'name'          => $item['itemName'],
            'description'   => $item['description'],
            'price'         => $item['price'],
            'image'         => $item['image'],
            'quantity'      => 1,
            'customizations'=> $customization_json,
        ];
    }

    // After adding to cart, redirect to menu.php
    header('Location: menu.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customize Your Order</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/order_customize.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .customize-container {
            padding: 40px 0;
        }
        .customize-card {
            background-color: #ffffff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .item-image {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }
        .customization-section {
            margin-top: 20px;
        }
        .customization-option {
            margin-bottom: 15px;
        }
        .btn-customize {
            background-color: #28a745;
            color: #ffffff;
            border-radius: 30px;
            padding: 10px 20px;
            transition: background-color 0.3s;
        }
        .btn-customize:hover {
            background-color: #218838;
            color: #ffffff;
        }
        .btn-cancel {
            background-color: #dc3545;
            color: #ffffff;
            border-radius: 30px;
            padding: 10px 20px;
            transition: background-color 0.3s;
        }
        .btn-cancel:hover {
            background-color: #c82333;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container customize-container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="customize-card">
                    <div class="row">
                        <!-- Item Image -->
                        <div class="col-md-5">
                            <img src="../images/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['itemName']) ?>" class="item-image">
                        </div>
                        <!-- Item Details & Customization Form -->
                        <div class="col-md-7">
                            <h2 class="mb-3"><?= htmlspecialchars($item['itemName']) ?></h2>
                            <p class="text-muted"><?= htmlspecialchars($item['description']) ?></p>
                            <h4 class="mb-4">Price: SGD <?= number_format($item['price'], 2) ?></h4>

                            <form method="POST">
                                <!-- Extra Cheese -->
                                <div class="customization-section">
                                    <h5>Customize Your Meal</h5>
                                    <div class="customization-option form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="extra_cheese" name="extra_cheese" value="1">
                                        <label class="form-check-label" for="extra_cheese">Extra Cheese</label>
                                    </div>

                                    <!-- No Onion -->
                                    <div class="customization-option form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="no_onion" name="no_onion" value="1">
                                        <label class="form-check-label" for="no_onion">No Onion</label>
                                    </div>

                                    <!-- Spice Level -->
                                    <div class="customization-option form-group">
                                        <label for="spice_level">Spice Level:</label>
                                        <select class="form-control" id="spice_level" name="spice_level" required>
                                            <option value="Mild">Mild</option>
                                            <option value="Normal" selected>Normal</option>
                                            <option value="Hot">Hot</option>
                                            <option value="Extra Hot">Extra Hot</option>
                                        </select>
                                    </div>

                                    <!-- Add more customization options here as needed -->
                                </div>

                                <!-- Submit Buttons -->
                                <div class="mt-4">
                                    <button type="submit" name="confirm_add_to_cart" class="btn btn-customize mr-2">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                    <a href="menu.php" class="btn btn-cancel">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.html'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js'></script>
</body>
</html>
