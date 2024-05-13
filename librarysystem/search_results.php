<?php
session_start(); // Start the session for user authentication

$searchErr = '';
$books_details = '';
$magazines_details = '';

$sql = "SELECT *, image_path
        FROM magazines ";


if (isset($_POST['save'])) {
    $search = !empty($_POST['search']) ? $_POST['search'] : '';
    $mysqli = require __DIR__ . "/database.php";
    
    // Search in the "books" table
    $stmt = $mysqli->prepare("
    SELECT 
        books.id AS book_id, 
        books.title, 
        books.author, 
        books.year, 
        books.publisher,
        books.edition,
        books.location,
        location.name AS location,
        location.room AS room,
        books.isbn, 
        status.*
    FROM books 
    LEFT JOIN status ON books.status = status.id
    LEFT JOIN location ON location.id = books.location 
    WHERE 
        books.title LIKE ? 
        OR books.author LIKE ? 
        OR books.year LIKE ? 
        OR books.publisher LIKE ?
");
    $searchTerm = "%$search%";
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $books_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Search in the "magazines" table
    $stmt = $mysqli->prepare("SELECT *, magazines.id AS magazine_id FROM magazines WHERE title LIKE ? OR jahrgang LIKE ? OR volumes LIKE ? OR standort LIKE ?");
    $searchTerm = "%$search%";
    $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute(); 
    $magazines_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

//not working 
if (isset($_POST['save_item_id'])) {
    $item_id = $_POST['save_item_id'];
    $user_id = $_SESSION['user_id']; 

    $mysqli = require __DIR__ . "/database.php";

    $stmt = $mysqli->prepare("SELECT id, title FROM books WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
        $title = $book['title'];
        $stmt = $mysqli->prepare("INSERT INTO saved_items (user_id, book_id, title) VALUES (?,?,?)");
        $stmt->bind_param("iis", $user_id, $item_id, $title);
        if($stmt->execute()) {
            header("Location: saved_items.php?book_id=" . urlencode($book['id']));
            exit();
        } else {
            echo "Error saving the book: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Book not found";
    }
}

?>


<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="style/search_books_index.css">
<link rel="stylesheet" type="text/css" href="style/index.css">
<link rel="stylesheet" type="text/css" href="style/search_results.css">

<head>
    <title>Search</title>
    <link rel="icon" href="/librarysystem/image/IMSA-LOGO.png">

</head>

<?php include 'navbar.php' ?>

<body>
    <div id="loading" class="loading"></div>
    <div class="flexbox">
        <div class="search-box">
            <div class="search-title"> Bibilotheken durchensuchen </div>
            <div class="search-form">
                <form action="search_results.php" method='POST' class="search-bar">
                        <input class="search-input" type="text" name="search" id="search" placeholder="Bücher, Magazines">
                        <button type="submit" name="save"><img src="../librarysystem/image/search.svg" alt="search"></button>
                </form>
            </div>
            <br>
                <a href="/librarysystem/books.php" class="underline">zur erweiterten Suche</a>
            
        </div>
    </div>
    <div class="sort-info">Sortiert nach Relevanz</div>
    <div class="sort-info2">Sie finden nicht, was Sie brauchen? <b>abergrath@ukaachen.de</b></div>
    <div class="results-container">
        <div class="column books-column">
            <h1>Books</h1>
            <table>
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Year</th>
                        <th>Publisher</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_POST['save']) && !empty($_POST['search'])) {
                        if (!$books_details) {
                            echo '<tr><td colspan="5" class="no-data-found">keine Datei gefunden</td></tr>';
                        } else {
                            foreach ($books_details as $book) {
                                ?>
                                <tr onclick="showPopup(<?php echo $book['book_id'] ?>)">
                                    <td class="book-list"><div data-isbn="<?php echo htmlspecialchars($book['isbn']); ?>">
                                    <img src="/librarysystem/image/book-solid.svg" alt="Cover Image">
                                    </div></td>
                                    <td class="result-title"><?php echo $book['title']; ?></td>
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
        </div>

        <div class="column magazines-column">
            <h1>Magazines</h1>
            <table>
                <thead>
                    <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Jahrgang</th>
                        <th>Volumes</th>
                        <th>Standort</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_POST['save']) && !empty($_POST['search'])) {
                        if (!$magazines_details) {
                            echo '<tr><td colspan="4" class="no-data-found">Keine Datei gefunden</td></tr>';
                        } else {
                            foreach ($magazines_details as $magazine) {
                                ?>
                                <tr onclick="showMagazinePopup(<?php echo $magazine['magazine_id']?>)">
                                    <td class="magazine-list">
                                        <?php if(!empty($magazine['image_path'])): ?>
                                            <img src="<?php echo htmlspecialchars($magazine['image_path']); ?>" alt="Magazine Image">
                                        <?php else: ?>
                                            <img src="/librarysystem/image/newspaper-solid.svg" alt="magazine">
                                        <?php endif; ?>
                                    </td>
                                    <td class="result-title"><?php echo $magazine['title']; ?></td>
                                    <td><?php echo $magazine['jahrgang']; ?></td>
                                    <td><?php echo $magazine['volumes']; ?></td>
                                    <td><?php echo $magazine['standort']; ?></td>
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
    
    <div id="footnotes">
        <?php include 'footnotes.php' ?>
    </div>

    <?php

foreach ($magazines_details as $magazine) {
    ?>
    <div class="popup" id="magPopup-<?php echo $magazine['magazine_id'];?>">
        <h2 class="book-title-popup"> <?php echo $magazine['title']; ?></h2>
        <div class="popup-content">
            <span class="close-btn" onclick="closeMagazinePopup(<?php echo $magazine['magazine_id'];?>)">&times;</span>
            <div class="book-container">
                <div class="book-cover">
                                        <?php if(!empty($magazine['image_path'])): ?>
                                            <img src="<?php echo htmlspecialchars($magazine['image_path']); ?>" alt="Magazine Image">
                                        <?php else: ?>
                                            <img src="/librarysystem/image/newspaper-solid.svg" alt="magazine">
                                        <?php endif; ?>
                </div>
                <div class="book-info">
                    <div class="magazine-title"><?php echo $magazine['title']; ?></div>
                </div>
            </div>
            <h3 class="bib-info">Bibilographic Information</h3>
            <hr>
            <div class="book-details">
                <div class=info-row>
                    <div class="info-title">Title:</div>
                    <div class="info-detail"><?php echo $magazine['title']; ?></div>
                </div>
                <div class=info-row>
                    <div class="info-title">Jahrgang:</div>
                    <div class="info-detail"><?php echo $magazine['jahrgang']; ?></div>
                </div>
                <div class=info-row>
                    <div class="info-title">Volumes:</div>
                    <div class="info-detail"><?php echo $magazine['volumes']; ?></div>
                </div>
                <div class=info-row>
                    <div class="info-title">Standort:</div>
                    <div class="info-detail"><?php echo $magazine['standort']; ?></div>
                </div>
            </div>   
        </div>
    
    </div>
    <?php
}
?>

<?php
foreach ($books_details as $book) {
    ?>
    <div class="popup" id="popup-<?php echo $book['book_id']; ?>">
        <h2 class="book-title-popup"> <?php echo $book['title']; ?> </h2>
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup(<?php echo $book['book_id']; ?>)">&times;</span>
            <div class="book-container">
                <div class="book-cover" data-isbn="<?php echo htmlspecialchars($book['isbn']); ?>">
                    <img src="/librarysystem/image/book-solid.svg" alt="Cover Image" class="book-cover">
                </div>
                <div class="book-info">
                    <div class="book-author"><?php echo $book['author']; ?></div>
                    <div class="book-publisher"><?php echo $book['publisher']; ?></div>
                    <div class="book-isbn"><?php echo $book['isbn']; ?></div>
                </div>
                <form action="save_book.php" method="post">
                    <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                    <button type="submit" name="save_book">Save Book</button>
                </form>
            </div>
            <h3 class="bib-info">Bibilographic Information</h3>
            <hr>
            <div class="book-details">
                <div class="info-row">
                    <div class="info-title">Title:</div>
                    <div class="info-detail"><?php echo $book['title']; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-title">Author:</div>
                    <div class="info-detail"><?php echo $book['author']; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-title">Year:</div>
                    <div class="info-detail"><?php echo $book['year']; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-title">Publisher:</div>
                    <div class="info-detail"><?php echo $book['publisher']; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-title">Edition:</div>
                    <div class="info-detail"><?php echo $book['edition']; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-title">Location:</div>
                    <div class="info-detail"><?php echo $book['location']; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-title">Status:</div>
                    <div class="info-detail"><?php echo $book['status']; ?></div>
                </div>
                <div class="info-row">
                    <div class="info-title">ISBN:</div>
                    <div class="info-detail"><?php echo $book['isbn']; ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

    <script src="./js/search_results.js"></script>
</body>

</html>