<?php  
session_start();
include 'db_connection.php';

$per_page = 20;
$count = $conn->query("SELECT * FROM reservation");
$pages = ceil(mysqli_num_rows($count) / $per_page);
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $per_page;

$reserve = $conn->query("SELECT * FROM reservation ORDER BY date_rsv ASC, time_rsv ASC LIMIT $start, $per_page");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    if ($delete_id > 0) {
        $sql = $conn->query("DELETE FROM reservation WHERE id='$delete_id'");
        if ($sql) {
            header("location:admin_reservation.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bliss Burger - Table Reservations</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Lobster|Roboto:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="../css/admin_reservation.css"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    
</head>
<body>
<div class="container">
    <?php include "admin_navbar.php" ?>
    <div class="main-content container-fluid">
        <h1>Admin Reservations</h1>
        <p style="padding-bottom:15px;">Check and view reservations made</p>

        <table id="reservation-table" class="reservation-table table-striped table-bordered">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Name</th>
                    <th>No of Guests</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($reserve->num_rows > 0): ?>
                    <?php $x = 1; ?>
                    <?php while ($row = $reserve->fetch_assoc()): ?>
                        <tr>
                            <td><?= $x++ ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['guests']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['contact']) ?></td>
                            <td><?= htmlspecialchars($row['date_rsv']) ?></td>
                            <td><?= htmlspecialchars($row['time_rsv']) ?></td>
                            <td>
                                <form method="POST" onsubmit="return confirmDelete();">
                                    <input type="hidden" name="delete_id" value="<?= intval($row['id']) ?>">
                                    <button type="submit" class="delete-btn">✖</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No Table reservations available yet</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
    <script>
        $(document).ready(function() {
        var table = $('#reservation-table').DataTable({
            "order": [[0, "desc"]],
            "pageLength": 10,
            "responsive": true,
            "dom": '<"d-flex justify-content-between align-items-center custom-header"l>tip', 
            "initComplete": function () {

                $('.custom-header').append(
                    '<div class="refresh-container"><button id="refreshBtn" class="btn btn-refresh"><i class="fas fa-sync-alt"></i> Refresh</button></div>'
                );
            }
        });

        $(document).on('click', '#refreshBtn', function () {
            location.reload(); // Reload the page
        });
        });

        function confirmDelete() {
            return confirm("Are you sure you want to delete this reservation?");
        }
    </script>
</body>
</html>
