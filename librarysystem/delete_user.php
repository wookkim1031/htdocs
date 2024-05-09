<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $mysqli = require __DIR__ . "/database.php";

    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        $_SESSION['message'] = "User erfolgreich gelöscht.";
    } else {
        $_SESSION['error'] = "Benutzer löschen fehlgeschlagen.";
    }

    $stmt->close();
    header("Location: admin_users.php");
    exit;
}
?>
