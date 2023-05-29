<?php
session_start(); // Start the session for user authentication

$searchErr = '';
$books_details = '';
$magazines_details = '';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect the user to the login page or display an error message
    header("Location: login.php"); // Replace with your login page URL
    exit();
}

if (isset($_POST['save'])) {
    $search = !empty($_POST['search']) ? $_POST['search'] : '';
    $mysqli = require __DIR__ . "/database.php";
    
    // Search in the "books" table
    $stmt = $mysqli->prepare("SELECT * FROM books LEFT JOIN status ON books.status = status.id WHERE title LIKE ? OR author LIKE ? OR year LIKE ? OR publisher LIKE ?");
    $stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
    $books_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Search in the "magazines" table
    $stmt = $mysqli->prepare("SELECT * FROM magazines WHERE title LIKE ? OR jahrgang LIKE ? OR volumes LIKE ? OR standort LIKE ?");
    $stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
    $magazines_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

if (isset($_POST['save_item_id'])) {
    $item_id = $_POST['save_item_id'];
    $user_id = $_SESSION['user_id']; // Get the user ID from the session

    // Save the item with the given item_id and user_id to the database
    $stmt = $mysqli->prepare("INSERT INTO saved_items (user_id, item_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $item_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to the saved_items.php page after saving the item
    header("Location: saved_items.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="style/search_books_index.css">
<link rel="stylesheet" type="text/css" href="style/search_results.css">

<head>
    <title>Search</title>
    <link rel="icon" href="/librarysystem/image/IMSA-LOGO.png">

</head>

<?php include 'navbar.php' ?>

<body>

    <div class="container">
        <form action="search_results.php" method='POST' class="search-bar">
            <input class="search-input" type="text" name="search" id="search" placeholder="z.B. Bücher, Magazines">
            <button type="submit" name="save"><img src="../librarysystem/image/search.svg" alt="search"></button>
        </form>
    </div>

    <div class="results-container">
        <div class="column books-column">
            <h2>Books</h2>
            <table>
                <thead>
                    <tr>
                        <th>title</th>
                        <th>author</th>
                        <th>year</th>
                        <th>publisher</th>
                        <th>status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_POST['save']) && !empty($_POST['search'])) {
                        if (!$books_details) {
                            echo '<tr><td colspan="5" class="no-data-found">No data found</td></tr>';
                        } else {
                            foreach ($books_details as $book) {
                                ?>
                                <tr>
                                    <td><?php echo $book['title']; ?></td>
                                    <td><?php echo $book['author']; ?></td>
                                    <td><?php echo $book['year']; ?></td>
                                    <td><?php echo $book['publisher']; ?></td>
                                    <td><?php echo $book['status']; ?></td>
                                    <td>
                                        <form action="search_results.php" method="POST">
                                        <input type="hidden" name="save_item_id" value="<?php echo $book['id']; ?>">
                                        <button type="submit" name="save">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="column magazines-column">
            <h2>Magazines</h2>
            <table>
                <thead>
                    <tr>
                        <th>title</th>
                        <th>jahrgang</th>
                        <th>volumes</th>
                        <th>standort</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_POST['save']) && !empty($_POST['search'])) {
                        if (!$magazines_details) {
                            echo '<tr><td colspan="4" class="no-data-found">No data found</td></tr>';
                        } else {
                            foreach ($magazines_details as $magazine) {
                                ?>
                                <tr>
                                    <td><?php echo $magazine['title']; ?></td>
                                    <td><?php echo $magazine['jahrgang']; ?></td>
                                    <td><?php echo $magazine['volumes']; ?></td>
                                    <td><?php echo $magazine['standort']; ?></td>
                                    <td>
                                        <form action="search_results.php" method="POST">
                                            <input type="hidden" name="save_item_id" value="<?php echo $magazine['ID']; ?>">
                                            <button type="submit">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>
