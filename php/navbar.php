<?php

// Get the current page's filename
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Meta tags and other head elements -->
  <link href="https://fonts.googleapis.com/css?family=Lobster&display=swap" rel="stylesheet" />
  <title>Custom Navbar</title>
  <link rel="stylesheet" href="../css/navbar.css">
</head>
<body>

  <nav class="custom-navbar">
    <div class="navbar-container">
      <!-- Brand -->
      <a href="<?php echo ($currentPage === 'index.php') ? '#home' : 'index.php#home'; ?>" class="brand">Burger Bliss</a>

      <!-- Navigation Menu -->
      <ul class="nav-links">
        <li>
          <a href="<?php echo ($currentPage === 'index.php') ? '#home' : 'index.php#home'; ?>">Home</a>
        </li>
        <li class="dropdown">
          <a href="<?php echo ($currentPage === 'index.php') ? '#menu' : 'menu.php'; ?>">Our Food</a>
          <ul class="dropdown-menu">
          <li><a href="menu.php?category=appetizer">Appetizers</a></li>
          <li><a href="menu.php?category=pizza">Pizza</a></li>
          <li><a href="menu.php?category=burgers">Burger</a></li>
          <li><a href="menu.php?category=beverages">Beverages</a></li>
          </ul>
        </li>
        <li><a href="<?php echo ($currentPage === 'index.php') ? '#Reservation' : 'index.php#Reservation'; ?>">Reservation</a></li>
        <li><a href="<?php echo ($currentPage === 'index.php') ? '#about-us' : 'index.php#about-us'; ?>">About Us</a></li>
        <li><a href="<?php echo ($currentPage === 'index.php') ? '#contact' : 'index.php#contact'; ?>">Contact</a></li>
      </ul>

      <!-- Cart and Login -->
      <div class="right-section">
        <a href="cart.php" class="cart-icon">🛒</a>
        <a href="login.php" class="login-btn">Login</a>
      </div>

      <!-- Mobile Menu Toggle (optional) -->
      <!-- <button class="menu-toggle">☰</button> -->
    </div>
  </nav>

  <script src="../script/navScript.js"></script>
</body>
</html>
