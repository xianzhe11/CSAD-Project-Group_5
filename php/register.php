
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/register.css">
<link rel="stylesheet" href="../css/navbar.css">
<title>Login Page</title>

</head>
<body>
<?php
 include("navbar.php");
 
?>
<div class="container">
    <div class="left-side">
      <div class = "slide">
        <img src = "../images/pizza.png" alt= "first" style = "--pos:1" />
        <img src = "../images/burger.png" alt= "second" style = "--pos:2" />
        <img src = "../images/pizza.png" alt= "third" style = "--pos:3" />
        <img src = "../images/pizza1.png" alt= "four" style = "--pos:4" />
        <img src = "../images/pizza1.png" alt= "five" style = "--pos:5" />
        <img src = "../images/pizza1.png" alt= "six" style = "--pos:6" />
</div>
          
      
    </div>
    <div class="right-side">
      <h2>Sign in</h2>
      <form>
        <div class="input-group">
          <label for="email">Email</label>
          <input type="email" id="email" placeholder="Email">
        </div>
        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" id="password" placeholder="Password">
        </div>
        <button type="submit" class="login-button">LOGIN</button>
      </form>
    </div>
  </div>
</body>