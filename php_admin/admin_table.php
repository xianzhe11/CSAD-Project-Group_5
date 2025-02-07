<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bliss Burger-Table Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Lobster|Roboto:400,500&display=swap" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <!-- External CSS Files -->
    <link rel="stylesheet" href="../css/admin_live.css"> 

    <script>
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
            <button id="table-1" class="table-btn" onclick="helpTable(1)">1</button>
            <button id="table-4" class="table-btn rectangle-table" onclick="helpTable(4)">4</button>
            <button id="table-7" class="table-btn rectangle-table" onclick="helpTable(7)">7</button>
            </div>
            <div class="table-column">
            <button id="table-2" class="table-btn" onclick="helpTable(2)">2</button>
            <button id="table-5" class="table-btn rectangle-table" onclick="helpTable(5)">5</button>
            <button id="table-8" class="table-btn rectangle-table" onclick="helpTable(8)">8</button>
            </div>
            <div class="table-column">
            <button id="table-3" class="table-btn" onclick="helpTable(3)">3</button>
            <button id="table-6" class="table-btn rectangle-table" onclick="helpTable(6)">6</button>
            <button id="table-9" class="table-btn rectangle-table" onclick="helpTable(9)">9</button>
            </div>
        </section>
    </main>
</div>

</body>
</html>
