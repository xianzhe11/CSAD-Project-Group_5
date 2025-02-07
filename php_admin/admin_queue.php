<?php
include '../php/server-queue.php';

$queueRecords = getAllQueueRecords();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-size: cover;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .container {
            background: white;
            color: #333;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            display: inline-block;
            width: 90%;
            max-width: 1000px;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #5a3d8a;
            color: white;
        }

        .delete-btn {
            background: red;
            color: white;
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .delete-btn:hover {
            background: darkred;
        }
    </style>
</head>
<body>
    <?php include "admin_navbar.php"?>
    <div class="container">
        <h1>Admin Panel - Queue Management</h1>

        <table>
            <tr>
                <th>Queue No</th>
                <th>Name</th>
                <th>Contact No</th>
                <th>No. of Persons</th>
                <th>Action</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($queueRecords)): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['contact_number']) ?></td>
                    <td><?= $row['number_of_persons'] ?></td>
                    <td>
                        <a href="server.php?del=<?= $row['id'] ?>" class="delete-btn">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

