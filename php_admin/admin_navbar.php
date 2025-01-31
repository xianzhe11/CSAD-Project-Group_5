<?php

$current_page = basename($_SERVER['SCRIPT_NAME'], ".php");  // Determine the current page name
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Navbar</title>
    <link href="https://fonts.googleapis.com/css?family=Lobster|Roboto:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="../css/admin_navbar.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <!-- Sidebar Navigation -->
    <nav class="sidebar">
        <div class="logo">
            <a href="index.php">Bliss Burger</a>
        </div>

        <!-- Navigation Items -->
        <ul class="nav-items">
            <li><a href="admin_dashboard.php" class="<?php echo ($current_page == 'admin_dashboard') ? 'active' : ''; ?>" title="Dashboard"><i class="fa-solid fa-tachometer-alt" aria-hidden="true"></i>Dashboard</a></li>
            <li><a href="admin_orders.php" class="<?php echo ($current_page == 'admin_orders') ? 'active' : ''; ?>" title="Orders"><i class="fa-solid fa-shopping-cart" aria-hidden="true"></i>Orders</a></li>
            <li><a href="admin_menu.php" class="<?php echo ($current_page == 'admin_menu') ? 'active' : ''; ?>" title="Menu"><i class="fa-solid fa-book-open" aria-hidden="true"></i>Menu</a></li>
            <li><a href="admin_reviews.php" class="<?php echo ($current_page == 'admin_reviews') ? 'active' : ''; ?>" title="Reviews"><i class="fa-solid fa-comments" aria-hidden="true"></i>Reviews</a></li>
            <li><a href="admin_reservation.php"  class="<?php echo ($current_page == 'admin_reservation') ? 'active' : ''; ?>"title="Reservation"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i>Reservation</a></li>
            <li><a href="admin_settings.php" class="<?php echo ($current_page == 'admin_settings') ? 'active' : ''; ?>" title="Settings"><i class="fa-solid fa-gear" aria-hidden="true"></i>Settings</a></li>
        </ul>

        <!-- Add Menu Box -->
        <div class="add-menu-box">
            <p>Organize your menu using the button below</p>
            <button onclick="window.location.href='admin_menu.php'"><i class="fa-solid fa-plus"></i> Add Menu</button>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Burger Bliss Restaurant Dashboard</p>
            <p>&copy; 2025 All rights reserved</p>
        </div>
    </nav>
</body>
</html>
