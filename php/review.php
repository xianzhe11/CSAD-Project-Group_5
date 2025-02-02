<?php
session_start();

// Include the database connection
require_once 'db_connection.php'; // Ensure this path is correct
$_SESSION['prev_page'] = $_SERVER['REQUEST_URI'];
// Initialize variables
$errors = [];
$success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize input
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : '';
    $date_of_visit = isset($_POST['date_of_visit']) ? $_POST['date_of_visit'] : '';
    $phone_number = isset($_POST['phone_number']) ? htmlspecialchars(trim($_POST['phone_number'])) : '';
    $feedback = isset($_POST['feedback']) ? htmlspecialchars(trim($_POST['feedback'])) : '';
    $food_quality = isset($_POST['food_quality']) ? (int)$_POST['food_quality'] : 0;
    $service = isset($_POST['service']) ? (int)$_POST['service'] : 0;
    $value = isset($_POST['value']) ? (int)$_POST['value'] : 0;
    $cleanliness = isset($_POST['cleanliness']) ? (int)$_POST['cleanliness'] : 0;
    $speed = isset($_POST['speed']) ? (int)$_POST['speed'] : 0;

    // Validation for new inputs
    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($date_of_visit)) {
        $errors[] = "Date of Visit is required.";
    }

    if (empty($phone_number)) {
        $errors[] = "Phone Number is required.";
    } elseif (!preg_match('/^\+65\s\d{4}\s\d{4}$/', $phone_number)) {
        $errors[] = "Invalid Phone Number format. Please use the format +65 1234 5678.";
    }

    // Validation for feedback
    if (empty($feedback)) {
        $errors[] = "Feedback is required.";
    }

    // Validate ratings (should be between 1 and 5)
    foreach (['food_quality', 'service', 'value', 'cleanliness', 'speed'] as $rating) {
        if (!isset($$rating) || $$rating < 1 || $$rating > 5) {
            $errors[] = ucfirst(str_replace('_', ' ', $rating)) . " rating must be between 1 and 5.";
        }
    }

    if (empty($errors)) {
        // Prepare the SQL statement with new fields and status
        $stmt = $conn->prepare("INSERT INTO `reviews` (`name`, `email`, `date_of_visit`, `phone_number`, `feedback`, `food_quality`, `service`, `value`, `cleanliness`, `speed`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        if ($stmt) {
            $stmt->bind_param(
                "sssssiiiii",
                $name,
                $email,
                $date_of_visit,
                $phone_number,
                $feedback,
                $food_quality,
                $service,
                $value,
                $cleanliness,
                $speed
            );

            if ($stmt->execute()) {
                $success = "Thank you for your feedback! Your review is pending approval.";
                // Clear POST data to prevent resubmission
                $_POST = [];
            } else {
                $errors[] = "Error submitting your review. Please try again.";
            }

            $stmt->close();
        } else {
            $errors[] = "Database error: Unable to prepare statement.";
        }
    }
}

// Retrieve existing reviews with status 'approved'
$reviews = [];
$result = $conn->query("SELECT * FROM `reviews` WHERE `status` = 'approved' ORDER BY `created_at` DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $result->free();
} else {
    $errors[] = "Error fetching reviews.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Reviews</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css' />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="../css/review.css" rel='stylesheet'>
</head>
<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main Container -->
    <div class="review-container">
        <h2 class="mb-4 text-center">We Value Your Feedback</h2>

        <!-- Display Success or Error Messages -->
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Review Submission Form -->
        <div class="review-form">
            <form action="review.php" method="POST">
                <!-- CSRF Token (Optional but Recommended) -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] ?? ''; ?>">

                <div class="form-row">
                    <!-- Name -->
                    <div class="form-group col-md-6">
                        <label for="name"><strong>Name</strong></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" 
                               required>
                    </div>
                    <!-- Email -->
                    <div class="form-group col-md-6">
                        <label for="email"><strong>Email</strong></label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                               required>
                    </div>
                </div>
                <div class="form-row">
                    <!-- Date of Visit -->
                    <div class="form-group col-md-6">
                        <label for="date_of_visit"><strong>Date of Visit</strong></label>
                        <input type="date" class="form-control" id="date_of_visit" name="date_of_visit" 
                               value="<?php echo isset($_POST['date_of_visit']) ? htmlspecialchars($_POST['date_of_visit']) : ''; ?>" 
                               required>
                    </div>
                    <!-- Phone Number -->
                    <div class="form-group col-md-6">
                        <label for="phone_number"><strong>Phone Number</strong></label>
                        <input type="tel" class="form-control" id="phone_number" name="phone_number" 
                               pattern="^\+65\s\d{4}\s\d{4}$" 
                               placeholder="+65 1234 5678"
                               value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>" 
                               required>
                        <small class="form-text text-muted">Format: +65 1234 5678</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="feedback"><strong>Your Feedback</strong></label>
                    <textarea class="form-control" id="feedback" name="feedback" rows="4" required><?php echo isset($_POST['feedback']) ? htmlspecialchars($_POST['feedback']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label><strong>Rate Us:</strong></label>
                    <div class="d-flex flex-wrap">
                        <!-- Food Quality -->
                        <div class="rating-category mr-3 mb-2">
                            <span>Food Quality:</span>
                            <div class="star-rating">
                                <?php
                                    $food_q = isset($_POST['food_quality']) ? (int)$_POST['food_quality'] : 0;
                                    for ($i = 5; $i >= 1; $i--) {
                                        $checked = ($food_q == $i) ? 'checked' : '';
                                        echo '<input type="radio" name="food_quality" value="' . $i . '" id="food_quality_' . $i . '" ' . $checked . '>';
                                        echo '<label for="food_quality_' . $i . '"><i class="fas fa-star"></i></label>';
                                    }
                                ?>
                            </div>
                        </div>
                        <!-- Service -->
                        <div class="rating-category mr-3 mb-2">
                            <span>Service:</span>
                            <div class="star-rating">
                                <?php
                                    $service_r = isset($_POST['service']) ? (int)$_POST['service'] : 0;
                                    for ($i = 5; $i >= 1; $i--) {
                                        $checked = ($service_r == $i) ? 'checked' : '';
                                        echo '<input type="radio" name="service" value="' . $i . '" id="service_' . $i . '" ' . $checked . '>';
                                        echo '<label for="service_' . $i . '"><i class="fas fa-star"></i></label>';
                                    }
                                ?>
                            </div>
                        </div>
                        <!-- Value -->
                        <div class="rating-category mr-3 mb-2">
                            <span>Value:</span>
                            <div class="star-rating">
                                <?php
                                    $value_r = isset($_POST['value']) ? (int)$_POST['value'] : 0;
                                    for ($i = 5; $i >= 1; $i--) {
                                        $checked = ($value_r == $i) ? 'checked' : '';
                                        echo '<input type="radio" name="value" value="' . $i . '" id="value_' . $i . '" ' . $checked . '>';
                                        echo '<label for="value_' . $i . '"><i class="fas fa-star"></i></label>';
                                    }
                                ?>
                            </div>
                        </div>
                        <!-- Cleanliness -->
                        <div class="rating-category mr-3 mb-2">
                            <span>Cleanliness:</span>
                            <div class="star-rating">
                                <?php
                                    $cleanliness_r = isset($_POST['cleanliness']) ? (int)$_POST['cleanliness'] : 0;
                                    for ($i = 5; $i >= 1; $i--) {
                                        $checked = ($cleanliness_r == $i) ? 'checked' : '';
                                        echo '<input type="radio" name="cleanliness" value="' . $i . '" id="cleanliness_' . $i . '" ' . $checked . '>';
                                        echo '<label for="cleanliness_' . $i . '"><i class="fas fa-star"></i></label>';
                                    }
                                ?>
                            </div>
                        </div>
                        <!-- Speed -->
                        <div class="rating-category mr-3 mb-2">
                            <span>Speed:</span>
                            <div class="star-rating">
                                <?php
                                    $speed_r = isset($_POST['speed']) ? (int)$_POST['speed'] : 0;
                                    for ($i = 5; $i >= 1; $i--) {
                                        $checked = ($speed_r == $i) ? 'checked' : '';
                                        echo '<input type="radio" name="speed" value="' . $i . '" id="speed_' . $i . '" ' . $checked . '>';
                                        echo '<label for="speed_' . $i . '"><i class="fas fa-star"></i></label>';
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Submit Review</button>
            </form>
        </div>

        <!-- Display Reviews -->
        <div class="reviews-list">
            <h3 class="mb-4">Customer Reviews</h3>
            <?php if (empty($reviews)): ?>
                <p>No reviews yet. Be the first to review!</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <strong>#<?php echo htmlspecialchars($review['id']); ?> <?php echo htmlspecialchars($review['name']); ?></strong>
                            <span class="review-date">Visited on <?php echo date("F j, Y", strtotime($review['date_of_visit'])); ?></span>
                        </div>
                        <p class="mt-2"><?php echo nl2br(htmlspecialchars($review['feedback'])); ?></p>
                        <div class="d-flex flex-wrap">
                            <!-- Display Ratings -->
                            <?php
                                $categories = [
                                    'Food Quality' => $review['food_quality'],
                                    'Service' => $review['service'],
                                    'Value' => $review['value'],
                                    'Cleanliness' => $review['cleanliness'],
                                    'Speed' => $review['speed']
                                ];
                                foreach ($categories as $category => $rating) {
                                    echo '<div class="rating-category mr-4 mb-2">';
                                    echo '<strong>' . htmlspecialchars($category) . ':</strong> ';
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            echo '<i class="fas fa-star"></i>';
                                        } else {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                    }
                                    echo '</div>';
                                }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.html'; ?>

    <!-- JavaScript -->
    <script src='https://code.jquery.com/jquery-3.5.1.slim.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js'></script>
    <!-- Inline JavaScript for Interactive Star Ratings -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const starRatings = document.querySelectorAll('.star-rating');

            starRatings.forEach(function(starRating) {
                const stars = starRating.querySelectorAll('label');
                const inputs = starRating.querySelectorAll('input');

                stars.forEach(function(star, index) {
                    star.addEventListener('mouseover', function() {
                        highlightStars(starRating, index + 1);
                    });

                    star.addEventListener('mouseout', function() {
                        resetStars(starRating);
                    });

                    star.addEventListener('click', function() {
                        setRating(starRating, index + 1);
                    });
                });

                // Initialize stars based on checked input
                resetStars(starRating);
            });

            function highlightStars(starRating, rating) {
                const stars = starRating.querySelectorAll('label');
                stars.forEach(function(star, index) {
                    if (index < rating) {
                        star.classList.add('fas');
                        star.classList.remove('far');
                    } else {
                        star.classList.add('far');
                        star.classList.remove('fas');
                    }
                });
            }

            function resetStars(starRating) {
                const checkedInput = starRating.querySelector('input:checked');
                if (checkedInput) {
                    const rating = parseInt(checkedInput.value);
                    highlightStars(starRating, rating);
                } else {
                    // If no rating is selected, reset all stars to empty
                    const stars = starRating.querySelectorAll('label');
                    stars.forEach(function(star) {
                        star.classList.add('far');
                        star.classList.remove('fas');
                    });
                }
            }

            function setRating(starRating, rating) {
                const inputs = starRating.querySelectorAll('input');
                inputs.forEach(function(input) {
                    input.checked = false;
                });
                const selectedInput = starRating.querySelector('input[value="' + rating + '"]');
                if (selectedInput) {
                    selectedInput.checked = true;
                }
                highlightStars(starRating, rating);
            }
        });
    </script>
</body>
</html>
