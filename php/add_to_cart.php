<?php
    // add_to_cart.php

    session_start();

    // Check if the form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve item details from POST
        $item_id = isset($_POST['item_id']) ? intval($_POST['item_id']) : 0;
        $item_name = isset($_POST['item_name']) ? $_POST['item_name'] : '';
        $item_description = isset($_POST['item_description']) ? $_POST['item_description'] : '';
        $item_price = isset($_POST['item_price']) ? floatval($_POST['item_price']) : 0.00;
        $item_image = isset($_POST['item_image']) ? $_POST['item_image'] : '';

        if ($item_id > 0 && !empty($item_name)) {
            // Initialize the cart if it doesn't exist
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Check if the item is already in the cart
            if (isset($_SESSION['cart'][$item_id])) {
                // If it is, increase the quantity
                $_SESSION['cart'][$item_id]['quantity'] += 1;
            } else {
                // If not, add it to the cart with quantity 1
                $_SESSION['cart'][$item_id] = [
                    'id' => $item_id,
                    'name' => $item_name,
                    'description' => $item_description,
                    'price' => $item_price,
                    'image' => $item_image,
                    'quantity' => 1
                ];
            }

            // Optionally, set a success message
            $_SESSION['success_message'] = "{$item_name} has been added to your cart.";

        } else {
            // Optionally, set an error message
            $_SESSION['error_message'] = "Invalid item selected.";
        }
    }

    // Redirect back to the menu page or cart page
    header('Location: menu.php'); // Change to 'cart.php' if you want to redirect to the cart
    exit();
    ?>