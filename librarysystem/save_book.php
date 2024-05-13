<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Optionally redirect to login page or handle not logged-in scenario
    header('Location: login.php');
    exit();
}

// Check if the book_id is provided
if (!isset($_POST['book_id'])) {
    echo "Error: Book ID is required.";
    exit();
}

$book_id = $_POST['book_id'];
$user_id = $_SESSION['user_id'];

// Database connection
$mysqli = require __DIR__ . "/database.php";

// Verify the book exists
$stmt = $mysqli->prepare("SELECT title FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Error: Book not found.";
    exit();
}
$book = $result->fetch_assoc();

// Insert into saved_items table
$stmt = $mysqli->prepare("INSERT INTO saved_items (user_id, book_id, title) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE title = VALUES(title)");
$stmt->bind_param("iis", $user_id, $book_id, $book['title']);
if ($stmt->execute()) {
    // Redirect to a page or display success message
    header("Location: user_dashboard.php?success=1&book_id=" . urlencode($book_id));
    exit();
} else {
    echo "Error saving the book: " . $mysqli->error;
}

$stmt->close();
?>
