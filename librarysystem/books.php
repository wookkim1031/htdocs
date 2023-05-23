<?php
$results_per_page = 20; //number of results per page
$mysqli = require __DIR__ . "/database.php";

if (isset($_GET['page'])) {
    $page = $_GET["page"];
} else {
    $page = 1;
}
$start = ($page - 1) * $results_per_page;
if (isset($_GET['alphabet'])) {
    $alphabet = $_GET['alphabet'];
    $sql = "SELECT books.*, mediatypes.type AS type, status.status, location.name AS location, location.room AS room
        FROM books
        JOIN mediatypes ON books.type = mediatypes.id
        JOIN status ON status.id = books.status 
        JOIN location ON location.id = books.location
        WHERE title LIKE '" . $alphabet . "%'
        ORDER BY title ASC 
        LIMIT $start, $results_per_page";
} else {
    $sql = "SELECT books.*, mediatypes.type AS type, status.status, location.name AS location, location.room AS room
        FROM books
        JOIN mediatypes ON books.type = mediatypes.id
        JOIN status ON status.id = books.status 
        JOIN location ON location.id = books.location
        ORDER BY title ASC 
        LIMIT $start, $results_per_page";
}
$result = $mysqli->query($sql);

$count = $result->num_rows;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Books</title>
    <link rel="icon" href="/librarysystem/image/IMSA-LOGO.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital@1&family=PT+Serif:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style/books.css">
</head>
<body>
    <?php include 'navbar.php' ?>
    <div class="alphabet-navigation">
    <?php 
        $sql_alphabets = "SELECT DISTINCT LEFT(title, 1) AS alphabet FROM books ORDER BY alphabet ASC";
        $result_alphabets = $mysqli->query($sql_alphabets);

        while ($row_alphabet = $result_alphabets->fetch_assoc()) {
            $alphabet = $row_alphabet['alphabet'];
            echo "<a href='?page=1&alphabet=" . $alphabet . "'";
            if (isset($_GET['alphabet']) && $_GET['alphabet'] == $alphabet) echo " class='selected'";
            echo ">" . $alphabet . "</a>";
        }
    ?>
    </div>
    <div class="found-books-count">
            <?php echo $count . " Database found"; ?>
        </div>
    <table>
    <div class="container">
    <tbody>
        <?php 
            while($row = $result-> fetch_assoc()) {
                if(!empty($row['title'])) { ?> 
                    <tr>
                        <td> <img src="/librarysystem/image/book-solid.svg" alt="magazine"></td>
                        <td>
                            <div class="books-details">
                                <?php if((($row['type']) !== "unbekannt")) { ?>
                                    <div class="book-type"> <?php echo $row['type']; ?> </div>
                                <?php } ?>
                                <div class="book-title"> <?php echo $row['title']; ?> </div>
                                <div class="book-author"> <?php echo $row['author']; ?> </div>

                                <?php if(($row['year'] !== "0000")) {?> 
                                    <div> <?php echo $row['year']; ?> </div>
                                <?php } ?>

                                <?php if(($row['room'] !== "unbekannt")) { ?> 
                                    <div> <?php echo "Standort : ".$row['location'] ?> </div>
                                    <div> <?php echo ' Raum : '. $row['room']; ?> </div>
                                <?php } ?>
                                
                                <?php if(($row['status'] == "verfügbar")) { ?>
                                    <div class="book-status"> Verfügbar </div>
                                <?php } else { ?>
                                    <div class="book-status-not"> Nicht Verfügbar </div>
                                <?php } ?>
                                

                                <button type="submit" class="btn" onclick="openPopup()"> Hier Klicken</button>
                                
                                <div class="popup" id="popup">
                                    <img src="/librarysystem/image/book-solid.svg" alt="magazine">
                                    <h2 class="book-title-popup"> <?php echo $row['title']; ?> </h2>
                                    <div class="popup-container">
                                    <table>
                                        <thead>
                                            <tr class="popup-side-title">
                                                <th>Author</th>
                                                <th>Publisher</th>
                                                <th>Edition</th>
                                                <th>Standort</th>
                                                <th>Room</th>
                                                <th>ISBN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <tr class="popup-detail">
                                            <td> <?php echo $row['author']; ?> </td>
                                            <td> <?php echo $row['publisher']; ?> </td>
                                            <td> <?php echo $row['edition']; ?> </td>
                                            <td> <?php echo $row['location'] ?> </td>
                                            <td> <?php echo $row['room']; ?> </td>
                                            <td> <?php echo $row['isbn']; ?> </td>
                                        </tr>   
                                        </tbody>
                                    </table>  
                                    </div>                             
                                    <button type="button" onclick="closePopup()">OK</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    
            <?php
                }
            }
        ?>
    </div>
    </tbody>
    </table>
    <script src="./js/books.js"></script>

    <?php
$sql = "SELECT COUNT(ID) AS total FROM books";
$result = $mysqli -> query($sql);
$row = $result->fetch_assoc();
$total_pages = ceil($row["total"] / $results_per_page);

$visible_pages = min(5, $total_pages); // Set the maximum number of visible page numbers

$current_page = isset($_GET['page']) ? $_GET['page'] : 1; // Get the current page number

$half_visible = floor($visible_pages / 2);
$start_page = max(1, $current_page - $half_visible);
$end_page = min($total_pages, $start_page + $visible_pages - 1);
?>

<div class="pagination">
  <?php if ($current_page > 1): ?>
    <a href="?page=<?php echo ($current_page - 1); ?>">Previous</a>
  <?php endif; ?>

  <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
    <a href="?page=<?php echo $i; ?>" <?php echo ($i == $current_page) ? "class='curPage'" : ""; ?>>
      <?php echo $i; ?>
    </a>
  <?php endfor; ?>

  <?php if ($current_page < $total_pages): ?>
    <a href="?page=<?php echo ($current_page + 1); ?>">Next</a>
  <?php endif; ?>
</div>
</body>
</html>