<?php
session_start();
include 'db_connection.php';
$_SESSION['prev_page'] = $_SERVER['REQUEST_URI'];
// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Initialize cart if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart with Customizations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_add_to_cart'])) {
    // Sanitize and retrieve item data
    $itemId = intval($_POST['item_id']);
    $itemName = sanitize_input($_POST['item_name']);
    $itemDescription = sanitize_input($_POST['item_description']);
    $itemPrice = floatval($_POST['item_price']);
    $itemImage = sanitize_input($_POST['item_image']);
    $category = sanitize_input($_POST['category']);

    // Initialize customizations array
    $customizations = [];

    // Process customizations based on category
    switch (strtolower($category)) {
        case 'appetizers':
            $custom_extra_sauce = isset($_POST['extra_sauce']) ? 'Yes' : 'No';
            $custom_size = isset($_POST['appetizer_size']) ? sanitize_input($_POST['appetizer_size']) : 'Regular';
            $customizations = [
                'Extra Dipping Sauce' => $custom_extra_sauce,
                'Size' => $custom_size
            ];
            break;
        case 'beverages':
            $custom_size = isset($_POST['beverage_size']) ? sanitize_input($_POST['beverage_size']) : 'Medium';
            $custom_no_ice = isset($_POST['no_ice']) ? 'Yes' : 'No';
            $custom_sugar_level = isset($_POST['sugar_level']) ? sanitize_input($_POST['sugar_level']) : 'Normal';
            $customizations = [
                'Size' => $custom_size,
                'No Ice' => $custom_no_ice,
                'Sugar Level' => $custom_sugar_level
            ];
            break;
        case 'burgers':
            $custom_extra_cheese = isset($_POST['extra_cheese']) ? 'Yes' : 'No';
            $custom_no_onion = isset($_POST['no_onion']) ? 'Yes' : 'No';
            $custom_add_bacon = isset($_POST['add_bacon']) ? 'Yes' : 'No';
            $customizations = [
                'Extra Cheese' => $custom_extra_cheese,
                'No Onion' => $custom_no_onion,
                'Add Bacon' => $custom_add_bacon
            ];
            break;
        case 'pizza':
            $custom_crust = isset($_POST['crust_type']) ? sanitize_input($_POST['crust_type']) : 'Regular';
            $custom_extra_toppings = isset($_POST['extra_toppings']) ? 'Yes' : 'No';
            $custom_size = isset($_POST['pizza_size']) ? sanitize_input($_POST['pizza_size']) : 'Medium';
            $customizations = [
                'Crust Type' => $custom_crust,
                'Extra Toppings' => $custom_extra_toppings,
                'Size' => $custom_size
            ];
            break;
        case 'seasonal':
            $custom_special_ingredient = isset($_POST['special_ingredient']) ? 'Yes' : 'No';
            $custom_size = isset($_POST['seasonal_size']) ? sanitize_input($_POST['seasonal_size']) : 'Regular';
            $customizations = [
                'Special Ingredient' => $custom_special_ingredient,
                'Size' => $custom_size
            ];
            break;
        default:
            // Default or no customizations
            break;
    }

    // Encode customizations as JSON
    $customization_json = json_encode($customizations);

    // Check if item with the same customizations already exists in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$cartItem) {
        if ($cartItem['id'] == $itemId && $cartItem['customizations'] == $customization_json) {
            $cartItem['quantity'] += 1;
            $found = true;
            break;
        }
    }
    if (!$found) {
        // Add a new item to cart
        $_SESSION['cart'][] = [
            'id'            => $itemId,
            'name'          => $itemName,
            'description'   => $itemDescription,
            'price'         => $itemPrice,
            'image'         => $itemImage,
            'quantity'      => 1,
            'customizations'=> $customization_json,
        ];
    }

    // Set a success message
    $_SESSION['success_message'] = "{$itemName} has been added to your cart.";

    // Redirect to prevent form resubmission
    header('Location: menu.php');
    exit;
}

// Fetch all categories from the database
$categoryQuery = 'SELECT catName FROM menu_categories';
$categoryResult = $conn->query($categoryQuery);

$categories = [];
if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row['catName'];
    }
} else {
    echo "<!-- Query Failed: (" . $conn->errno . ") " . $conn->error . " -->";
}

// Retrieve and sanitize the selected category from the URL
$selectedCategory = isset($_GET['category']) ? strtolower(trim($_GET['category'])) : strtolower($categories[0]);

// Validate the selected category
$validCategories = array_map('strtolower', $categories);
if (!in_array($selectedCategory, $validCategories)) {
    $selectedCategory = strtolower($categories[0]); // Default to the first category
}

// Find the index of the selected category to set the active tab
$activeIndex = array_search($selectedCategory, $validCategories);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/menu.css">
    <style>
        /* Additional styling for better UI */

    </style>
</head>
<body>
    <?php
    if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
      include 'navbar_loggedIn.php';
    } else {
      include 'navbar.php';
    }
    ?>

    <!-- Display Success Message -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
            <?= $_SESSION['success_message'] ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <div class="heading">
        <div class="heading-title">Restaurant Menu</div>
    </div>

    <div class="category-nav">
        <ul class="nav nav-tabs justify-content-center" id="categoryTab" role="tablist">
            <?php foreach ($categories as $index => $category): ?>
                <?php 
                    $categorySlug = strtolower($category);
                    $isActive = ($index === $activeIndex) ? 'active' : '';
                ?>
                <li class="nav-item">
                    <a class="nav-link <?= $isActive ?>" id="<?= $categorySlug ?>-tab" data-toggle="tab" href="#<?= $categorySlug ?>" role="tab" aria-controls="<?= $categorySlug ?>" aria-selected="<?= ($isActive === 'active') ? 'true' : 'false'; ?>">
                        <?= htmlspecialchars($category) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="container menu-section">
        <div class="tab-content" id="categoryTabContent">
            <?php foreach ($categories as $index => $category): ?>
                <?php 
                    $categorySlug = strtolower($category);
                    $isActive = ($categorySlug === $selectedCategory) ? 'show active' : '';
                ?>
                <div class="tab-pane fade <?= $isActive ?>" id="<?= $categorySlug ?>" role="tabpanel" aria-labelledby="<?= $categorySlug ?>-tab">
                    <div class="row menu-items">
                        <?php
                        // Prepare and execute the statement
                        $stmt = $conn->prepare('SELECT * FROM menu_items WHERE catName = ?');
                        $stmt->bind_param('s', $category);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        ?>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <div class="col-lg-4 col-md-6 col-sm-12 menu-item mb-4">
                                    <div class="menu-card card h-100">
                                        <img src="../food_images/<?= htmlspecialchars($row['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['itemName']) ?>">
                                        <div class="card-body d-flex flex-column">
                                            <h5 class="card-title text-center mt-3"> <?= htmlspecialchars($row['itemName']) ?> </h5>
                                            <p class="description"> <?= htmlspecialchars($row['description']) ?> </p>
                                            <?php if ($row['status'] == 'Unavailable'): ?>
                                                <p class="card-status">Unavailable</p>
                                            <?php else: ?>
                                                <div class="button-container mt-auto">
                                                    <p class="card-text">SGD&nbsp;<?= number_format($row['price'], 2) ?>/-</p>
                                                    <button 
                                                        type="button" 
                                                        class="btn btn-primary addItemBtn" 
                                                        data-toggle="modal" 
                                                        data-target="#customizeModal"
                                                        data-id="<?= htmlspecialchars($row['id']) ?>"
                                                        data-name="<?= htmlspecialchars($row['itemName']) ?>"
                                                        data-description="<?= htmlspecialchars($row['description']) ?>"
                                                        data-price="<?= htmlspecialchars($row['price']) ?>"
                                                        data-image="<?= htmlspecialchars($row['image']) ?>"
                                                        data-category="<?= htmlspecialchars($category) ?>"
                                                    >
                                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <p class="text-center">No items available in this category.</p>
                            </div>
                        <?php endif; ?>
                        <?php 
                            $stmt->close(); 
                            $result->free();
                        ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Customization Modal -->
    <div class="modal fade" id="customizeModal" tabindex="-1" aria-labelledby="customizeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <form method="POST" id="customizeForm">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="customizeModalLabel">Customize Your Order</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            
            <div class="modal-body">
              <!-- Hidden Inputs to Store Item Data -->
              <input type="hidden" name="item_id" id="modal_item_id">
              <input type="hidden" name="item_name" id="modal_item_name">
              <input type="hidden" name="item_description" id="modal_item_description">
              <input type="hidden" name="item_price" id="modal_item_price">
              <input type="hidden" name="item_image" id="modal_item_image">
              <input type="hidden" name="category" id="modal_item_category">

              <div class="row">
                  <!-- Item Image -->
                  <div class="col-md-4 text-center">
                      <img src="" alt="" id="modal_item_image_display" class="img-fluid mb-3" style="max-height: 150px;">
                  </div>
                  <!-- Item Details -->
                  <div class="col-md-8">
                      <h5 id="modal_item_name_display"></h5>
                      <p id="modal_item_description_display"></p>
                      <p><strong>Price:</strong> SGD <span id="modal_item_price_display"></span>/-</p>
                  </div>
              </div>

              <hr>

              <!-- Customization Options -->
              <div class="form-group">
                <h6>Customize Your Meal</h6>
                
                <!-- Appetizers Customizations -->
                <div id="customizations_appetizers" class="customizations-section">
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" id="extra_sauce" name="extra_sauce" value="1">
                      <label class="form-check-label" for="extra_sauce">Extra Dipping Sauce</label>
                    </div>
                    <div class="form-group mt-3">
                      <label for="appetizer_size">Size:</label>
                      <select class="form-control" id="appetizer_size" name="appetizer_size">
                        <option value="Regular" selected>Regular</option>
                        <option value="Large">Large</option>
                      </select>
                    </div>
                </div>

                <!-- Beverages Customizations -->
                <div id="customizations_beverages" class="customizations-section">
                    <div class="form-group">
                      <label for="beverage_size">Size:</label>
                      <select class="form-control" id="beverage_size" name="beverage_size">
                        <option value="Small">Small</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="Large">Large</option>
                      </select>
                    </div>
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" id="no_ice" name="no_ice" value="1">
                      <label class="form-check-label" for="no_ice">No Ice</label>
                    </div>
                    <div class="form-group mt-3">
                      <label for="sugar_level">Sugar Level:</label>
                      <select class="form-control" id="sugar_level" name="sugar_level">
                        <option value="No Sugar">No Sugar</option>
                        <option value="Less Sugar">Less Sugar</option>
                        <option value="Normal" selected>Normal</option>
                        <option value="Extra Sugar">Extra Sugar</option>
                      </select>
                    </div>
                </div>

                <!-- Burgers Customizations -->
                <div id="customizations_burgers" class="customizations-section">
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" id="extra_cheese" name="extra_cheese" value="1">
                      <label class="form-check-label" for="extra_cheese">Extra Cheese</label>
                    </div>
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" id="no_onion" name="no_onion" value="1">
                      <label class="form-check-label" for="no_onion">No Onion</label>
                    </div>
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" id="add_bacon" name="add_bacon" value="1">
                      <label class="form-check-label" for="add_bacon">Add Bacon</label>
                    </div>
                </div>

                <!-- Pizza Customizations -->
                <div id="customizations_pizza" class="customizations-section">
                    <div class="form-group">
                      <label for="crust_type">Crust Type:</label>
                      <select class="form-control" id="crust_type" name="crust_type">
                        <option value="Regular" selected>Regular</option>
                        <option value="Thin">Thin</option>
                        <option value="Thick">Thick</option>
                        <option value="Gluten-Free">Gluten-Free</option>
                      </select>
                    </div>
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" id="extra_toppings" name="extra_toppings" value="1">
                      <label class="form-check-label" for="extra_toppings">Extra Toppings</label>
                    </div>
                    <div class="form-group mt-3">
                      <label for="pizza_size">Size:</label>
                      <select class="form-control" id="pizza_size" name="pizza_size">
                        <option value="Small">Small</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="Large">Large</option>
                        <option value="Extra Large">Extra Large</option>
                      </select>
                    </div>
                </div>

                <!-- Seasonal Customizations -->
                <div id="customizations_seasonal" class="customizations-section">
                    <div class="form-check">
                      <input type="checkbox" class="form-check-input" id="special_ingredient" name="special_ingredient" value="1">
                      <label class="form-check-label" for="special_ingredient">Special Ingredient</label>
                    </div>
                    <div class="form-group mt-3">
                      <label for="seasonal_size">Size:</label>
                      <select class="form-control" id="seasonal_size" name="seasonal_size">
                        <option value="Regular" selected>Regular</option>
                        <option value="Large">Large</option>
                      </select>
                    </div>
                </div>

              </div>
            </div>
            
            <div class="modal-footer">
              <button type="submit" name="confirm_add_to_cart" class="btn btn-success">
                <i class="fas fa-cart-plus"></i> Add to Cart
              </button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">
                <i class="fas fa-times"></i> Cancel
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js'></script>
    
    <script>
      $(document).ready(function(){
        $('#customizeModal').on('show.bs.modal', function (event) {
          var button = $(event.relatedTarget); // Button that triggered the modal
          var itemId = button.data('id');
          var itemName = button.data('name');
          var itemDescription = button.data('description');
          var itemPrice = button.data('price');
          var itemImage = button.data('image');
          var category = button.data('category');

          // Update the modal's content.
          var modal = $(this);
          modal.find('#modal_item_id').val(itemId);
          modal.find('#modal_item_name').val(itemName);
          modal.find('#modal_item_description').val(itemDescription);
          modal.find('#modal_item_price').val(itemPrice);
          modal.find('#modal_item_image').val(itemImage);
          modal.find('#modal_item_category').val(category);

          modal.find('#modal_item_name_display').text(itemName);
          modal.find('#modal_item_description_display').text(itemDescription);
          modal.find('#modal_item_price_display').text(parseFloat(itemPrice).toFixed(2));
          modal.find('#modal_item_image_display').attr('src', '../food_images/' + itemImage);
          modal.find('#modal_item_image_display').attr('alt', itemName);
          
          // Reset customization options
          modal.find('input[type="checkbox"]').prop('checked', false);
          modal.find('select').each(function(){
              $(this).val($(this).find('option[selected]').val());
          });

          // Hide all customization sections
          $('.customizations-section').hide();

          // Show relevant customization section based on category
          var categorySlug = category.toLowerCase();
          $('#customizations_' + categorySlug).show();
        });
      });
    </script>

    <?php include 'footer.html'; ?>
</body>
</html>
