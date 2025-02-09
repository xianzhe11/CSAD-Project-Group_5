<?php  
session_start();

include 'db_connection.php';
$per_page = 20;
$count = $conn->query("SELECT * FROM users");
$pages = ceil(mysqli_num_rows($count) / $per_page);
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $per_page;

$users = $conn->query("SELECT * FROM users LIMIT $start, $per_page");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bliss Burger - Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Lobster|Roboto:400,500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="../css/admin_users.css"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>
<body>
<div class="container">

    <?php include "admin_navbar.php" ?>


    <div class="main-content container-fluid">
        <h1>Admin Users</h1>
        <p style="padding-bottom:15px;">Check and view user accounts</p>

        <table id="users-table" class="users-table table-striped table-bordered">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Password</th>
                    <th>Points</th>
                    <th>Created on</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users->num_rows > 0): ?>
                    <?php $x = 1; ?>
                    <?php while ($row = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?= $x++ ?></td>
                            <td><?= htmlspecialchars($row['username']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['pass']) ?></td>
                            <td><?= htmlspecialchars($row['points']) ?></td>
                            <td><?= htmlspecialchars($row['created_at']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No users found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {
        var table = $('#users-table').DataTable({
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
        $(document).on('click', '#refreshBtn', function() {
            location.reload();
        });
    });
</script>
</body>
</html>
