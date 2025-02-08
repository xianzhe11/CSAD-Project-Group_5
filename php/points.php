<?php
session_start();
include 'db_connection.php';
$_SESSION['prev_page'] = $_SERVER['REQUEST_URI'];

// Process POST request for redemption and exit immediately.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['redeem_action'])) {
    if (!isset($_SESSION['userloggedin']) || !$_SESSION['userloggedin'] || !isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please Log in to continue']);
        exit();
    }
    

    $redeem_cost = isset($_POST['redeem_cost']) ? floatval($_POST['redeem_cost']) : 0;
    $redeem_product_id = isset($_POST['redeem_product_id']) ? intval($_POST['redeem_product_id']) : 0;
    $redeem_product_name = isset($_POST['redeem_product_name']) ? $_POST['redeem_product_name'] : 'Redeemed Product';
    $redeem_product_image = isset($_POST['redeem_product_image']) ? $_POST['redeem_product_image'] : 'blank.png';

    if ($redeem_cost <= 0 || $redeem_product_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product details.']);
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT points FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($current_points);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit();
    }
    $stmt->close();

    if ($current_points < $redeem_cost) {
        echo json_encode(['success' => false, 'message' => 'Not enough points to redeem this product.']);
        exit();
    }

    $new_points = $current_points - $redeem_cost;
    $stmt = $conn->prepare("UPDATE users SET points = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_points, $user_id);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Could not update points.']);
        exit();
    }
    $stmt->close();
    $_SESSION['user_points'] = $new_points;

    $redeemed_product = [
        'id'            => $redeem_product_id,
        'name'          => $redeem_product_name,
        'description'   => 'This product was redeemed using points.',
        'price'         => 0.00,
        'image'         => $redeem_product_image,
        'quantity'      => 1,
        'customizations'=> json_encode([])
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    $_SESSION['cart'][] = $redeemed_product;

    echo json_encode(['success' => true, 'new_points' => $new_points]);
    exit();
} ?>

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
  if (isset($_SESSION['userloggedin'])) {
      include 'navbar_loggedIn.php';
  } else {
      include 'navbar.php';
  }
  
  
  $current_points = 0;
  if (isset($_SESSION['userloggedin']) && isset($_SESSION['user_id'])) {   // Fetch current points 
      $user_id = $_SESSION['user_id'];
      $stmt = $conn->prepare("SELECT points FROM users WHERE id = ?");
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $stmt->bind_result($points);
      if ($stmt->fetch()) {
          $current_points = $points;
          $_SESSION['user_points'] = $current_points;
      }
      $stmt->close();
  }
  ?>

  <?php if (isset($_GET['redeemed']) && $_GET['redeemed'] == '1' && isset($_GET['name'])): ?> <!-- If redeemed, show a green notification box -->
    <div class="redeemed-box">
      Successfully redeemed: <?php echo htmlspecialchars($_GET['name']); ?>
    </div>
  <?php endif; ?>
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
    <div class = "pointtext" >Current Points: <br> <?php echo $current_points; ?></div>
    <div class="container">
        <div class="slider">
            <img src = "../images/Limited.png" alt= "third" class = "image1" 
            data-id="101" data-cost="400" data-name="Limited Product" data-image="../images/Limited.png"  />

            <img src = "../images/pastaredeem.png" alt= "third" class = "image1"
            data-id="102" data-cost="450" data-name="Pasta Special" data-image="../images/pastaredeem.png" />

            <img src = "../images/cheeseredeem.png" alt= "third" class  = "image1"
            data-id="103" data-cost="300" data-name="Cheesy Delight"  data-image="../images/cheeseredeem.png" />

            <img src = "../images/chickenredeem.png" alt= "third" class = "image1"
            data-id="104" data-cost="250" data-name="Crispy Chicken" data-image="../images/chickenredeem.png"/>

            <img src = "../images/cheesy.png" alt= "third" class  = "image1"
            data-id="105" data-cost="250" data-name="Cheese Pizza" data-image= "../images/cheesy.png"/>
        </div>
    </div>  
    <div id="popup" class="popup">  <!-- Pop Up -->
      <div class="popup-content">
        <img id="popupimage" src="" alt="image">
        <p>Are you sure you want to redeem this product?</p>
        <button id="confirmBtn">Confirm</button>
        <button id="cancelBtn">Cancel</button>
        <div class = "userpoint">
          Your Current Points are: <span id="currentPointsDisplay"><?php echo $current_points; ?></span>
        </div>
        <div class = "costpoint">
          This redemption costs: <span id="redeemCostDisplay"></span> 
        </div>
        <div class = "pointafter">
          Your points after redemption: <span id="pointsAfterDisplay"></span> 
        </div>
      </div>
    </div>
    <div class = "cont1">
    <img src = "../food_images/fries.png" alt= "third" id = "img1" data-id="108" data-cost="150" data-name="Chili Pepper Fries"
    data-image="../food_images/fries.png"/>
    <div class = "label1">Chili Pepper Fries</div>
    <div class = "desc1">Fresh peeled french fries, seasoned with our homemade chili pepper. Guaranteed Spice and Flavour!</div>
    <button id="redeem1">Redeem</button>
  </div>
  <div class = "cont2">
    <img src = "../food_images/seasonalnobg.png" alt= "third" id = "img2" data-id="109" data-cost="500" data-name="Punny Pepperoni Paradise"
    data-image="../food_images/seasonalnobg.png"/>
    <div class = "label2">Punny Pepperoni Paradise</div>
    <div class = "desc2">This pepperoni pizza packs a punch with zesty tomato sauce, a mountain of pepperoni slices and cheesy layers.
      A paradise of flavor that’ll have you coming back for more puns.</div>
    <button id="redeem2">Redeem</button>
  </div>
  <div class = "cover"><div>
  <div class = "cover2"><div>
</body>

<script>
  const sliders = document.querySelectorAll('.slider');    // Horizontal scrolling
  sliders.forEach(slider => {
    slider.addEventListener('wheel', function(event) {
      event.preventDefault();     // Prevent vertical scrolling
      slider.scrollLeft += event.deltaY;     // Scroll horizontally.
    });
  });

  const popup = document.getElementById('popup');
  const confirmBtn = document.getElementById('confirmBtn');
  const redeem1 = document.getElementById('redeem1');
  const redeem2 = document.getElementById('redeem2');
  const cancelBtn = document.getElementById('cancelBtn');
  const popupimage = document.getElementById('popupimage');
  let selectedImage;

  const currentPointsDisplay = document.getElementById('currentPointsDisplay');
  const pointsAfterDisplay = document.getElementById('pointsAfterDisplay');
  const redeemCostDisplay = document.getElementById('redeemCostDisplay');
  let selectedCost = 0, selectedProductId = 0, selectedProductName = '', selectedProductImage = '';
  
  sliders.forEach(slider => {
    slider.addEventListener('click', function(event) {
      if (event.target.classList.contains('image1')) {
        selectedImage = event.target;
        popupimage.src = selectedImage.src;

        selectedCost = parseFloat(selectedImage.getAttribute('data-cost'));    // Get data attributes from the clicked item
        selectedProductId = parseInt(selectedImage.getAttribute('data-id'));
        selectedProductName = selectedImage.getAttribute('data-name');
        selectedProductImage = selectedImage.getAttribute('data-image');    // Get the image path.

        redeemCostDisplay.innerText = selectedCost;       // Update the popup display with the item's cost 
        const currentPoints = parseFloat(currentPointsDisplay.innerText); // Update display with current points
        pointsAfterDisplay.innerText = currentPoints - selectedCost; // Update display with computed new points

        popup.style.display = 'block';
      }
    });
  });

  confirmBtn.addEventListener('click', function() {
    const formData = new URLSearchParams(); // build  data to send to the server
    formData.append('redeem_action', '1'); // key/value pair to signal server redeem action is requested
    formData.append('redeem_cost', selectedCost);
    formData.append('redeem_product_id', selectedProductId);
    formData.append('redeem_product_name', selectedProductName);
    formData.append('redeem_product_image', selectedProductImage); // Pass the image

    fetch('points.php', { // send to points.php using POST, tell data is URL-encoded, body = string of formData
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData.toString()
    })

    .then(response => response.json())   //convert JSON Respone to javascript object
    .then(data => {
      if (data.success) {     //process object
        window.location.href = window.location.pathname + '?redeemed=1&name=' + encodeURIComponent(selectedProductName);
      } else {
        alert("Error: " + data.message);
        popup.style.display = 'none';
      }
    })
    .catch(error => {
      console.error('Error:', error);
      popup.style.display = 'none';
    });
  });

  cancelBtn.addEventListener('click', function() {
    popup.style.display = 'none';
  });

  redeem1.addEventListener('click', function() {
    selectedImage = document.getElementById("img1")
    popupimage.src = selectedImage.src;

    selectedCost = parseFloat(selectedImage.getAttribute('data-cost'));    // Get data attributes from the clicked item
    selectedProductId = parseInt(selectedImage.getAttribute('data-id'));
    selectedProductName = selectedImage.getAttribute('data-name');
    selectedProductImage = selectedImage.getAttribute('data-image');   // Get the image path.

    redeemCostDisplay.innerText = selectedCost;       // Update the popup display with the item's cost 
    const currentPoints = parseFloat(currentPointsDisplay.innerText); // Update display with current points
    pointsAfterDisplay.innerText = currentPoints - selectedCost; // Update display with computed new points
    popup.style.display = 'block';

  });

  redeem2.addEventListener('click', function() {
    selectedImage = document.getElementById("img2")
    popupimage.src = selectedImage.src;
    
    selectedCost = parseFloat(selectedImage.getAttribute('data-cost'));    // Get data attributes from the clicked item
    selectedProductId = parseInt(selectedImage.getAttribute('data-id'));
    selectedProductName = selectedImage.getAttribute('data-name');
    selectedProductImage = selectedImage.getAttribute('data-image');   // Get the image path.

    redeemCostDisplay.innerText = selectedCost;       // Update the popup display with the item's cost 
    const currentPoints = parseFloat(currentPointsDisplay.innerText); // Update display with current points
    pointsAfterDisplay.innerText = currentPoints - selectedCost; // Update display with computed new points
    popup.style.display = 'block';
  });

</script>
</html>