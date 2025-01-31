<?php
// admin_dashboard.php



// TODO: Implement authentication check to ensure only authorized users can access the dashboard
// Example:
// if (!isset($_SESSION['admin_logged_in'])) {
//     header("Location: login.php");
//     exit();
// }

session_start();
include "db_connection.php"; // Include the database connection

// Fetch total Menu Items
$menu_count_query = "SELECT COUNT(*) as total_menu FROM menu_items";
$menu_result = $conn->query($menu_count_query);

if ($menu_result && $menu_result->num_rows > 0) {
    $menu_row = $menu_result->fetch_assoc();
    $total_menu = $menu_row['total_menu'];
} else {
    $total_menu = 0;
}

// Fetch total orders
$order_count_query = "SELECT COUNT(*) as total_orders FROM orders";
$order_result = $conn->query($order_count_query);

if ($order_result && $order_result->num_rows > 0) {
    $order_row = $order_result->fetch_assoc();
    $total_orders = $order_row['total_orders'];
} else {
    $total_orders = 0;
}

// Fetch total reviews
$review_count_query = "SELECT COUNT(*) as total_reviews FROM reviews";
$review_result = $conn->query($review_count_query);

if ($review_result && $review_result->num_rows > 0) {
    $review_row = $review_result->fetch_assoc();
    $total_reviews = $review_row['total_reviews'];
} else {
    $total_reviews = 0;
}

// Fetch sales data for the current year, aggregated by month
$sales_data_query = "
    SELECT 
        DATE_FORMAT(created_at, '%M') AS month,
        SUM(total_price) AS total_sales
    FROM 
        orders
    WHERE 
        YEAR(created_at) = YEAR(CURDATE())
    GROUP BY 
        MONTH(created_at)
    ORDER BY 
        MONTH(created_at) ASC
";

$sales_result = $conn->query($sales_data_query);
$months = [];
$sales = [];

if ($sales_result && $sales_result->num_rows > 0) {
    while ($row = $sales_result->fetch_assoc()) {
        $months[] = $row['month'];
        $sales[] = (float)$row['total_sales'];
    }
} else {
    // If no data is found, initialize with empty arrays or default values
    $months = [];
    $sales = [];
}

// Fetch the latest two reviews
$latest_reviews_query = "
    SELECT 
        id,
        name,
        date_of_visit,
        feedback,
        food_quality,
        service,
        value,
        cleanliness,
        speed
    FROM 
        reviews
    ORDER BY 
        id DESC
    LIMIT 2
";

$reviews_result = $conn->query($latest_reviews_query);

$latest_reviews = [];

if ($reviews_result && $reviews_result->num_rows > 0) {
    while ($row = $reviews_result->fetch_assoc()) {
        // Calculate average rating
        $average_rating = ($row['food_quality'] + $row['service'] + $row['value'] + $row['cleanliness'] + $row['speed']) / 5;
        $average_rating = round($average_rating * 2) / 2; // Round to nearest 0.5

        // Add to latest_reviews array
        $latest_reviews[] = [
            'name' => htmlspecialchars($row['name']),
            'date_of_visit' => date('Y-m-d', strtotime($row['date_of_visit'])),
            'feedback' => htmlspecialchars($row['feedback']),
            'average_rating' => $average_rating,
        ];
    }
} else {
    // No reviews found
    $latest_reviews = [];
}

$conn->close(); // Close the connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Burger Bliss</title>
    <link href="https://fonts.googleapis.com/css?family=Lobster|Roboto:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include "admin_navbar.php"?>

    <!-- Main Content Area -->
    <div class="main-content">
        <h2>Welcome to the Dashboard</h2>
        <p>Manage your restaurant efficiently.</p>

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="card">
                <div class="card-icon">
                    <i class="fa-solid fa-shopping-cart"></i>
                </div>
                <div class="card-info">
                    <h3>Orders</h3>
                    <p><?php echo htmlspecialchars($total_orders); ?></p>
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <div class="card-info">
                    <h3>Menu Items</h3>
                    <p><?php echo htmlspecialchars($total_menu); ?></p>
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <div class="card-info">
                    <h3>Reviews</h3>
                    <p><?php echo htmlspecialchars($total_reviews); ?></p>
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <div class="card-info">
                    <h3>Reservations</h3>
                    <p>60</p>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="chart-container">
                <h3>Sales Overview</h3>
                <canvas id="salesChart"></canvas>
            </div>
            <div class="chart-container">
                <h3>Monthly Reservations</h3>
                <canvas id="reservationsChart"></canvas>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="recent-section">
            <h3>Recent Orders</h3>
            <table class="recent-orders">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total ($)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>#1001</td>
                        <td>John Doe</td>
                        <td>Burger, Fries</td>
                        <td>25.00</td>
                        <td><span class="status pending">Pending</span></td>
                    </tr>
                    <tr>
                        <td>#1002</td>
                        <td>Jane Smith</td>
                        <td>Pizza, Soda</td>
                        <td>30.00</td>
                        <td><span class="status completed">Completed</span></td>
                    </tr>
                    <!-- Add more orders as needed -->
                </tbody>
            </table>
        </div>

        <!-- Recent Reviews -->
        <div class="recent-section">
            <h3>Recent Reviews</h3>
            <?php if (!empty($latest_reviews)): ?>
            <table class="recent-reviews">
                <thead>
                    <tr>
                        <th>Reviewer</th>
                        <th>Average Rating</th>
                        <th>Comment</th>
                        <th>Date of Visit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latest_reviews as $review): ?>
                    <tr>
                        <td><?php echo $review['name']; ?></td>
                        <td><?php echo $review['average_rating']; ?> <i class="fa-solid fa-star"></i></td>
                        <td><?php echo $review['feedback']; ?></td>
                        <td><?php echo $review['date_of_visit']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>No recent reviews available.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Chart.js Scripts -->
    <script>
        // Fetch the PHP arrays and convert them to JavaScript arrays
        const salesLabels = <?php echo json_encode($months); ?>;
        const salesData = <?php echo json_encode($sales); ?>;

        // Initialize Sales Overview Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Sales ($)',
                    data: salesData,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    },
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales ($)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Month'
                        }
                    }
                }
            }
        });

        // Monthly Reservations Chart  example
        const reservationsCtx = document.getElementById('reservationsChart').getContext('2d');
        const reservationsChart = new Chart(reservationsCtx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Reservations',
                    data: [50, 60, 70, 80, 65, 75, 85],
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</body>
</html>
