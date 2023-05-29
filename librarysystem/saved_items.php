<?php
// Assume you have a database connection
$mysqli = require __DIR__ . "/database.php";

// Retrieve the saved items for the current user
$user_id = 1; // Replace with the actual user ID or retrieve it dynamically based on the logged-in user
$stmt = $mysqli->prepare("SELECT * FROM saved_items WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$saved_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Saved Items</title>
    <link rel="stylesheet" type="text/css" href="style/saved_items.css">
</head>
<body>
    <h1>Saved Items</h1>

    <?php if (!empty($saved_items)): ?>
        <table>
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <!-- Add more columns if needed -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($saved_items as $item): ?>
                    <tr>
                        <td><?php echo $item['id']; ?></td>
                        <td><?php echo $item['title']; ?></td>
                        <td><?php echo $item['description']; ?></td>
                        <!-- Display more data columns if needed -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No saved items found.</p>
    <?php endif; ?>
</body>
</html>
