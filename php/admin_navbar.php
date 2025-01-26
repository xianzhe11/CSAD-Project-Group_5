<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Burger Bliss Admin Dashboard</title>
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
            <li><a href="dashboard.php" class="active" title="Dashboard"><i class="fa-solid fa-tachometer-alt" aria-hidden="true"></i>Dashboard</a></li>
            <li><a href="orders.php" title="Orders"><i class="fa-solid fa-shopping-cart" aria-hidden="true"></i>Orders</a></li>
            <li><a href="menu.php" title="Menu"><i class="fa-solid fa-book-open" aria-hidden="true"></i>Menu</a></li>
            <li><a href="reviews.php" title="Reviews"><i class="fa-solid fa-comments" aria-hidden="true"></i>Reviews</a></li>
            <li><a href="reservation.php" title="Reservation"><i class="fa-solid fa-calendar-check" aria-hidden="true"></i>Reservation</a></li>
            <li><a href="settings.php" title="Settings"><i class="fa-solid fa-gear" aria-hidden="true"></i>Settings</a></li>
        </ul>

        <!-- Add Menu Box -->
        <div class="add-menu-box">
            <p>Organize your menu using the button below</p>
            <button onclick="window.location.href='add_menu.php'"><i class="fa-solid fa-plus"></i> Add Menu</button>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Burger Bliss Restaurant Dashboard</p>
            <p>&copy; 2025 All rights reserved</p>
        </div>
    </nav>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Your main dashboard content goes here -->
        <h2>Welcome to the Dashboard</h2>
        <p>Manage your restaurant efficiently using the navigation panel.</p>
        <!-- Add more dashboard widgets and content as needed -->
    </div>
</body>
</html>
