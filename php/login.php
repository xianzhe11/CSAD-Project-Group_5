
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/login.css">
<title>Login Page</title>

</head>

<body>
<?php
session_start();
include 'db_connection.php';
?>  

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);
    echo '<div class="error1"></div>';
    echo '<div class="error2">s</div>';
    if ($result->num_rows == 1) {
        $sql = "SELECT pass FROM users WHERE email='$email'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $hashed_password = $row['pass'];
        echo '<div class="error1"></div>';
        if(password_verify($pass, $hashed_password)){
       
          $_SESSION['user_email'] = $email; // user exists so set session
          $_SESSION['userloggedin'] = true;

          if (isset($_SESSION['prev_page'])) {
            $prevPage = $_SESSION['prev_page'];
            unset($_SESSION['prev_page']); // clear previous page URL
            header("Location: $prevPage"); //back to the previous page
            exit();
        } else {
            header("Location: index.php"); // Default redirect
            exit();
        }
         
        }
        else {
          echo '<div class="error1">Credentials are Wrong. Please check your Credentials</div>';
          echo '<div class="error2">Credentials are Wrong. Please check your Credentials</div>'; //wrong creds
      }
    } else {
        echo '<div class="error1">Credentials are Wrong. Please check your Credentials</div>'; //wrong creds
        echo '<div class="error2">Credentials are Wrong. Please check your Credentials</div>';
    }

    $conn->close();
}
?>

    <div class = "rect">co</div>
    <img src = "../food_images/cheeseburger.png" alt= "cheeseburger" class = "burger"  />
    <img src = "../food_images/fries.png" alt= "fries" class = "fries"  />
    <img src = "../food_images/drink.png" alt= "drinks" class = "drink"  />
    <div class= "wave">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="100 0 1340 320"><path fill="#ffb84d" fill-opacity="1" 
            d="M0,224L48,229.3C96,235,192,245,288,256C384,267,480,277,576,256C672,235,768,181,864,133.3C960,85,1056,43,1152,32C1248,21,1344,43,1392,53.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
            </svg>
        </div>  
      <div class = "cover"></div>
    <div class="container">
      
    
      <div class="right-side">
        <h2>Sign in</h2>
        <form id = "form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
          <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name = "email" placeholder="Email">
          </div>
          <div class="input-group">
            <label for="password">Password</label>
            <input type="password" id="password" name = "password" placeholder="Password">
          </div>
          <button type="submit" class="login-button"><h5>LOGIN</h5></button>
          <div class = "signuptext">Haven't Signed Up With Us? </div>
          <div class = "signuptext2">Start Now!</div>
          <button class = "sign"> <a href="register.php" style = "text-decoration: none; color:black" >Sign up</a> </button>

        </form>
      </div>
    </div>
    <img src = "../images/burgericon.png" alt= "icon" class = "icon1"  />
    <img src = "../images/burgericon.png" alt= "icon" class = "icon2"  />
    <img src = "../images/burgericon.png" alt= "icon" class = "icon3"  />
    <img src = "../images/burgericon.png" alt= "icon" class = "icon4"  />
    <img src = "../images/pizzabg.png" alt= "icon" class = "background"  />
</body>
</html>