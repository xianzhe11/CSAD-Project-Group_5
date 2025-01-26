<?php
// progressbar.php

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
<div>
    <?php if ($current_step == 1): ?>
        <!-- Set Active Circle -->     
    <?php elseif ($current_step == 2): ?>
        <!-- Set Active Circle --> 
    <?php elseif ($current_step == 3): ?> 
        <!-- Set Active Circle -->
    <?php endif; ?>
</div>

</body>
</html>
