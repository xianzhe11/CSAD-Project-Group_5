<?php
session_start();
include 'db_connection.php';

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css?family=Lobster&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <title>Home</title>
  <link rel="stylesheet" href="../css/index.css">
  
</head>
<body>
  <?php
  /*if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
    include 'nav-logged.php';
  } else {
    include 'navbar.php';
  }*/
  include 'navbar.php';
  ?>

<div class="main" id="home">
  <section>
    <div class="container">
      <div class="row">
        <!-- Text Container -->
        <div class="text-container">
          <h2>Welcome to <span class="highlight">Burger Bliss</span></h2>
          <h4 class="sub-heading">"Where Hot Flavors Meet Cool Comfort."</h4>
          <p>
            Dive into a culinary celebration where every dish bursts with flavor.
            At <strong>Burger Bliss</strong>, we believe in making every meal an unforgettable experience.
          </p>
          <div class="button-group">
            <a href="menu.php" class="btn">Start Order</a>
            <a href="menu.php" class="btn btn-secondary">Explore Menu</a>
          </div>
        </div>
        <!-- Image Container -->
        <div class="image-container">
          <img src="../images/burger.png" alt="Delicious Pizza">
        </div>
      </div>
    </div>
  </section>
</div>

  <!-- Menu Section -->
  <section class="menu-section" id="menu">
    <div class="container">
      <h1 class="section-title lobster"> OUR <span>FOOD</span></h1>
      <div class="menu-items">
        <div class="menu-card" style="background-image: url('../images/appe-index.avif');">
          <h3>Appetizer</h3>
          <p>Start your meal with our delicious appetizers.</p>
          <a href="menu.php#appetizer" class="btn">Explore Variety</a>
        </div>
        <div class="menu-card" style="background-image: url('../images/index-pizza.jpg');">
          <h3>Pizza</h3>
          <p>Indulge in our wide variety of pizzas.</p>
          <a href="menu.php#pizza" class="btn">Explore Variety</a>
        </div>
        <div class="menu-card" style="background-image: url('../images/index-burger.avif');">
          <h3>Burger</h3>
          <p>Indulge in our wide variety of Burgers.</p>
          <a href="menu.php#pizza" class="btn">Explore Variety</a>
        </div>
        <div class="menu-card" style="background-image: url('../images/bev-index.jpeg');">
          <h3>Beverages</h3>
          <p>Quench your Thirst with our refreshing Beverages</p>
          <a href="menu.php#pizza" class="btn">Explore Variety</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Promotional Section -->
  <section class="info_section paralax">
  <div class="container text-center">
    <h2 class="heading_space">Seasonal Promotions!</h2>
    <p class="EnjoyFood">Enjoy Delicious Food in Limited time</p>
    <a href="menu.php" class="btn-common-white page-scroll">Order Now</a>
  </div>
</section>

<!-- About Us Section -->
<section class="aboutus-section" id="about-us">
  <div class="container">
    <h1 class="section-title lobster">ABOUT <span>US</span></h1>
    <div class="aboutus-content">
      <div class="aboutus-text">
        <div class="text-overlay">
          <p>At <strong>Burger Bliss</strong>, we believe that a great burger is more than just a meal—it’s an experience. Our passion for crafting the perfect burger drives us to bring together fresh ingredients, bold flavors, and creative recipes that tantalize your taste buds and leave you craving more. Whether you're a classic burger lover or an adventurous foodie, our menu is designed to satisfy every craving.</p>
          <p>Founded in <strong>2020, Burger Bliss</strong> has quickly become a favorite destination for burger enthusiasts. Our commitment to quality and taste means we source the finest ingredients, ensuring every bite is packed with mouthwatering goodness. From our signature juicy patties to our freshly baked buns and handcrafted sauces, we take pride in delivering an unforgettable dining experience.</p>
          <p>Looking for something delicious? <strong>Burger Bliss</strong> offers a welcoming ambiance and a menu full of delightful surprises. Come and indulge in the blissful joy of burgers with us—where every bite is a moment of happiness.</p>
        </div>
      </div>
      <div class="aboutus-image">
        <img src="../images/appe-index.avif" alt="Crafting Memorable Meals">
      </div>
    </div>
  </div>
</section>

<!-- Facts Counter Section -->
<section id="facts">
  <div class="container">
    <div class="row number-counters"> 
      <!-- First Count Item -->
      <div class="col-sm-3 col-xs-12 text-center wow fadeInDown" data-wow-duration="500ms" data-wow-delay="300ms">
        <div class="counters-item">
          <i class="fas fa-smile"></i> 
          <h2><strong data-to="4680">4680</strong></h2>
          <p>Happy Customers</p>
        </div>
      </div>
      <!-- Second Count Item -->
      <div class="col-sm-3 col-xs-12 text-center wow fadeInDown" data-wow-duration="500ms" data-wow-delay="600ms">
        <div class="counters-item"> 
          <i class="fas fa-utensils"></i>
          <h2><strong data-to="865">865</strong></h2>
          <p>Dishes Served</p>
        </div>
      </div>
      <!-- Third Count Item -->
      <div class="col-sm-3 col-xs-12 text-center wow fadeInDown" data-wow-duration="500ms" data-wow-delay="900ms">
        <div class="counters-item"> 
          <i class="fas fa-glass-whiskey"></i>
          <h2><strong data-to="510">510</strong></h2>
          <p>Total Beverages</p>
        </div>
      </div>
      <!-- Fourth Count Item -->
      <div class="col-sm-3 col-xs-12 text-center wow fadeInDown" data-wow-duration="500ms" data-wow-delay="1200ms">
        <div class="counters-item"> 
          <i class="fas fa-coffee"></i>
          <h2><strong data-to="1350">1350</strong></h2>
          <p>Cups of Coffee</p>
        </div>
      </div>
    </div>  
  </div>
</section>

  <!-- Footer -->
  <footer id="contact">
    <div class="footer-container">
      <div class="footer-row">
        <div class="footer-col">
          <h4>Contact Us</h4>
          <p>Dover Road 123, Singapore</p>
          <p>Email: info@BurgerBliss.com</p>
          <p>Phone: +65 8888 8888</p>
        </div>
        <div class="footer-col">
          <h4>Follow Us</h4>
          <div class="social-icons">
            <a href="https://www.instagram.com/singaporepoly/?hl=en" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/singaporepoly/?hl=en" target="_blank"><i class="fab fa-instagram"></i></a>
            <a href="https://www.instagram.com/singaporepoly/?hl=en" target="_blank"><i class="fab fa-twitter"></i></a>
          </div>
        </div>
        <div class="footer-col">
          <h4>Subscribe</h4>
          <form action="#">
            <input type="email" placeholder="Your email address" required>
            <button type="submit">Subscribe</button>
          </form><br>
          <h4>Admin Dashboard</h4>
          <a style="color: white;" href="../php_admin/admin_dashboard.php">Dashboard</a>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2024 Authored by CSAD Group 5. All Rights Reserved.</p>
      </div>
    </div>
  </footer>


  <script src="script.js"></script>
</body>
</html>
