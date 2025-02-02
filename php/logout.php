<?php
session_start();       // Start the session to access session variables
session_unset();       // Unset all session variables
session_destroy();     // Destroy the session

// clear the session cookie for added security
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to the homepage after logout
header("Location: index.php");
exit();
?>
