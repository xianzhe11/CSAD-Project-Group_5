<?php
session_start();

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'restaurant_food';
$port = 3306; 

$db = mysqli_connect($host, $user, $password, $database, $port);

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($db, $_POST['name']);
    $contact = mysqli_real_escape_string($db, $_POST['contact']);
    $numberOfPersons = (int) $_POST['persons'];

    $result = mysqli_query($db, "SELECT MAX(groups) AS max_group FROM QueueDetails");
    $row = mysqli_fetch_assoc($result);
    $newGroup = ($row['max_group'] !== null) ? $row['max_group'] + 1 : 1;

    $sql = "INSERT INTO QueueDetails (groups, name, contact_number, number_of_persons) 
              VALUES ('$newGroup', '$name', '$contact', '$numberOfPersons')";

    if (mysqli_query($db, $sql)) {
        $_SESSION['message'] = "Successfully joined the queue!";
        header("Location: queue-info.php?queueNumber=" . mysqli_insert_id($db));
        exit();
    } else {
        $_SESSION['message'] = "Failed to join the queue!";
    }
}

function getQueueInfo($queueNumber) {
    global $db;

    $sql = "SELECT * FROM QueueDetails WHERE id = '$queueNumber'";
    $result = mysqli_query($db, $sql);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $groupQuery = "SELECT COUNT(*) AS groups_ahead FROM QueueDetails WHERE groups < '{$row['groups']}'";
        $groupResult = mysqli_query($db, $groupQuery);
        $groupRow = mysqli_fetch_assoc($groupResult);

        $row['groups_ahead'] = $groupRow['groups_ahead'];
        return $row;
    }
    return null;
}

function getAllQueueRecords() {
    global $db;
    $sql = "SELECT * FROM QueueDetails ORDER BY groups ASC";
    return mysqli_query($db, $sql);
}

if (isset($_GET['del'])) {
    $id = $_GET['del'];
    $sql = "DELETE FROM QueueDetails WHERE id = '$id'";
    
    if (mysqli_query($db, $sql)) {
        $_SESSION['message'] = "Queue entry deleted!";
    } else {
        $_SESSION['message'] = "Failed to delete queue entry!";
    }

    header('location: ../php_admin/admin_queue.php');
    exit();
}
?>
