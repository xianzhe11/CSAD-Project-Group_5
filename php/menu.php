<?php
session_start();
include 'db_connection.php';

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
        <!--<div class="heading-description">~Discover a feast of flavors with our exciting menu!</div>-->
    </div>
    <!--<hr class="gray_line">-->

    <div class="category-nav">
        <ul class="nav nav-tabs justify-content-center" id="categoryTab" role="tablist">
            <?php foreach ($categories as $index => $category): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" id="<?= strtolower($category) ?>-tab" data-toggle="tab" href="#<?= strtolower($category) ?>" role="tab">
                        <?= htmlspecialchars($category) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="container menu-section">
        <div class="tab-content" id="categoryTabContent">
            <?php foreach ($categories as $index => $category): ?>
                <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>" id="<?= strtolower($category) ?>" role="tabpanel">
                    <div class="row menu-items">
                        <?php
                        $stmt = $conn->prepare('SELECT * FROM menu_items WHERE catName = ?');
                        $stmt->bind_param('s', $category);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="col-lg-4 col-md-6 col-sm-12 menu-item">
                                <div class="menu-card">
                                    <img src="../images/<?= $row['image'] ?>" alt="<?= $row['itemName'] ?>">
                                    <div class="card-body">
                                        <h4 class="card-title text-center mt-3"> <?= $row['itemName'] ?> </h4>
                                        <p class="description"> <?= $row['description'] ?> </p>
                                        <?php if ($row['status'] == 'Unavailable'): ?>
                                            <p class="card-status">Unavailable</p>
                                        <?php endif; ?>
                                        <div class="button-container">
                                            <p class="card-text">SGD&nbsp;<?= number_format($row['price'], 2) ?>/-</p>
                                            <button class="addItemBtn <?= $row['status'] == 'Unavailable' ? 'disabled-button' : ''; ?>">
                                                <i class="fas fa-cart-plus"></i> Add to cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js'></script>
    <script src="../script/menu.js"></script>

</body>
</html>
