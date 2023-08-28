<?php
session_start();

if (isset($_SESSION["user_id"]) && isset($_POST["new_name"])) {
    $mysqli = require __DIR__ . "/database.php";
    
    $user_id = $_SESSION["user_id"]; // Store user ID in a variable
    $new_name = $_POST["new_name"]; // Get the updated name from the form

    // Update the user's name in the database
    $stmt = $mysqli->prepare("UPDATE users SET name = ? WHERE id = ?");
    $stmt->bind_param("si", $new_name, $user_id);
    $stmt->execute();
    $stmt->close();

    // Update the $user variable with the new name
    $user['name'] = $new_name;
}

// Redirect back to the profile page
header("Location: profile.php");
exit();
?>