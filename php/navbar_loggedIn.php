<?php



$currentPage = basename($_SERVER['PHP_SELF']);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$totalItems = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $totalItems += $item['quantity'];
    }
}

$userPicture = (isset($_SESSION['user']['profile_picture']) && !empty($_SESSION['user']['profile_picture']))
    ? $_SESSION['user']['profile_picture']
    : "../images/empty_pfp.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Custom Navbar - Logged In</title>
  <link rel="stylesheet" href="../css/navbar.css">
  
  <link href="https://fonts.googleapis.com/css?family=Lobster|Poppins:400,500&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  
  <style>
    .user-dropdown {
      position: relative;
      display: inline-block;
      cursor: pointer;
    }
    .user-dropdown img.user-pfp {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #fff;
      margin-left: 20px;
    }

    .user-dropdown .dropdown-content {
      position: absolute;
      top: calc(100%); 
      transform: translateX(55%) translateY(-10px); 
      right: 50%;
      background-color: var(--dropdown-bg);
      min-width: 200px;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      visibility: hidden;
      opacity: 0.5;
      transition: opacity var(--transition-speed) ease, transform var(--transition-speed) ease;
      z-index: 1000;
      font-family: var(--font-primary);
      font-weight: 500;
      font-size: 18px;
    }

    .user-dropdown:hover .dropdown-content {
      opacity: 1;
      visibility: visible;
      transform: translateX(55%);
    }

    .user-dropdown .dropdown-content a {
      white-space: nowrap;
      display: block;
      padding: 12px 16px;
      color: var(--text-color);
      text-decoration: none;
      transition: background-color var(--transition-speed);
    }
    .user-dropdown .dropdown-content a i {
      margin-right: 10px;
    }

    .user-dropdown .dropdown-content a:hover {
      background-color: var(--navbar-hover);
      color: #fff;
    }
  </style>
</head>
<body>
  <nav class="custom-navbar">
    <div class="navbar-container">
      
      <a href="<?php echo ($currentPage === 'index.php') ? '#home' : 'index.php#home'; ?>" class="brand">Burger Bliss</a>

      
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
            <li><a href="menu.php?category=seasonal"><i class="fas fa-snowflake"></i></i> Seasonal</a></li>
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

      
      <div class="right-section">
        <a href="cart.php" class="cart-icon" title="Cart" data-count="<?= htmlspecialchars($totalItems) ?>">
          <i class="fas fa-shopping-cart"></i>
        </a>

        
        <div class="user-dropdown">
          <img src="<?= htmlspecialchars($userPicture) ?>" alt="Profile Picture" class="user-pfp">
          <div class="dropdown-content">
            <a href="track_order.php"><i class="fas fa-truck"></i> Track Order</a>
            <a href="points.php"><i class="fas fa-gift"></i> Reward System</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <script src="../script/navScript.js"></script>
</body>
</html>

