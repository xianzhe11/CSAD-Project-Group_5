
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../css/register.css">
<link rel="stylesheet" href="../css/navbar.css">
<title>Register</title>

</head>
<body>
<?php
session_start();
include("navbar.php");
include 'db_connection.php';
$_SESSION['prev_page'] = "index.php";
?>

<?php
$user= $email = $pass= "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get data
        
        $user = $_POST['username'];
        $email = $_POST['email'];
        $pass = $_POST['password'];
    
        // Hash password
        $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
      
        $sql = "SELECT * FROM users WHERE username='$user'";
        $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo '<div class="error">Sorry! Username Already Exists</div>'; //registed username
        $user = "";
    } else {
        $sql = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo '<div class="error">Sorry! Email is already registered</div>'; // registered email
            $email = "";
        } else {
            $sql = "INSERT INTO users (username, email, pass) VALUES ('$user', '$email', '$hashed_password')"; //insert data
            if ($conn->query($sql) === TRUE) {
                echo '<div class="popup">Account Created! Please proceed to Login.</div>';
            } 
        }
    }

    $conn->close();
}

?>

<div class="container">
    <div class="left-side">
      <div class = "slide">
        <div  style = " color:black ; font-size : 14px; font-weight : bold">Rain or shine, it's a fine time to dine.You can't dismiss our Burger Bliss.</div>
        <img src = "../images/pizza.png" alt= "first" style = "--pos:1" id = "image"/>
        <img src = "../images/burger.png" alt= "second" style = "--pos:2" id = "image"/>
        <img src = "../food_images/drink.png" alt= "third" style = "--pos:3" id = "image"/>
        <img src = "../food_images/chickenburger.png" alt= "four" style = "--pos:4" id = "image"/>
        <img src = "../food_images/spaghetti.png" alt= "five" style = "--pos:5" id = "image"/>
        <img src = "../images/pizza1.png" alt= "six" style = "--pos:6"id = "image" />
</div>

    </div>
    <div class="right-side">
      <h2>Sign Up</h2>
      <form id ="form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <div class="input-group">
          <label for="username">Username</label>
          <input type="username" id="username" name ="username" placeholder="Username" value="<?php echo $user; ?>">
          <span id = "erroruser"></span>
        </div>
        <div class="input-group">
          <label for="email">Email</label>
          <input type="email" id="email" name ="email" placeholder="Email" value="<?php echo $email; ?>">
          <span id = "erroremail"></span>
        </div>
        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" id="password" name ="password" placeholder="Password" value="<?php echo $pass; ?>">
          <span id = "errorpass"></span>
        </div>
        
        <button onclick="validate();"  class="login-button" id = "btn" >Create Account</button>
      </form>
    </div>
  </div>
  <img src = "../images/pizzaicon.png" alt= "icon" class = "icon1"  />
    <img src = "../images/pizzaicon.png" alt= "icon" class = "icon2"  />
    <img src = "../images/pizzaicon.png" alt= "icon" class = "icon3"  />
    <img src = "../images/pizzaicon.png" alt= "icon" class = "icon4"  />
</body>

<script>
    const form = document.getElementById('form');
    const myLink = document.getElementById('btn');
    const input1 = document.getElementById('username');
    const input2 = document.getElementById('password');
    const input3 = document.getElementById('email');


    myLink.addEventListener('click', function(event) {
        if (input1.value.trim() === '' |input2.value.trim() === ''|input3.value.trim() === '') {
            event.preventDefault(); 
        }
    });
  function validateUsername() {
    var x = document.getElementById("username").value;
    document.getElementById("erroruser").innerHTML="";
    if (x===""){
        document.getElementById("erroruser").innerHTML="<span style='color:red; font-size : 10px ; margin-left : 11%'>Username is Empty</span>";
    } 
}   
 
function validatePass() {
    var x = document.getElementById("password").value;
    document.getElementById("errorpass").innerHTML="";
    if (x===""){
        document.getElementById("errorpass").innerHTML="<span style='color:red; font-size : 10px ; margin-left : 11%'>Password is Empty </span>";
}  
}
function validateEmail() {
    var x = document.getElementById("email").value;
    document.getElementById("erroremail").innerHTML="";
    if (x===""){
        document.getElementById("erroremail").innerHTML="<span style='color:red; font-size : 10px ; margin-left : 11%'>Email is Empty </span>";
        <?php
        
        ?>} 
}
 
function validate() {
    validateUsername();
    validatePass();
    validateEmail();
}
 
</script>