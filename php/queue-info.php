<?php
include 'server-queue.php';

if (isset($_GET['queueNumber'])) {
    $queueNumber = $_GET['queueNumber'];
    $queueInfo = getQueueInfo($queueNumber);

    if ($queueInfo) {
        $queueId = $queueInfo['id'];
        $groupsAhead = $queueInfo['groups_ahead'];
        $estimatedWaitingTime = $groupsAhead * 15;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5">
    <title>Queue Info</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url(https://img.freepik.com/free-photo/vintage-old-rustic-cutlery-dark_1220-4886.jpg);
            background-size: cover;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
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

        p {
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Queue Information</h1>
        <?php if ($queueInfo): ?>
            <p>Queue Number: <strong><?= $queueId ?></strong></p>
            <p>Groups Ahead: <strong><?= $groupsAhead ?></strong></p>
            <p>Estimated Waiting Time: <strong><?= $estimatedWaitingTime ?> minutes</strong></p>
        <?php else: ?>
            <p>No queue information found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
