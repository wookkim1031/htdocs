<?php
session_start(); // Start the session for user authentication

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page or display an error message
    header("Location: login.php"); // Replace with your login page URL
    exit();
}

// Check if the book ID is provided
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
    
    // Save the book ID in the database for the current user
    $user_id = $_SESSION['user_id'];
    // Insert the book ID into the saved_items table
    $mysqli = require __DIR__ . "/database.php";
    $stmt = $mysqli->prepare("INSERT INTO saved_items (user_id, book_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $book_id);
    $stmt->execute();
    $stmt->close();
}

// Retrieve all saved books for the current user
$user_id = $_SESSION['user_id'];
$mysqli = require __DIR__ . "/database.php";
$stmt = $mysqli->prepare("SELECT book_id FROM saved_items WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$saved_books = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Saved Items</title>
    <link rel="stylesheet" type="text/css" href="style/saved_items.css">
</head>
<body>
    <h1>Saved Items</h1>

    <?php if (!empty($saved_books)): ?>
        <h2>Saved Books:</h2>
        <ul>
            <?php foreach ($saved_books as $book): ?>
                <li><?php echo $book['book_id']; ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No saved items found.</p>
    <?php endif; ?>
</body>
</html>
