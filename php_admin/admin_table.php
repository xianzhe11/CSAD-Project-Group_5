<?php  
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table_number'])) {
    $tableNumber = intval($_POST['table_number']);
    $deleteQuery = "DELETE FROM orders WHERE table_number = ? AND total_price = 0.00";

    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $tableNumber);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}

$tables = [];
$result = $conn->query("SELECT table_number FROM orders WHERE total_price = 0.00 ");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tables[] = $row['table_number'];
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bliss Burger - Table Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Lobster|Roboto:400,500&display=swap" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <!-- External CSS Files -->
    <link rel="stylesheet" href="../css/admin_live.css"> 

    <script>

        function removeTable(tableNumber) {
            let button = document.getElementById('table-' + tableNumber);

            if (!button) return;
            
            // Send AJAX request to delete the table row
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "", true); // Same file
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    let response = JSON.parse(xhr.responseText);
                    if (response.status === "success") {
                        button.style.backgroundColor = "green"; // Indicate attended
                        button.disabled = true; // Disable button after handling
                    } else {
                        alert("Error removing table: " + response.message);
                    }
                }
            };
            xhr.send("table_number=" + tableNumber);
            }

            function checkForNewOrders() {
                let xhr = new XMLHttpRequest();
                xhr.open("GET", "admin_check.php", true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        let response;
                        try {
                        response = JSON.parse(xhr.responseText);
                    } catch (error) {
                        console.error("JSON Parsing Error:", error);
                        return;
                    }

                    console.log("Server Response:", response); // Debugging log

                    if (response.new_orders) {
                        console.log("New order detected! Reloading...");
                        location.reload(); // Refresh the page
                    }
                }
            };
            xhr.send();
        }

            // Check for new orders every 5 seconds
            setInterval(checkForNewOrders, 5000);

            window.onload = function() {
                const tablesWithZeroPrice = <?php echo json_encode($tables); ?>;

            // Highlight tables with total_price = 0.00
            tablesWithZeroPrice.forEach(tableNumber => {
                let button = document.getElementById('table-' + tableNumber);
                if (button) {
                    button.style.backgroundColor = 'yellow';
                }
                });
            };
    </script>
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <?php include "admin_navbar.php"?>

    <!-- Main Content -->
    <div class="main-content container-fluid">
        <h1>Admin Tables</h1>
        <p style="padding-bottom:15px;">Attend to tables</p>

        <!-- Manage Tables Layout -->
        <section id="manage-tables" class="manage-tables">
            <div class="table-column">
                <button id="table-1" class="table-btn" onclick="removeTable(1)">1</button>
                <button id="table-4" class="table-btn rectangle-table" onclick="removeTable(4)">4</button>
                <button id="table-7" class="table-btn rectangle-table" onclick="removeTable(7)">7</button>
            </div>
            <div class="table-column">
                <button id="table-2" class="table-btn" onclick="removeTable(2)">2</button>
                <button id="table-5" class="table-btn rectangle-table" onclick="removeTable(5)">5</button>
                <button id="table-8" class="table-btn rectangle-table" onclick="removeTable(8)">8</button>
            </div>
            <div class="table-column">
                <button id="table-3" class="table-btn" onclick="removeTable(3)">3</button>
                <button id="table-6" class="table-btn rectangle-table" onclick="removeTable(6)">6</button>
                <button id="table-9" class="table-btn rectangle-table" onclick="removeTable(9)">9</button>
            </div>
        </section>
    </main>
</div>

</body>
</html> 
