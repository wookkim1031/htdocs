<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_POST['magazine_id'])) {
    echo "Error: Magazine ID is required.";
    exit();
}

$magazine_id = $_POST['magazine_id'];
$user_id = $_SESSION['user_id'];

$mysqli = require __DIR__ . "/database.php";

$stmt = $mysqli->prepare("SELECT title FROM magazines WHERE id = ?");
$stmt->bind_param("i", $magazine_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    echo "Error: Magazine not found.";
    exit();
}
$magazine = $result->fetch_assoc();

$stmt = $mysqli->prepare("INSERT INTO saved_items (user_id, magazine_id, title) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title)");
$stmt->bind_param("iis", $user_id, $magazine_id, $magazine['title']);

if ($stmt->execute()) {
    header("Location: user_dashboard.php?success=1&magazine_id=" . urlencode($magazine_id));
    exit();
} else {
    echo "Error saving the magazine: " . $mysqli->error;
}

$stmt->close();
?>