<?php
// admin_orders.php
session_start();

// Include database connection
include 'db_connection.php'; // Ensure this file contains your DB credentials


// Fetch orders from the database
$sql = "SELECT orders.*, users.username FROM orders LEFT JOIN users ON orders.user_id = users.id ORDER BY orders.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Orders</title>
    <link href="https://fonts.googleapis.com/css?family=Lobster|Roboto:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="../css/admin_orders.css">
    <!-- Include Bootstrap CSS for better styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS for advanced table features -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

</head>
<body>
    <?php include "admin_navbar.php"?>

    <!-- Main Content Area -->
    <div class="main-content container-fluid">
        <h2>Admin Orders</h2>
        <p>Manage restaurant's orders efficiently.</p>
        <table id="ordersTable" class="order-table table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Order Type</th>
                    <th>Total Cost</th>
                    <th>Order Status</th>
                    <th>Payment Status</th>
                    <th>Payment Mode</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($order = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['order_id'] ?? ''); ?></td>
                            <td>
                                <?php 
                                    echo !empty($order['username']) ? htmlspecialchars($order['username']) : 'Guest';
                                ?>
                            </td>
                            <td class="order-type"><?php echo ($order['order_type'] === 'takeaway') ? 'Takeaway' : 'Dine-In'; ?></td>
                            <td>$<?php echo number_format($order['total_price'] ?? 0, 2); ?></td>
                            <td>
                                <span class="<?php 
                                    switch($order['order_status']) {
                                        case 'Pending':
                                            echo 'status-pending';
                                            break;
                                        case 'Preparing':
                                            echo 'status-preparing';
                                            break;
                                        case 'Delivering':
                                            echo 'status-delivering';
                                            break;
                                        case 'Completed':
                                            echo 'status-completed';
                                            break;
                                        case 'Cancelled':
                                            echo 'status-cancelled';
                                            break;
                                        default:
                                            echo '';
                                    }
                                ?>">
                                    <?php echo htmlspecialchars($order['order_status'] ?? ''); ?>
                                </span>
                            </td>
                            <td>
                                <span class="<?php 
                                    switch($order['payment_status']) {
                                        case 'Paid':
                                            echo 'status-completed';
                                            break;
                                        case 'Unpaid':
                                            echo 'status-pending';
                                            break;
                                        case 'Refunded':
                                            echo 'status-cancelled';
                                            break;
                                        default:
                                            echo '';
                                    }
                                ?>">
                                    <?php echo htmlspecialchars($order['payment_status'] ?? ''); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(ucfirst($order['payment_method'] ?? '')); ?></td>
                            <td><?php echo htmlspecialchars($order['created_at'] ?? ''); ?></td>
                            <td>
                            <a href="admin_order_details.php?order_id=<?php echo $order['id']; ?>" class="btn btn-view btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                                <button class="btn btn-status btn-sm change-status" data-order-id="<?php echo $order['id']; ?>">
                                    <i class="fas fa-edit"></i> Status
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="viewDetailsModalLabel">Order Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Order details will be loaded here via AJAX -->
            <div id="orderDetailsContent"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Change Status Modal -->
    <div class="modal fade" id="changeStatusModal" tabindex="-1" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form id="changeStatusForm">
              <div class="modal-header">
                <h5 class="modal-title" id="changeStatusModalLabel">Change Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <input type="hidden" name="order_id" id="changeStatusOrderId">
                  <div class="mb-3">
                      <label for="orderStatus" class="form-label">Order Status</label>
                      <select class="form-select" id="orderStatus" name="order_status" required>
                          <option value="Pending">Pending</option>
                          <option value="Preparing">Preparing</option>
                          <option value="Delivering">Delivering</option>
                          <option value="Completed">Completed</option>
                          <option value="Cancelled">Cancelled</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label for="paymentStatus" class="form-label">Payment Status</label>
                      <select class="form-select" id="paymentStatus" name="payment_status" required>
                          <option value="Unpaid">Unpaid</option>
                          <option value="Paid">Paid</option>
                          <option value="Refunded">Refunded</option>
                      </select>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Status</button>
              </div>
          </form>
        </div>
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
            // Initialize DataTable
            $('#ordersTable').DataTable({
                "order": [[7, "desc"]], // Order by Created At descending
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

            // Handle Change Status button click
            $('.change-status').on('click', function() {
                var orderId = $(this).data('order-id');
                $('#changeStatusOrderId').val(orderId);
                // Optionally, fetch current status via AJAX and set the select values
                // For simplicity, we'll leave the selects with their default options
                $('#changeStatusModal').modal('show');
            });

            // Handle Change Status form submission
            $('#changeStatusForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'admin_update_status.php',
                    type: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function() {
                        alert('Failed to update order status.');
                    }
                });
            });
        });
    </script>
</body>
</html>
