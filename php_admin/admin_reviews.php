<?php
// admin_reviews.php

session_start();

include 'db_connection.php'; // Include the database connection

// Handle Approve/Reject Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['review_id']) && isset($_POST['action'])) {
        $review_id = intval($_POST['review_id']);
        $action = $_POST['action'];

        if ($action === 'approve') {
            $stmt = $conn->prepare("UPDATE `reviews` SET `status` = 'approved' WHERE `id` = ?");
        } elseif ($action === 'reject') {
            $stmt = $conn->prepare("UPDATE `reviews` SET `status` = 'rejected' WHERE `id` = ?");
        }

        if (isset($stmt) && $stmt) {
            $stmt->bind_param("i", $review_id);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Review has been " . ($action === 'approve' ? "approved." : "rejected.");
            } else {
                $_SESSION['error'] = "Failed to update review status.";
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Invalid action.";
        }
    }
    header("Location: admin_reviews.php");
    exit();
}

// Fetch all reviews
$reviews = [];
$result = $conn->query("SELECT * FROM `reviews` ORDER BY `created_at` DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    $result->free();
} else {
    $_SESSION['error'] = "Error fetching reviews.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Reviews - Burger Bliss</title>
    <link href="https://fonts.googleapis.com/css?family=Lobster|Roboto:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="../css/admin_navbar.css">
    <link rel="stylesheet" href="../css/admin_reviews.css"> 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Include Bootstrap CSS for better styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">

</head>
<body>
    <?php include "admin_navbar.php"?>
        <!-- Display Messages -->

        <!-- Main Content Area -->
        <div class="main-content container-fluid">
        <h2>Manage Reviews</h2>
        <p>Approve or reject customer reviews.</p>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success message">
                <?php 
                    echo htmlspecialchars($_SESSION['message']); 
                    unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger message">
                <?php 
                    echo htmlspecialchars($_SESSION['error']); 
                    unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        <!-- Reviews Table -->
        <div class="table-responsive">
            <table id="reviewsTable" class="reviews-table table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date of Visit</th>
                        <th>Phone Number</th>
                        <th>Feedback</th>
                        <th>Ratings</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($review['id']); ?></td>
                                <td style="white-space: nowrap;"><?php echo htmlspecialchars($review['name']); ?></td>
                                <td><?php echo htmlspecialchars($review['email']); ?></td>
                                <td><?php echo htmlspecialchars(date("F j, Y", strtotime($review['date_of_visit']))); ?></td>
                                <td><?php echo htmlspecialchars($review['phone_number']); ?></td>
                                <td><?php echo nl2br(htmlspecialchars($review['feedback'])); ?></td>
                                <td class="ratings">
                                    <?php
                                        $categories = [
                                            'Food Quality' => $review['food_quality'],
                                            'Service' => $review['service'],
                                            'Value' => $review['value'],
                                            'Cleanliness' => $review['cleanliness'],
                                            'Speed' => $review['speed']
                                        ];
                                        foreach ($categories as $category => $rating) {
                                            echo '<strong>' . htmlspecialchars($category) . ':</strong> ';
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $rating) {
                                                    echo '<i class="fas fa-star text-warning"></i>';
                                                } else {
                                                    echo '<i class="far fa-star text-warning"></i>';
                                                }
                                            }
                                            echo '<br>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        $status = strtolower($review['status']);
                                        $badge_class = '';
                                        switch ($status) {
                                            case 'approved':
                                                $badge_class = 'status-approved';
                                                break;
                                            case 'rejected':
                                                $badge_class = 'status-rejected';
                                                break;
                                            case 'pending':
                                            default:
                                                $badge_class = 'status-pending';
                                                break;
                                        }
                                    ?>
                                    <span class="status-badge <?php echo $badge_class; ?>">
                                        <?php echo ucfirst($review['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(date("F j, Y, g:i a", strtotime($review['created_at']))); ?></td>
                                <td class="action-buttons">
                                    <?php if ($review['status'] === 'pending'): ?>
                                        <!-- Approve Button -->
                                        <form method="POST" action="admin_reviews.php" class="action-form">
                                            <input type="hidden" name="review_id" value="<?php echo htmlspecialchars($review['id']); ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn action-btn btn-approve" onclick="return confirm('Are you sure you want to approve this review?');">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>
                                        <!-- Reject Button -->
                                        <form method="POST" action="admin_reviews.php" class="action-form">
                                            <input type="hidden" name="review_id" value="<?php echo htmlspecialchars($review['id']); ?>">
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="btn action-btn btn-reject" onclick="return confirm('Are you sure you want to reject this review?');">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </form>
                                    <?php else: echo strtoupper($status)?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">No reviews found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable with custom dom to add the refresh button
            var table = $('#reviewsTable').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 10,
                "responsive": true,
                "dom": '<"d-flex justify-content-between align-items-center"lfB>tip', // Custom layout
                "initComplete": function () {
                    // Add Refresh Button
                    $('.dataTables_filter').append(
                        '<button id="refreshBtn" class="btn btn-refresh ms-2"><i class="fas fa-sync-alt"></i> Refresh</button>'
                    );
                    // Customize Search Input
                    $('.dataTables_filter label').contents().filter(function() {
                        return this.nodeType === 3; // Remove default "Search:" text
                    }).remove();

                    // Add placeholder to search input
                    $('.dataTables_filter input').attr('placeholder', 'Search Here').addClass('custom-search');

                    // Wrap input and icon
                    $('.dataTables_filter input').wrap('<div class="search-box"></div>');
                    $('.dataTables_filter input').attr('placeholder', 'Search Here').addClass('custom-search');
                    $('.custom-search').after('<i class="fas fa-search search-icon"></i>');
                }
            });

            // Refresh Button Click Event
            $(document).on('click', '#refreshBtn', function () {
                location.reload(); // Reload the page
            });
        });
    </script>

</body>
</html>
