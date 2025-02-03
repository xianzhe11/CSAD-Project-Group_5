<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/points.css">
<title>Points</title>

</head>
<body>
<?php
session_start();
if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
    include 'navbar_loggedIn.php';
  } else {
    include 'navbar.php';
  }
$_SESSION['prev_page'] = $_SERVER['REQUEST_URI'];
?>
    <div class= "wave">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#ff6330" fill-opacity="1" 
        d="M0,64L48,96C96,128,192,192,288,208C384,224,480,192,576,160C672,128,768,96,864,96C960,96,1056,128,1152,128C1248,128,1344,96,1392,80L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
    <div class= "wave2">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1640 320"><path fill="#ff5500" fill-opacity="1" 
        d="M0,128L48,117.3C96,107,192,85,288,85.3C384,85,480,107,576,138.7C672,171,768,213,864,208C960,203,1056,149,1152,138.7C1248,128,1344,160,1392,176L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
    <div class = "pointtext" >Current Points</div>
    <div class="container">
        <div class="slider">
            <img src = "../images/Limited.png" alt= "third" id = "image"/>
            <img src = "../images/cheeseredeem.png" alt= "third" id  = "image"/>
            <img src = "../images/cheeseredeem.png" alt= "third" id = "image"/>
            <img src = "../food_images/drink.png" alt= "third" id = "image"/>
            <img src = "../food_images/drink.png" alt= "third" id  = "image"/>
            <img src = "../food_images/drink.png" alt= "third" id = "image"/>
            <img src = "../food_images/drink.png" alt= "third" id  = "image"/>
        </div>
    </div>  
  

</body>
<script>
  const container = document.querySelector('.slider');

  container.addEventListener('wheel', function(event) {
    // Prevent vertical scrolling
    event.preventDefault();
    // Scroll horizontally.
    container.scrollLeft += event.deltaY;
  });
</script>
</html>