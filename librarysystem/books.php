<?php
$mysqli = require __DIR__ . "/database.php";

$min_year = isset($_GET['min_year']) ? $_GET['min_year'] : '';
$max_year = isset($_GET['max_year']) ? $_GET['max_year'] : '';

if (isset($_GET['alphabet'])) {
    $alphabet = $_GET['alphabet'];
    $sql = "SELECT books.*, mediatypes.type AS type, status.status, location.name AS location, location.room AS room
        FROM books
        JOIN mediatypes ON books.type = mediatypes.id
        JOIN status ON status.id = books.status 
        JOIN location ON location.id = books.location
        WHERE title LIKE '" . $alphabet . "%'";

    if (!empty($_GET['location'])) {
        $location = $_GET['location'];
        $sql .= " AND location.id = " . $location;
    }

    if (!empty($min_year)) {
        $sql .= " AND year >= " . $min_year;
    }
    if (!empty($max_year)) {
        $sql .= " AND year <= " . $max_year;
    }

    $sql .= " ORDER BY title ASC ";
} else {
    $sql = "SELECT books.*, mediatypes.type AS type, status.status, location.name AS location, location.room AS room
        FROM books
        JOIN mediatypes ON books.type = mediatypes.id
        JOIN status ON status.id = books.status 
        JOIN location ON location.id = books.location";
    
    if (!empty($_GET['location'])) {
        $location = $_GET['location'];
        $sql .= " WHERE location.id = " . $location;
    }

    if (!empty($min_year)) {
        $sql .= " AND year >= " . $min_year;
    }
    if (!empty($max_year)) {
        $sql .= " AND year <= " . $max_year;
    }

    $sql .= " ORDER BY title ASC";
}

$result = $mysqli->query($sql);
$count = $result->num_rows;

$sql_locations = "SELECT * FROM location";
$result_locations = $mysqli->query($sql_locations);
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
    <link rel="stylesheet" type="text/css" href="style/footnotes.css">
</head>
<body>
    <?php include 'navbar.php' ?>
    <div class="a-z">
        <h2>A-Z Datenbanken</h2>
        <p>Suche das Buch in dem Datenbank</p>
    </div>
    
    <div class="alphabet-navigation">
        <?php 
        $sql_alphabets = "SELECT DISTINCT LEFT(title, 1) AS alphabet FROM books ORDER BY CASE WHEN LEFT(title, 1) REGEXP '^[0-9]' THEN 1 ELSE 0 END, alphabet ASC";
        $result_alphabets = $mysqli->query($sql_alphabets);

        $numberAlphabets = []; // Array to store number alphabets
        $letterAlphabets = []; // Array to store letter alphabets

        while ($row_alphabet = $result_alphabets->fetch_assoc()) {
            $alphabet = $row_alphabet['alphabet'];

            if (ctype_alpha($alphabet)) {
                $letterAlphabets[] = $alphabet;
            } else {
                $numberAlphabets[] = $alphabet;
            }
        }

        $numberAlphabets = array_unique($numberAlphabets);
        sort($letterAlphabets);

        echo "<a href='?page=1&alphabet=#'";
        if (isset($_GET['alphabet']) && $_GET['alphabet'] == '#') echo " class='selected'";
        echo ">#</a>";

        foreach ($letterAlphabets as $alphabet) {
            echo "<a href='?page=1&alphabet=" . $alphabet . "'";
            if (isset($_GET['alphabet']) && $_GET['alphabet'] == $alphabet) echo " class='selected'";
            echo ">" . $alphabet . "</a>";
        }
        ?>
    </div>
     
    <div class="found-books-count">
        <?php echo "Zeige Ergebnisse für " . $count . " sortiert nach Relevanz"; ?>
    </div>
    <div class="filter"> 
        <div class="filter-top">
            <h3>Sortiere nach</h3>
            <div class="filter-section">
                <div>
                    <button class="show-filter" onclick="toggleFilter('year')">Year Filter <img src="/librarysystem/image/angleright.svg" alt="arrow"> </button>
                    <div class="year-filter">
                        <form action="" method="get">
                            <label for="min_year">Min Jahr:</label>
                            <input type="number" id="min_year" name="min_year" value="<?php echo $min_year; ?>">
                            <br>
                            <label for="max_year">Max Jahr:</label>
                            <input type="number" id="max_year" name="max_year" value="<?php echo $max_year; ?>">
                            <br>
                            <button type="submit">Apply</button>
                        </form>
                    </div>
                </div>
                <div>
                    <button class="show-filter" onclick="toggleFilter('location')">Location Filter <img src="/librarysystem/image/angleright.svg" alt="arrow1"> </button>
                    <div class="location-filter">
                        <form action="" method="get">
                            <label for="location">Location</label>
                            <select name="location" id="location">
                                <option value="">All</option>
                                <?php 
                                while ($row_location = $result_locations->fetch_assoc()) {
                                    $selected = (isset($_GET['location']) && $_GET['location'] == $row_location['id']) ? "selected" : '';
                                    echo "<option value='" . $row_location['id'] . "' " . $selected . ">" . $row_location['name'] . "</option>";
                                }
                                ?>
                            </select>

                            <button type="submit">Filter</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <table id="tb">
            <div class="container-wrapper">
            <div class="container">
                <tbody>
                    <?php 
                    while ($row = $result->fetch_assoc()) {
                        if (!empty($row['title'])) { ?> 
                            <tr>
                                <td> <img src="/librarysystem/image/book-solid.svg" alt="magazine"></td>
                                <td>
                                    <div class="books-details">
                                        <?php if (($row['type']) !== "unbekannt") { ?>
                                            <div class="book-type"> <?php echo $row['type']; ?> </div>
                                        <?php } ?>
                                        <div class="book-title"> <?php echo $row['title']; ?> </div>
                                        <div class="book-publisher"> <?php echo $row['publisher']; ?> </div>
                                        <div class="book-author"> <?php echo $row['author']; ?> </div>

                                        <?php if (($row['year']) !== "0000") {?> 
                                            <div> <?php echo $row['year']; ?> </div>
                                        <?php } ?>

                                        <?php if (($row['room']) !== "unbekannt") { ?> 
                                            <div> <?php echo "Standort : ".$row['location'] ?> </div>
                                            <div> <?php echo ' Raum : '. $row['room']; ?> </div>
                                        <?php } ?>
                                        
                                        <?php if (($row['status']) == "verfügbar") { ?>
                                            <div class="book-status"> Verfügbar </div>
                                        <?php } else { ?>
                                            <div class="book-status-not"> Nicht Verfügbar </div>
                                        <?php } ?>
                                        

                                        <button type="submit" class="btn" onclick="openPopup(<?php echo $row['id'] ?>)"> Hier Klicken</button>
                                        
                                        <div class="popup" id="popup-<?php echo $row['id']; ?>">
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
                                            <button type="button" onclick="closePopup(<?php echo $row['id'] ?>)">OK</button>
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
                </div>
    </div>
                
            </table>
        
        <script src="./js/books.js"></script>

        <button onclick="scrollToTop()" id="scrollToTopBtn" title="Go to top">Top</button>
        <div class="footnotes">
            <?php include 'footnotes.php' ?>
        </div>
    </body>
</html>
