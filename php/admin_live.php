<?php  
session_start();
require_once("db_connection.php");

$per_page = 20;
$count = $conn->query("SELECT * FROM reservation");
$pages = ceil(mysqli_num_rows($count) / $per_page);
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $per_page;

$reserve = $conn->query("SELECT * FROM reservation ORDER BY date_rsv ASC, time_rsv ASC LIMIT $start, $per_page");

$result = "";
if ($reserve->num_rows) {
    $result .= "<table class='reservation-table'>
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
                    <tbody>";
    
    $x = 1;
    while ($row = $reserve->fetch_assoc()) {
        $reserve_id = $row['id'];
        $name = $row['name'];
        $no_of_guest = $row['guests'];
        $email = $row['email'];
        $phone = $row['contact'];
        $date_res = $row['date_rsv'];
        $time = $row['time_rsv'];

        // Delete form inside the table
        $result .= "<tr>
                        <td>$x</td>
                        <td>$name</td>
                        <td>$no_of_guest</td>
                        <td>$email</td>
                        <td>$phone</td>
                        <td>$date_res</td>
                        <td>$time</td>
                        <td>
                            <form method='POST' onsubmit='return confirmDelete();'>
                                <input type='hidden' name='delete_id' value='$reserve_id'>
                                <button type='submit' class='delete-btn'>✖</button>
                            </form>
                        </td>
                    </tr>";
        $x++;
    }   
    $result .= "</tbody></table>";
} else {
    $result = "<p class='no-reservations'>No Table reservations available yet</p>";
}

// Handle Deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    if ($delete_id > 0) {
        $sql = $conn->query("DELETE FROM reservation WHERE id='$delete_id'");
        if ($sql) {
            header("location:admin_live.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Burger Haven - Table Reservations</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../css/admin_live.css">

    <script>
    // Function to confirm deletion
    function confirmDelete() {
        return confirm("Are you sure you want to delete this reservation?");
    }

    // Function to show specific sections
    function showSection(sectionId) {
        // Hide both sections initially
        document.getElementById('reservation-list').style.display = 'none';
        document.getElementById('manage-tables').style.display = 'none';

        // Display the requested section
        document.getElementById(sectionId).style.display = (sectionId === 'manage-tables') ? 'flex' : 'block';
        
        // Update the header text dynamically based on the section
        document.getElementById('header-title').innerText = (sectionId === 'manage-tables') ? 'MANAGE TABLES' : 'TABLE RESERVATIONS';
    }

    // Function to toggle the table color when clicked
    function helpTable(tableNumber) {
        let button = document.getElementById('table-' + tableNumber);
        // Toggle between green and yellow colors
        if (button.style.backgroundColor === "yellow") {
            button.style.backgroundColor = "green";
        } else {
            button.style.backgroundColor = "yellow";
        }
    }
</script>

<style>
    /* Initially hide the "Manage Tables" section */
    #manage-tables {
        display: none;
    }

    /* Reservation List section */
    #reservation-list {
        display: block;
    }

    /* Button and layout adjustments for the manage tables */
    .manage-tables {
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
        gap: 20px;
        padding: 20px;
        max-width: 1000px;
        margin: auto;
    }

    .table-btn {
        width: 125px;
        height: 125px;
        background-color: green;
        color: white;
        font-size: 18px;
        font-weight: bold;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .table-btn:hover {
        background-color: darkgreen;
    }

    .rectangle-table {
        width: 250px;
        height: 125px;
    }

    @media (max-width: 800px) {
        .table-btn {
            width: 80px;
            height: 80px;
            font-size: 14px;
        }

        .rectangle-table {
            width: 160px;
            height: 80px;
        }
    }
</style>

</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <h2>Burger Haven</h2>
        <nav>
            <ul>
                <li><a href="#" onclick="showSection('reservation-list')">Table Reservations</a></li>
                <li><a href="#" onclick="showSection('manage-tables')">Manage Tables</a></li>
            </ul>

        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header>
            <h1 id="header-title">TABLE RESERVATIONS</h1>
        </header>

        <!-- Reservation List -->
        <section id="reservation-list">
            <h2 style="padding-top:15px;padding-bottom:15px;">Reservation List</h2>
            <?php echo $result; ?>
        </section>

        <!-- Manage Tables Layout -->
        <section id="manage-tables" class="manage-tables">
            <button id="table-1" class="table-btn" onclick="helpTable(1)">1</button>
            <button id="table-2" class="table-btn" onclick="helpTable(2)">2</button>
            <button id="table-3" class="table-btn" onclick="helpTable(3)">3</button>
            <button id="table-4" class="table-btn" onclick="helpTable(4)">4</button>
            <button id="table-5" class="table-btn" onclick="helpTable(5)">5</button>
            <button id="table-6" class="table-btn" onclick="helpTable(6)">6</button>
            <button id="table-7" class="table-btn" onclick="helpTable(7)">7</button>
            <button id="table-8" class="table-btn rectangle-table" onclick="helpTable(8)">8</button> <!-- Rectangular -->
            <button id="table-9" class="table-btn rectangle-table" onclick="helpTable(9)">9</button> <!-- Rectangular -->
            <button id="table-10" class="table-btn rectangle-table" onclick="helpTable(10)">10</button> <!-- Rectangular -->
            <button id="table-11" class="table-btn rectangle-table" onclick="helpTable(11)">11</button> <!-- Rectangular -->
            <button id="table-12" class="table-btn rectangle-table" onclick="helpTable(12)">12</button> <!-- Rectangular -->
            <button id="table-13" class="table-btn rectangle-table" onclick="helpTable(13)">13</button> <!-- Rectangular -->
        </section>
    </main>
</div>

</body>
</html>
