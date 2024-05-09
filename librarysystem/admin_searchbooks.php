<?php
session_start(); 

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit;
}

$mysqli = require __DIR__ . "/database.php"; 

$searchErr = '';
$searchType = $_POST['searchType'] ?? ($_SESSION['searchType'] ?? 'books');
$search = $_POST['search_books'] ?? ($_SESSION['search_books'] ?? '');

$_SESSION['search_books'] = $search;
$_SESSION['searchType'] = $searchType;

$books_details = [];
$magazines_details = [];


$booksPerPage = 40;
$totalBooksQuery = "SELECT COUNT(*) AS total FROM books
JOIN mediatypes ON books.type = mediatypes.id
JOIN status ON status.id = books.status 
JOIN location ON location.id = books.location";
$totalBooksResult = $mysqli->query($totalBooksQuery);
$totalBooksRow = $totalBooksResult->fetch_assoc();
$totalBooks = $totalBooksRow['total'];

$totalPages = ceil($totalBooks / $booksPerPage);

$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($currentPage - 1 ) * $booksPerPage;

$status_query = "SELECT id, status FROM status";
$status_result = $mysqli->query($status_query);
$statuses = $status_result->fetch_all(MYSQLI_ASSOC);

if ($searchType === 'books') {
        $stmt = $mysqli->prepare( "SELECT COUNT(*) AS total FROM books
        JOIN mediatypes ON books.type = mediatypes.id
        JOIN status ON status.id = books.status 
        JOIN location ON location.id = books.location
        WHERE title LIKE ? OR author LIKE ? OR year LIKE ? OR publisher LIKE ?");
        $searchTerm = "%$search%";
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    } else if ($searchType === 'magazines') {
        $stmt = $mysqli->prepare( "SELECT COUNT(*) AS total FROM magazines
        WHERE title LIKE ? OR jahrgang LIKE ? OR volumes LIKE ? OR standort LIKE ?");
        $searchTerm = "%$search%";
        $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
    }

if($stmt) {
    $stmt->execute();
    $stmt->bind_result($totalRecords); 
    $stmt->fetch();
    $stmt->close();

    $totalPages = ceil($totalRecords / $booksPerPage);
}

if (isset($_POST['save']) || !isset($_POST['save'])) {
    $search = !empty($_POST['search_books']) ? trim($_POST['search_books']) : '';

    if ($searchType === 'books') {
        $stmt = $mysqli->prepare( "SELECT books.*, status.status AS status_name, mediatypes.type AS mediatype_name, location.name AS location_name, location.room AS location_room
            FROM books 
            LEFT JOIN status ON books.status = status.id 
            LEFT JOIN mediatypes ON books.type = mediatypes.id
            LEFT JOIN location ON books.location = location.id
            WHERE title LIKE ? OR author LIKE ? OR year LIKE ? OR publisher LIKE ? LIMIT ? OFFSET ?");
    } elseif ($searchType === 'magazines') {
        $stmt = $mysqli->prepare("SELECT * FROM magazines WHERE title LIKE ? OR jahrgang LIKE ? OR volumes LIKE ? OR standort LIKE ? LIMIT ? OFFSET ?");
    }
    $searchTerm = "%$search%";
    $stmt->bind_param("ssssii", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $booksPerPage, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($searchType === 'books') {
        $books_details = $result->fetch_all(MYSQLI_ASSOC);
    } else if ($searchType === 'magazines') {
        $magazines_details = $result->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search</title>
    <link rel="stylesheet" type="text/css" href="style/search_books_admin.css">
    <link rel="icon" href="/librarysystem/image/IMSA-LOGO.png">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="flexbox">
        <div class="search-options">
            <form action="admin_searchbooks.php" method="POST" class="search-bar-section">
                <input type="hidden" name="searchType" value="books">
                <button type="submit" name="save" class="<?php echo (isset($_POST['searchType']) && $_POST['searchType'] == 'books') ? 'active-search' : ''; ?>">Search Books</button>
            </form>
            <form action="admin_searchbooks.php" method="POST" class="search-bar-section">
                <input type="hidden" name="searchType" value="magazines">
                <button type="submit" name="save" class="<?php echo (isset($_POST['searchType']) && $_POST['searchType'] == 'magazines') ? 'active-search' : ''; ?>">Search Magazines</button>
            </form>
        </div>

        <form action="admin_searchbooks.php" method="POST" class="search-bar">
            <input type="hidden" name="searchType" value="<?php echo $searchType; ?>">
            <input class="search-input" type="text" name="search_books" placeholder="e.g. Books, Magazines">
            <button type="submit" name="save"><img src="../librarysystem/image/search.svg" alt="search"></button>
        </form>
    </div>

    <div class="results-container">
        <?php if ($searchType === 'books' && !empty($books_details)) : ?>
            <div class="column books-column">
                <h2>Books</h2>

                <?php foreach ($books_details as $book) : ?>
                    <div id="book-<?php echo $book['id']; ?>" class="show-book-form">
                        <div class="input-container">
                            <label>Title:</label>
                            <span><?php echo htmlspecialchars($book['title']); ?></span>
                        </div>

                        <div class="input-container">
                            <label>Author:</label>
                            <span><?php echo htmlspecialchars($book['author']); ?></span>
                        </div>

                        <div class="input-container">
                            <label>Year:</label>
                            <span><?php echo htmlspecialchars($book['year']); ?></span>
                        </div>

                        <div class="input-container">
                            <label>Edition:</label>
                            <span><?php echo htmlspecialchars($book['edition']); ?></span>
                        </div>

                        <div class="input-container">
                            <label>Publisher:</label>
                            <span><?php echo htmlspecialchars($book['publisher']); ?></span>
                        </div>

                        <div class="input-container">
                            <label>Status Name:</label>
                            <span><?php echo htmlspecialchars($book['status_name']); ?></span>
                        </div>

                        <div class="input-container">
                            <label>isbn:</label>
                            <span><?php echo htmlspecialchars($book['isbn']); ?></span>
                        </div>

                        <button onclick="showEditForm(<?php echo $book['id']; ?>)">Edit</button>
                    </div>  
                    <div id="edit-form-<?php echo $book['id']; ?>" style="display:none;">
                        <form id="edit_books" action="edit_items.php" method="POST" class="edit-book-form">
                            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">

                            <div class="input-container">
                                <label for="title-<?php echo $book['id']; ?>">Title:</label>
                                <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>">
                            </div>

                            <div class="input-container">
                                <label for="title-<?php echo $book['id']; ?>">Author:</label>
                                <input type="text" name="author" value="<?php echo htmlspecialchars($book['author']); ?>">
                            </div>

                            <div class="input-container">
                                <label for="title-<?php echo $book['id']; ?>">Year:</label>
                                <input type="text" name="year" value="<?php echo htmlspecialchars($book['year']); ?>">
                            </div>

                            <div class="input-container">
                                <label for="title-<?php echo $book['edition']; ?>">Edition:</label>
                                <input type="text" name="edition" value="<?php echo htmlspecialchars($book['edition']); ?>">
                            </div>

                            <div class="input-container">
                                <label for="title-<?php echo $book['id']; ?>">Publisher:</label>
                                <input type="text" name="publisher" value="<?php echo htmlspecialchars($book['publisher']); ?>">
                            </div>

                            <div class="input-container">
                                <label for="status-<?php echo $book['id']; ?>">Status:</label>
                                <select id="status-<?php echo $book['id']; ?>" name="status">
                                    <?php foreach($statuses as $status) {
                                        $selected = ($status['id'] == $book['status']) ? 'selected' : '';
                                        echo "<option value=\"" . htmlspecialchars($status['id']) . "\" $selected>" . htmlspecialchars($status['status']) . "</option>";
                                    } ?>
                                </select>
                            </div>

                            <div class="input-container">
                                <label for="title-<?php echo $book['id']; ?>">isbn:</label>
                                <input type="text" name="isbn" value="<?php echo htmlspecialchars($book['isbn']); ?>">
                            </div>

                            <button type="submit" name="update_book">Update</button>
                            <button type="button" onclick="hideEditForm(<?php echo $book['id']; ?>)">Cancel</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($searchType === 'books') : ?>
            <p>Bitte klicken Sie auf Book oder Magazine oder schreiben Sie der Name des Buches oder Magazines auf der Suche.</p>
        <?php endif; ?>

        <!--Magazines Edit Form --> 
        <?php if ($searchType === 'magazines' && !empty($magazines_details)) : ?>
    <div class="column books-column">
        <h2>Magazines</h2>
        <?php foreach ($magazines_details as $magazine) : ?>
            <div id="book-<?php echo $magazine['ID']; ?>" class="show-book-form">
                        <div class="input-container">
                            <label>Title:</label>
                            <span><?php echo htmlspecialchars($magazine['title']); ?></span>
                        </div>

                        <div class="input-container">
                            <label>Author:</label>
                            <span><?php echo htmlspecialchars($magazine['jahrgang']); ?></span>
                        </div>

                        <div class="input-container">
                            <label>Year:</label>
                            <span><?php echo htmlspecialchars($magazine['volumes']); ?></span>
                        </div>

                        <div class="input-container">
                            <label>Publisher:</label>
                            <span><?php echo htmlspecialchars($magazine['standort']); ?></span>
                        </div>

                        <div class="input-container">
                            <label>Path:</label>
                            <span><?php echo htmlspecialchars($magazine['image_path'])?></span>
                        </div>

                        <button onclick="showEditForm(<?php echo $magazine['ID']; ?>)">Edit</button>
                    </div>  
            <div id="edit-form-<?php echo $magazine['ID']; ?>" style="display:none;">
                <form action="edit_items.php" method="POST" class="edit-book-form">
                <input type="hidden" name="magazine_id" value="<?php echo $magazine['ID']; ?>">
                    <div class="input-container">
                        <label>Title:</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($magazine['title']); ?>">
                    </div>

                    <div class="input-container">
                        <label>Jahrgang:</label>
                        <input type="text" name="jahrgang" value="<?php echo htmlspecialchars($magazine['jahrgang']); ?>">
                    </div>

                    <div class="input-container">
                        <label>Volumes:</label>
                        <input type="text" name="volumes" value="<?php echo htmlspecialchars($magazine['volumes']); ?>">
                    </div>
                    
                    <div class="input-container">
                        <label>Standort:</label>
                        <input type="text" name="standort" value="<?php echo htmlspecialchars($magazine['standort']); ?>">
                    </div>

                    <div class="input-container">
                        <label>Path:</label>
                        <input type="text" name="standort" value="<?php echo htmlspecialchars($magazine['image_path']); ?>"> 
                    </div>
                    
                    <button type="submit" name="update_magazine">Update</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
    <?php elseif ($searchType === 'magazines') : ?>
        <p>No magazines found.</p>
    <?php endif; ?>

            <button onclick="scrollToBottom()" id="scrollToBottomBtn" title="Go to Bottom">Scroll to Bottom</button>
            <button onclick="scrollToTop()" id="scrollToTopBtn" title="Go to top">Scroll to Top</button>

    <div class="pagination"> 
        <?php 
            $range = 8;
            $start = ((floor(($currentPage - 1) / $range)) * $range)  + 1;
            $end = $start + $range - 1;
            $end = ($totalPages < $end) ? $totalPages : $end;

            echo '<div class="pagination">'; 
            if ($start > 1) {
                echo '<a href="?page=' . ($start - 1) . '">&laquo; Previous</a>';
            }
            
            for ($page = $start; $page <= $end; $page++) {
                echo '<a href="?page=' . $page . '"';
                if ($page == $currentPage) {
                    echo ' class="active"';
                }
                echo '>' . $page . '</a>';
            }
        
            if ($end < $totalPages) {
                echo '<a href="?page=' . ($end + 1) . '">Next &raquo;</a>';
            }
            echo '</div>';
        ?>
    </div>   
</body>

<?php if(isset($_GET['success'])): ?>
    <div class="success-message">
        <p>Erfolgreiche Änderung</p>
        <ul>
            <li class="info-item"><span class="label">Title:</span> <span class="value"><?php echo htmlspecialchars($_GET['title']); ?></span></li>
            
            <!-- Conditional display based on type -->
            <?php if($_GET['type'] == 'book'): ?>
                <li class="info-item"><span class="label">Author:</span> <span class="value"><?php echo htmlspecialchars($_GET['author']); ?></span></li>
                <li class="info-item"><span class="label">Edition:</span> <span class="value"><?php echo htmlspecialchars($_GET['edition']); ?></span></li>
                <li class="info-item"><span class="label">Year:</span> <span class="value"><?php echo htmlspecialchars($_GET['year']); ?></span></li>
                <li class="info-item"><span class="label">Publisher:</span> <span class="value"><?php echo htmlspecialchars($_GET['publisher']); ?></span></li>
                <li class="info-item"><span class="label">ISBN:</span> <span class="value"><?php echo htmlspecialchars($_GET['isbn']); ?></span></li>
            <?php elseif($_GET['type'] == 'magazine'): ?>
                <li class="info-item"><span class="label">Jahrgang:</span> <span class="value"><?php echo htmlspecialchars($_GET['jahrgang']); ?></span></li>
                <li class="info-item"><span class="label">Volumes:</span> <span class="value"><?php echo htmlspecialchars($_GET['volumes']); ?></span></li>
                <li class="info-item"><span class="label">Standort:</span> <span class="value"><?php echo htmlspecialchars($_GET['standort']); ?></span></li>
            <?php endif; ?>
        </ul>
        <span class="close-btn">&times;</span>
    </div>
<?php elseif(isset($_GET['error'])): ?>
    <div class="error-message">
        <h4>Error</h4>
        <p><?php echo htmlspecialchars($_GET['error']); ?></p>
        <span class="close-btn">&times;</span>
    </div>
<?php endif; ?>

    <script>
        function showEditForm(bookId) {
            var form = document.getElementById('edit-form-' + bookId);
            var displayInfo = document.getElementById('book-' + bookId);
            if (form.style.display === 'none') {
                form.style.display = 'block';
                displayInfo.style.display = 'none';
            } else {
                form.style.display = "none";
                displayInfo.style.display='block';
            }
        }
        function hideEditForm(bookId) {
            var hideForm = document.getElementById('edit-form-' + bookId);
            hideForm.style.display = 'none';

            var displayHideInfo = document.getElementById('book-' + bookId);
            if(displayHideInfo) {
                displayHideInfo.style.display="block";
            }
        }
    </script>
    <script src="./js/books.js"></script>

</html>