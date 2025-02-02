<?php
require_once 'db_connection.php'; // Ensure this sets up $conn
$rsvmsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $contact = $conn->real_escape_string($_POST['contact']);
    $reservedDate = $conn->real_escape_string($_POST['reservedDate']);
    $reservedTime = $conn->real_escape_string($_POST['reservedTime']);
    $noOfGuests = (int)$_POST['noOfGuests'];

    // Insert query
    $sql = "INSERT INTO reservation (name, email, contact, date_rsv, time_rsv,guests) 
            VALUES ('$name', '$email', '$contact', '$reservedDate', '$reservedTime', $noOfGuests)";
           

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        $rsvmsg = "Reservation confirmed for $name on $reservedDate at $reservedTime.";
    } else {
        $rsvmsg = "Could not place reservation. Please try again";
    }
    session_start();
    $_SESSION['rsvmsg']=$rsvmsg;

    //redirect back to home page
    header("Location: index.php#reservation");
    exit();
}

// Close the connection
$conn->close();
?>

