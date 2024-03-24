<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit;
}

$mysqli = require __DIR__ . "/database.php";

if(isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    // Make sure this matches the actual column and data type in your database
    $user_role_id = $_POST['user_role_id'];

    // Update statement should reflect the actual column names in your database
    $stmt = $mysqli->prepare("UPDATE users SET name = ?, email = ?, role_id = ? WHERE id = ?");
    $stmt->bind_param("sssi", $user_name, $user_email, $user_role_id, $user_id);
    $stmt->execute();

    // Check if the update was successful and redirect accordingly
    if($stmt->affected_rows > 0) {
        header("Location: admin_users.php?success=Update successful.");
    } else {
        header("Location: admin_users.php?error=No changes made or update failed.");
    }
    $stmt->close();
} else {
    header("Location: admin_users.php?error=Invalid request.");
    exit;
}
?>
