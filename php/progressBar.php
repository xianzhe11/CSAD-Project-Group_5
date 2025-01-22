<?php
// progressbar.php

// Determine the current step based on the page
// You can set this variable dynamically based on your routing logic
// For example, you might set it based on the URL or session data

// Example using a GET parameter 'step'. Adjust as needed.
// = isset($_GET['step']) ? (int)$_GET['step'] : 1;

// Ensure current_step is between 1 and 3
if ($current_step < 1) $current_step = 1;
if ($current_step > 3) $current_step = 3;

// Define steps with labels
$steps = [
    1 => 'Shopping Cart',
    2 => 'Payment Method',
    3 => 'Order Details'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Progress Bar Example</title>
    <link rel="stylesheet" href="progressbar.css">
</head>
<body>

<div class="progressbar">
    <?php foreach ($steps as $step_number => $label): ?>
        <div class="step <?php echo ($current_step >= $step_number) ? 'active' : ''; ?>">
            <div class="circle"><?php echo $step_number; ?></div>
            <div class="label"><?php echo $label; ?></div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Example content to demonstrate the current step -->
<div>
    <?php if ($current_step == 1): ?>
        
        <!-- Shopping Cart content here -->
    <?php elseif ($current_step == 2): ?>
        
        <!-- Payment Method content here -->
    <?php elseif ($current_step == 3): ?>
        
        <!-- Order Details content here -->
    <?php endif; ?>
</div>

</body>
</html>
