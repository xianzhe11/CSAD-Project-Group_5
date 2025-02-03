<?php

// Get the current page's filename
$currentPage = basename($_SERVER['PHP_SELF']);


if (session_status() === PHP_SESSION_NONE) { // Start the session if it hasn't been started already
  session_start();
}

$totalItems = 0;

if (isset($_SESSION['cart'])) {
  //Sum the quantities of each item 
  foreach ($_SESSION['cart'] as $item) {
      $totalItems += $item['quantity'];
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Meta tags and other head elements -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css?family=Lobster|Poppins:400,500&display=swap" rel="stylesheet" />
  <title>Custom Navbar</title>
  <link rel="stylesheet" href="../css/navbar.css">
  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>

  <nav class="custom-navbar">
    <div class="navbar-container">
      <!-- Brand -->
      <a href="<?php echo ($currentPage === 'index.php') ? '#home' : 'index.php#home'; ?>" class="brand">Burger Bliss</a>

      <!-- Navigation Menu -->
      <ul class="nav-links">
        <li>
          <a href="<?php echo ($currentPage === 'index.php') ? '#home' : 'index.php#home'; ?>" class="<?php echo ($currentPage === 'index.php') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> Home
          </a>
        </li>
        <li class="dropdown">
          <a href="<?php echo ($currentPage === 'index.php') ? '#menu' : 'menu.php'; ?>" class="<?php echo ($currentPage === 'menu.php') ? 'active' : ''; ?>">
            <i class="fas fa-utensils"></i> Our Food <i class="fas fa-caret-down"></i>
          </a>
          <ul class="dropdown-menu">
            <li><a href="menu.php?category=appetizer"><i class="fas fa-pepper-hot"></i> Appetizers</a></li>
            <li><a href="menu.php?category=pizza"><i class="fas fa-pizza-slice"></i> Pizza</a></li>
            <li><a href="menu.php?category=burgers"><i class="fas fa-hamburger"></i> Burgers</a></li>
            <li><a href="menu.php?category=beverages"><i class="fas fa-coffee"></i> Beverages</a></li>
          </ul>
        </li>
        <li>
          <a href="<?php echo ($currentPage === 'index.php') ? '#reservation' : 'index.php#reservation'; ?>" class="<?php echo ($currentPage === 'index.php#reservation') ? 'active' : ''; ?>">
            <i class="fas fa-calendar-alt"></i> Reservation
          </a>
        </li>
        <li>
          <a href="<?php echo ($currentPage === 'index.php') ? '#about-us' : 'index.php#about-us'; ?>" class="<?php echo ($currentPage === 'index.php#about-us') ? 'active' : ''; ?>">
            <i class="fas fa-info-circle"></i> About Us
          </a>
        </li>
        <li>
          <a href="<?php echo ($currentPage === 'index.php') ? '#contact' : 'index.php#contact'; ?>" class="<?php echo ($currentPage === 'index.php#contact') ? 'active' : ''; ?>">
            <i class="fas fa-envelope"></i> Contact
          </a>
        </li>
        <li>
          <a href="review.php" class="<?php echo ($currentPage === 'review.php') ? 'active' : ''; ?>">
            <i class="fas fa-star"></i> Review
          </a>
        </li>
      </ul>

      <!-- (cart + login button) -->
      <div class="right-section">
        <a href="cart.php" class="cart-icon" title="Cart" data-count="<?= htmlspecialchars($totalItems) ?>"><i class="fas fa-shopping-cart"></i></a>
        <a href="login.php" class="login-btn">Login</a>
      </div>
    </div>
  </nav>

  <script src="../script/navScript.js"></script>
</body>
</html>
