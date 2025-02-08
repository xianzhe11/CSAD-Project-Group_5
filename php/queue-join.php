<?php
include 'server-queue.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Queue</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url(https://img.freepik.com/free-photo/vintage-old-rustic-cutlery-dark_1220-4886.jpg);
            background-size: cover;
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100vh;
            justify-content: center;
        }

        .container {
            background: white;
            color: #333;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
        }

        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            background: #e60000;
            color: white;
            font-size: 18px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #c00000;
        }

        .admin-btn {
            margin-top: 15px;
            background: #5a3d8a;
        }

        .admin-btn:hover {
            background: #422d6a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Join the Queue</h1>      
        <form method="POST" action="server-queue.php">
            <label for="name">Name:</label>
            <input type="text" name="name" required>

            <label for="contact">Contact Number:</label>
            <input type="tel" name="contact" required>

            <label for="persons">Number of People:</label>
            <input type="number" name="persons" value="1" min="1" required>

            <button type="submit" name="submit">Join Queue</button>
        </form>

        <!--<button class="admin-btn" onclick="window.location.href='admin.php'">Admin Panel</button>-->
    </div>
</body>
</html>
