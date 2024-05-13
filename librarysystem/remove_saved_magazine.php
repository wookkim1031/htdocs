<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['magazine_id'])) {
    $user_id = $_SESSION['user_id'];
    $magazine_id = $_POST['magazine_id'];

    $mysqli = require __DIR__ . "/database.php";

    $stmt = $mysqli->prepare("DELETE FROM saved_items WHERE user_id = ? AND magazine_id = ?");
    $stmt->bind_param("ii", $user_id, $magazine_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<p class='success'>" . $_SESSION['success_message'] . "</p>";
        unset($_SESSION['success_message']); 
    } else {
        echo "<p class='error'>" . $_SESSION['error_message'] . "</p>";
        unset($_SESSION['error_message']); 
    }
    $stmt->close();
    header('Location: user_dashboard.php');
    exit();
} else {
    header('Location: user_dashboard.php');
    exit();
}
?>
