<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_POST['book_id'])) {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];

    $mysqli = require __DIR__ . "/database.php";

    $stmt = $mysqli->prepare("DELETE FROM saved_items WHERE user_id = ? AND book_id = ?");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();

    // Check if the DELETE operation was successful
    if ($stmt->affected_rows > 0) {
        $_SESSION['success_message'] = 'Book removed successfully.';
    } else {
        $_SESSION['error_message'] = 'Failed to remove the book.';
    }
    $stmt->close();
    // Redirect to user_dashboard.php with a success or error message
    header('Location: user_dashboard.php');
    exit();
} else {
    // Redirect to user_dashboard.php if accessed without a POST request
    header('Location: user_dashboard.php');
    exit();
}
?>
