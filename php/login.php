
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/login.css">
<link rel="stylesheet" href="../css/navbar.css">
<title>Login Page</title>

</head>
<body>
<?php
 include("navbar.php");
 
 
?>
<div class="container">
    <div class="left-side">
      <h2>New to Our Restaurant?</h2>
      <p>Join us today and enjoy the convenience of online ordering. Get exclusive offers and track your orders easily.</p>
      <button> <a href="register.php" style="text-decoration: none; color: black;">Sign up</a> </button>
         
          
      
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
</html>