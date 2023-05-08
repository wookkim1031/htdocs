<?php
$searchErr = '';
$books_details = '';

if (isset($_POST['save'])) {
    $search = !empty($_POST['search']) ? $_POST['search'] : '';
    $mysqli = require __DIR__ . "/../database.php";
    $stmt = $mysqli->prepare("SELECT * FROM books LEFT JOIN status ON books.status = status.id WHERE title LIKE ? OR author LIKE ? OR year LIKE ? OR publisher LIKE ?");
    $stmt->execute(["%$search%", "%$search%", "%$search%", "%$search%"]);
    $books_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>

<link rel="stylesheet" type="text/css" href="style/search_books_index.css">
<head>
    <title>Search</title> 
</head>

<body>
    <div class="container">
        <form action="#" method='POST' class="search-bar">
                <input type="text" name="search" id="search" placeholder="search by title, author, year or publisher">
                <button type="submit" name="save"><img src="../librarysystem/image/search.svg" alt="search"></button>
        </form>
    </div>

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
                    echo '<tr>No data found</tr>';
                } else {
                    foreach ($books_details as $book) {
                        ?>
                        <tr>
                            <td><?php echo $book['title']; ?></td>
                            <td><?php echo $book['author']; ?></td>
                            <td><?php echo $book['year']; ?></td>
                            <td><?php echo $book['publisher']; ?></td>
                            <td><?php echo $book['status']; ?></td>
                        </tr>
                    <?php
                    }
                }
            }
            ?>
        </tbody>
    </table>
</body>

</html>
