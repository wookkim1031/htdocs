<?php
$mysqli = require __DIR__ . "/database.php";

$min_year = isset($_GET['min_year']) ? $_GET['min_year'] : '';
$max_year = isset($_GET['max_year']) ? $_GET['max_year'] : '';

$sql = "SELECT *
        FROM magazines ";

if (isset($_GET['alphabet'])) {
    $alphabet = $_GET['alphabet'];
    $sql .= "WHERE title LIKE '" . $alphabet . "%' ";
}

if (!empty($min_year)) {
    $sql .= "AND jahrgang >= " . $min_year . " ";
}
if (!empty($max_year)) {
    $sql .= "AND jahrgang <= " . $max_year . " ";
}

if (!empty($_GET['standort'])) {
    $standort = $_GET['standort'];
    $sql .= "AND standort = '" . $standort . "' ";
}

$sql .= "ORDER BY title ASC";

$result = $mysqli->query($sql);
$count = $result->num_rows;

$sql_alphabets = "SELECT DISTINCT LEFT(title, 1) AS alphabet FROM magazines ORDER BY alphabet ASC";
$result_alphabets = $mysqli->query($sql_alphabets);

$sql_locations = "SELECT DISTINCT standort FROM magazines";
$result_location = $mysqli->query($sql_locations);


?>
<!DOCTYPE html>
<html>
<head>
    <title>Magazines</title>
    <link rel="icon" href="/librarysystem/image/IMSA-LOGO.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital@1&family=PT+Serif:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style/magazines.css">
    <link rel="stylesheet" type="text/css" href="style/footnotes.css">
    <script src="./js/books.js"></script>
</head>
<body>
    <?php include 'navbar.php' ?>
    <div class="a-z">
        <h2>A-Z Datenbanken</h2>
        <p>Suche das Buch in der Datenbank</p>
    </div>

    <div class="alphabet-navigation">
        <?php
        while ($row_alphabet = $result_alphabets->fetch_assoc()) {
            $alphabet = $row_alphabet['alphabet'];
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
                        <br>
                <button class="show-filter" onclick="toggleFilter('standort')">Location Filter <img src="/librarysystem/image/angledown.svg" alt="arrow"></button>
                <div class="standort-filter">
                    <form action="" method="get" name="locationFilterForm" id="locationFilterForm">
                        <label for="standort"> </label>
                            <select name="standort" id="standort" class="filter-select">
                        
                        <option value="">Option</option>
                        <?php 
                            while ($row_location = $result_location->fetch_assoc()) {
                            $location = $row_location['standort'];
                            $isLocationSelected = (isset($_GET['standort']) &&  $_GET['standort'] == $location) ? 'checked' : '';
                            echo "<option value='" . $location . "' " . $isLocationSelected . ">" . $location . "</option>";
                         } ?>
                        </select>
                        <br>
                        <input type="hidden" name="alphabet" value="<?php echo isset($_GET['alphabet']) ? $_GET['alphabet'] : ''; ?>">
                        <button type="submit" form="locationFilterForm" class="filter-button">Apply</button>
                    </form>
                </div>
            </div>
    </div>

    <table>
        <div class="container">
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <tr>
                        <td><img src="/librarysystem/image/newspaper-solid.svg" alt="magazine"></td>
                        <td>
                            <div class="books-details">
                                <div class="book-title"><?php echo $row['title']; ?></div>
                                <div class="book-info"><?php echo $row['jahrgang']; ?></div>
                                <div class="book-info"><?php echo $row['volumes']; ?></div>
                                <div class="book-info"><?php echo $row['standort']; ?></div>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </div>
    </div>
    </table>

    <script src="./js/books.js"></script>
    <div id="footnotes">
        <?php include 'footnotes.php' ?>
    </div>
</body>
</html>
