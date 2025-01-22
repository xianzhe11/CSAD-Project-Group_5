<?php
session_start();
include 'db_connection.php';

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $itemId = $_POST['item_id'];
    $itemName = $_POST['item_name'];
    $itemDescription = $_POST['item_description'];
    $itemPrice = $_POST['item_price'];
    $itemImage = $_POST['item_image'];

    // Check if item already exists in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] === $itemId) {
            $item['quantity'] += 1;
            $found = true;
            break;
        }
    }
    if (!$found) {
        // Add new item to cart
        $_SESSION['cart'][] = [
            'id' => $itemId,
            'name' => $itemName,
            'description' => $itemDescription,
            'price' => $itemPrice,
            'image' => $itemImage,
            'quantity' => 1
        ];
    }

    // Optionally, you can redirect to cart.php or stay on the same page
    // header('Location: cart.php');
    // exit();
}

// Fetch all categories from the database
$categoryQuery = 'SELECT catName FROM menu_categories';
$categoryResult = $conn->query($categoryQuery);

$categories = [];
if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row['catName'];
    }
} else {
    echo "<!-- Query Failed: (" . $conn->errno . ") " . $conn->error . " -->";
}

// Retrieve and sanitize the selected category from the URL
$selectedCategory = isset($_GET['category']) ? strtolower(trim($_GET['category'])) : strtolower($categories[0]);

// Validate the selected category
$validCategories = array_map('strtolower', $categories);
if (!in_array($selectedCategory, $validCategories)) {
    $selectedCategory = strtolower($categories[0]); // Default to the first category
}

// Find the index of the selected category to set the active tab
$activeIndex = array_search($selectedCategory, $validCategories);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/menu.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="heading">
        <div class="heading-title">Restaurant Menu</div>
    </div>

    <div class="category-nav">
        <ul class="nav nav-tabs justify-content-center" id="categoryTab" role="tablist">
            <?php foreach ($categories as $index => $category): ?>
                <?php $categorySlug = strtolower($category);$isActive = ($index === $activeIndex) ? 'active' : '';?>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive ?>" id="<?= $categorySlug ?>-tab" data-toggle="tab" href="#<?= $categorySlug ?>" role="tab" aria-controls="<?= $categorySlug ?>" aria-selected="<?= ($isActive === 'active') ? 'true' : 'false'; ?>">
                        <?= htmlspecialchars($category) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="container menu-section">
        <div class="tab-content" id="categoryTabContent">
            <?php foreach ($categories as $index => $category): ?>
                <?php 
                    $categorySlug = strtolower($category);$isActive = ($categorySlug === $selectedCategory) ? 'show active' : '';
                    ?>
                <div class="tab-pane fade <?= $isActive ?>" id="<?= $categorySlug ?>" role="tabpanel" aria-labelledby="<?= $categorySlug ?>-tab">
                    <div class="row menu-items">
                        <?php
                        // Prepare and execute the statement
                        $stmt = $conn->prepare('SELECT * FROM menu_items WHERE catName = ?');
                        $stmt->bind_param('s', $category);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        ?>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <div class="col-lg-4 col-md-6 col-sm-12 menu-item">
                                    <div class="menu-card">
                                        <img src="../images/<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['itemName']) ?>">
                                        <div class="card-body">
                                            <h4 class="card-title text-center mt-3"> <?= htmlspecialchars($row['itemName']) ?> </h4>
                                            <p class="description"> <?= htmlspecialchars($row['description']) ?> </p>
                                            <?php if ($row['status'] == 'Unavailable'): ?>
                                                <p class="card-status">Unavailable</p>
                                            <?php endif; ?>
                                            <div class="button-container">
                                                <p class="card-text">SGD&nbsp;<?= number_format($row['price'], 2) ?>/-</p>
                                                <form method="GET" action="order_customise.php" class="d-inline">
                                                    <input type="hidden" name="add_to_cart" value="1">
                                                    <input type="hidden" name="item_id" value="<?= htmlspecialchars($row['id']) ?>">
                                                    <input type="hidden" name="item_name" value="<?= htmlspecialchars($row['itemName']) ?>">
                                                    <input type="hidden" name="item_description" value="<?= htmlspecialchars($row['description']) ?>">
                                                    <input type="hidden" name="item_price" value="<?= htmlspecialchars($row['price']) ?>">
                                                    <input type="hidden" name="item_image" value="<?= htmlspecialchars($row['image']) ?>">
                                                    <button type="submit" class="addItemBtn">
                                                        <i class="fas fa-cart-plus"></i> Add to cart
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <p class="text-center">No items available in this category.</p>
                            </div>
                        <?php endif; ?>
                        <?php 
                            $stmt->close(); 
                            $result->free();
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js'></script>
    
    <?php include 'footer.html'; ?>
</body>
</html>
