<?php
session_start();
include 'db_connection.php';

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    // ADD NEW MENU ITEM
    if ($_POST['action'] == 'add') {
        // Retrieve and sanitize submitted values
        $catName     = $_POST['catName'];
        $itemName    = $_POST['itemName'];
        $description = $_POST['description'];
        $price       = $_POST['price'];
        $status      = $_POST['status'];
        
        // Process uploaded image file
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed   = array('jpg', 'jpeg', 'png', 'gif');
            $fileName  = $_FILES['image']['name'];
            $fileTmp   = $_FILES['image']['tmp_name'];
            $fileExt   = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (in_array($fileExt, $allowed)) {
                // Create a unique file name to prevent overwriting
                $newFileName = uniqid() . '.' . $fileExt;
                $destination = '../food_images/' . $newFileName;
                if (move_uploaded_file($fileTmp, $destination)) {
                    $image = $destination;
                } else {
                    $_SESSION['error'] = "Failed to move uploaded file.";
                    header("Location: admin_menu.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG & GIF allowed.";
                header("Location: admin_menu.php");
                exit();
            }
        } else {
            $image = ''; // Optionally set a default image if desired
        }
        
        // Insert new menu item into the database
        $stmt = $conn->prepare("INSERT INTO menu_items (catName, itemName, description, price, image, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiss", $catName, $itemName, $description, $price, $image, $status);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Menu item added successfully!";
        } else {
            $_SESSION['error'] = "Failed to add menu item.";
        }
        $stmt->close();
        header("Location: admin_menu.php");
        exit();

    // UPDATE (EDIT) EXISTING MENU ITEM
    } elseif ($_POST['action'] == 'edit') {
        $id          = $_POST['id'];
        $catName     = $_POST['catName'];
        $itemName    = $_POST['itemName'];
        $description = $_POST['description'];
        $price       = $_POST['price'];
        $status      = $_POST['status'];
        // Use existing image by default
        $image = $_POST['existing_image'];
        
        // Process uploaded image file if a new one is provided
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $allowed   = array('jpg', 'jpeg', 'png', 'gif');
            $fileName  = $_FILES['image']['name'];
            $fileTmp   = $_FILES['image']['tmp_name'];
            $fileExt   = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (in_array($fileExt, $allowed)) {
                $newFileName = uniqid() . '.' . $fileExt;
                $destination = '../food_images/' . $newFileName;
                if (move_uploaded_file($fileTmp, $destination)) {
                    $image = $destination;
                } else {
                    $_SESSION['error'] = "Failed to move uploaded file.";
                    header("Location: admin_menu.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG & GIF allowed.";
                header("Location: admin_menu.php");
                exit();
            }
        }
        
        // Update the menu item
        $stmt = $conn->prepare("UPDATE menu_items SET catName=?, itemName=?, description=?, price=?, image=?, status=? WHERE id=?");
        $stmt->bind_param("sssissi", $catName, $itemName, $description, $price, $image, $status, $id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Menu item updated successfully!";
        } else {
            $_SESSION['error'] = "Failed to update menu item.";
        }
        $stmt->close();
        header("Location: admin_menu.php");
        exit();

    // DELETE SINGLE MENU ITEM 
    } elseif ($_POST['action'] == 'delete') {
        $id = intval($_POST['id']);
        // Fetch the image path before deletion
        $stmt = $conn->prepare("SELECT image FROM menu_items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($imagePath);
        $stmt->fetch();
        $stmt->close();

        // Delete the menu item
        $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            // Delete the corresponding image if it exists
            if (!empty($imagePath) && file_exists($imagePath)) {
                unlink($imagePath);  // ✅ This deletes the image from the folder
            }
            $_SESSION['message'] = "Menu item deleted successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete menu item.";
        }
        $stmt->close();
        header("Location: admin_menu.php");
        exit();

    // DELETE MULTIPLE MENU ITEMS
    } elseif ($_POST['action'] == 'delete_selected') {
        if (isset($_POST['ids']) && is_array($_POST['ids'])) {
            foreach($_POST['ids'] as $id){
                $id = intval($id);

                // Fetch the image path before deletion
                $stmt = $conn->prepare("SELECT image FROM menu_items WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->bind_result($imagePath);
                $stmt->fetch();
                $stmt->close();

                // Delete the menu item
                $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();

                // Delete the image file if it exists
                if (!empty($imagePath) && file_exists($imagePath)) {
                    unlink($imagePath);  // ✅ Deletes the image file
                }
            }
            $_SESSION['message'] = "Selected menu items deleted successfully!";
        } else {
            $_SESSION['error'] = "No items selected.";
        }
        header("Location: admin_menu.php");
        exit();
    }
}

// Fetch all menu items ordered by category then id
$sql = "SELECT * FROM menu_items ORDER BY catName ASC, id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Menu</title>
  <link href="https://fonts.googleapis.com/css?family=Lobster|Roboto:400,500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="../css/admin_navbar.css">
  <link rel="stylesheet" href="../css/admin_menu.css">
  <!-- Include Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
  <?php include "admin_navbar.php" ?>

  <!-- Main Content Area -->
  <div class="main-content container-fluid">
    <h2>Admin Menu</h2>
    <p>Manage your restaurant's food menu items below.</p>

    <!-- Display Success/Error Messages -->
    <?php if(isset($_SESSION['message'])): ?>
      <div class="alert alert-success">
        <?php 
          echo htmlspecialchars($_SESSION['message']);
          unset($_SESSION['message']);
        ?>
      </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?>
      <div class="alert alert-danger">
        <?php 
          echo htmlspecialchars($_SESSION['error']);
          unset($_SESSION['error']);
        ?>
      </div>
    <?php endif; ?>

    <!-- Multi-select Delete Button Container (will show only if >1 item is selected) -->
    <div class="multiselect-container">
      <button id="deleteSelectedBtn" class="btn btn-danger">Delete Selected</button>
    </div>

    <!-- Menu Items Table -->
    <table id="menuTable" class="menu-table table table-striped">
      <thead>
        <tr>
          <th><input type="checkbox" id="selectAll"></th>
          <th>ID</th>
          <th>Category</th>
          <th>Item Name</th>
          <th>Description</th>
          <th>Price</th>
          <th>Image</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if($result && $result->num_rows > 0): ?>
          <?php while($item = $result->fetch_assoc()): ?>
            <tr>
              <td>
                <input type="checkbox" class="select-item" value="<?php echo htmlspecialchars($item['id']); ?>">
              </td>
              <td><?php echo htmlspecialchars($item['id']); ?></td>
              <td><?php echo htmlspecialchars($item['catName']); ?></td>
              <td><?php echo htmlspecialchars($item['itemName']); ?></td>
              <td><?php echo htmlspecialchars($item['description']); ?></td>
              <td>$<?php echo number_format($item['price'], 2); ?></td>
              <td>
                <?php if (!empty($item['image'])): ?>
                  <img src="../food_images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['itemName']); ?>">
                <?php endif; ?>
              </td>
              <td><?php echo htmlspecialchars($item['status']); ?></td>
              <td>
                <!-- Edit Button with data attributes for use in the Edit modal -->
                <button class="btn btn-primary btn-sm edit-item-btn edit-menu" 
                  data-id="<?php echo htmlspecialchars($item['id']); ?>" 
                  data-catname="<?php echo htmlspecialchars($item['catName']); ?>" 
                  data-itemname="<?php echo htmlspecialchars($item['itemName']); ?>" 
                  data-description="<?php echo htmlspecialchars($item['description']); ?>" 
                  data-price="<?php echo htmlspecialchars($item['price']); ?>" 
                  data-status="<?php echo htmlspecialchars($item['status']); ?>"
                  data-image="<?php echo htmlspecialchars($item['image']); ?>"
                >
                  <i class="fas fa-edit"></i> Edit
                </button>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="9" class="text-center">No menu items found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

  <!-- Add Menu Item Modal -->
  <div class="modal fade" id="addMenuModal" tabindex="-1" aria-labelledby="addMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Note the enctype for file upload -->
        <form method="POST" action="admin_menu.php" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="addMenuModalLabel">Add New Menu Item</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="action" value="add">
            <div class="mb-3">
              <label for="catName" class="form-label">Category</label>
              <select name="catName" id="catName" class="form-select" required>
                <option value="">Select Category</option>
                <option value="Pizza">Pizza</option>
                <option value="Beverages">Beverages</option>
                <option value="Burgers">Burgers</option>
                <option value="Appetizers">Appetizers</option>
                <option value="Seasonal">Seasonal</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="itemName" class="form-label">Item Name</label>
              <input type="text" name="itemName" id="itemName" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label">Description</label>
              <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
              <label for="price" class="form-label">Price</label>
              <input type="number" step="0.01" name="price" id="price" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="image" class="form-label">Food Image</label>
              <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
            </div>
            <div class="mb-3">
              <label for="status" class="form-label">Status</label>
              <select name="status" id="status" class="form-select" required>
                <option value="Available">Available</option>
                <option value="Unavailable">Unavailable</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add Item</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Menu Item Modal -->
  <div class="modal fade" id="editMenuModal" tabindex="-1" aria-labelledby="editMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- The form below will update the menu item -->
        <form method="POST" action="admin_menu.php" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="editMenuModalLabel">Edit Menu Item</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="edit-id">
            <!-- Save the current image path -->
            <input type="hidden" name="existing_image" id="edit-existing-image">
            <div class="mb-3">
              <label for="edit-catName" class="form-label">Category</label>
              <select name="catName" id="edit-catName" class="form-select" required>
                <option value="">Select Category</option>
                <option value="Pizza">Pizza</option>
                <option value="Beverages">Beverages</option>
                <option value="Burgers">Burgers</option>
                <option value="Appetizers">Appetizers</option>
                <option value="Seasonal">Seasonal</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="edit-itemName" class="form-label">Item Name</label>
              <input type="text" name="itemName" id="edit-itemName" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="edit-description" class="form-label">Description</label>
              <textarea name="description" id="edit-description" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
              <label for="edit-price" class="form-label">Price</label>
              <input type="number" step="0.01" name="price" id="edit-price" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="edit-image" class="form-label">Food Image (Leave blank to keep current)</label>
              <input type="file" name="image" id="edit-image" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">
              <label for="edit-status" class="form-label">Status</label>
              <select name="status" id="edit-status" class="form-select" required>
                <option value="Available">Available</option>
                <option value="Unavailable">Unavailable</option>
              </select>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Update Item</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Hidden form for multi-delete -->
  <form id="deleteSelectedForm" method="POST" action="admin_menu.php" style="display:none;">
    <input type="hidden" name="action" value="delete_selected">
  </form>

  <!-- Include jQuery, Bootstrap JS, and DataTables JS -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
  <script>
    $(document).ready(function() {
    // Initialize DataTable for the menu table with custom controls
    $('#menuTable').DataTable({
        "order": [[2, "asc"]], // Order by Category by default
        "responsive": true,
        "columnDefs": [
            { "orderable": false, "targets": 0 } // Disable sorting for the first column (Select All checkbox)
        ],
        "dom": '<"d-flex justify-content-between align-items-center"lfB>tip',
        "initComplete": function () {
          // Add Refresh Button next to the search box
          $('.dataTables_filter').append(
            '<button id="refreshBtn" class="btn btn-refresh ms-2"><i class="fas fa-sync-alt"></i> Refresh</button>'
          );
          // Remove default "Search:" label text
          $('.dataTables_filter label').contents().filter(function() {
            return this.nodeType === 3;
          }).remove();
          // Customize search input: add placeholder and wrap with icon
          $('.dataTables_filter input').attr('placeholder', 'Search Here').addClass('custom-search');
          $('.dataTables_filter input').wrap('<div class="search-box"></div>');
          $('.custom-search').after('<i class="fas fa-search search-icon"></i>');

        // Add "Add New Item" Button centered below the table
        $('#menuTable_wrapper').append(`
                    <div class="add-item-container">
                        <button class="btn add-item-btn" data-bs-toggle="modal" data-bs-target="#addMenuModal">
                            <i class="fas fa-plus"></i> Add New Item
                        </button>
                    </div>
                `);
            }
        });
      
      // Refresh Button Click Event
      $(document).on('click', '#refreshBtn', function () {
        location.reload();
      });

      // Toggle checkbox when clicking the row
        $('#menuTable tbody').on('click', 'tr', function(event) {
            if (!$(event.target).is('input[type="checkbox"], button, i, label')) {
                const checkbox = $(this).find('.select-item');
                checkbox.prop('checked', !checkbox.prop('checked'));
                toggleDeleteSelected();
            }
        });
      
      // "Select All" checkbox functionality
      $('#selectAll').on('click', function() {
        $('.select-item').prop('checked', this.checked);
        toggleDeleteSelected();
      });
      
      // Individual checkbox change
      $('.select-item').on('change', function() {
        toggleDeleteSelected();
      });
      
      // Function to show/hide the Delete Selected button
      function toggleDeleteSelected() {
        var count = $('.select-item:checked').length;
        if (count >= 1) {
          $('.multiselect-container').show();
        } else {
          $('.multiselect-container').hide();
        }
      }
      
      // Delete Selected Button functionality
      $('#deleteSelectedBtn').on('click', function() {
        var selected = [];
        $('.select-item:checked').each(function() {
          selected.push($(this).val());
        });
        if (selected.length === 0) {
          alert('No items selected.');
        } else {
          if (confirm('Are you sure you want to delete the selected items?')) {
            // Append hidden inputs to the delete form and submit
            var form = $('#deleteSelectedForm');
            form.empty(); // Clear previous inputs
            form.append('<input type="hidden" name="action" value="delete_selected">');
            $.each(selected, function(i, id) {
              form.append('<input type="hidden" name="ids[]" value="'+ id +'">');
            });
            form.submit();
          }
        }
      });
      
      // Edit Button: Populate Edit Modal with data attributes
      $('.edit-menu').on('click', function() {
        var btn = $(this);
        $('#edit-id').val(btn.data('id'));
        $('#edit-catName').val(btn.data('catname'));
        $('#edit-itemName').val(btn.data('itemname'));
        $('#edit-description').val(btn.data('description'));
        $('#edit-price').val(btn.data('price'));
        $('#edit-status').val(btn.data('status'));
        $('#edit-existing-image').val(btn.data('image'));
        // Optionally, you could show the current image in the modal
        $('#editMenuModal').modal('show');
      });
    });
  </script>
</body>
</html>
