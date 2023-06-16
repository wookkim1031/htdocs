<?php

$mysqli = require __DIR__ . "/database.php";


$min_year = isset($_GET['min_year']) ? $_GET['min_year'] : '';
$max_year = isset($_GET['max_year']) ? $_GET['max_year'] : '';

if (isset($_GET['alphabet'])) {
    $alphabet = $_GET['alphabet'];
    $sql = "SELECT *
            FROM magazines 
            WHERE title LIKE '" . $alphabet . "%'";

            if (!empty($min_year)) {
                $sql .= " AND jahrgang >= " . $min_year;
            }
            if (!empty($max_year)) {
                $sql .= " AND jahrgang <= " . $max_year;
            }

    $sql .=   "ORDER BY title ASC";
} else {
    $sql = "SELECT *
            FROM magazines ";

if (!empty($min_year)) {
    $sql .= " WHERE jahrgang >= " . $min_year;
}
if (!empty($max_year)) {
    $sql .= " AND jahrgang <= " . $max_year;
}

$sql .= " ORDER BY title ASC ";
}
$result = $mysqli->query($sql);

$count = $result->num_rows;
?>

<!DOCTYPE html>
<html>
<head>
    <title>magazines</title>
    <link rel="icon" href="/librarysystem/image/IMSA-LOGO.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital@1&family=PT+Serif:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style/books.css">
    <link rel="stylesheet" type="text/css" href="style/footnotes.css">
    <script src="./js/books.js"></script>
</head>

<body>
    <?php include 'navbar.php' ?>
    <div class="a-z">
        <h2>A-Z Datenbanken</h2>
        <p>Suche das Buch in dem Datenbank</p>
    </div>
    
        <div class="alphabet-navigation">
            <?php
            $sql_alphabets = "SELECT DISTINCT LEFT(title, 1) AS alphabet FROM magazines ORDER BY alphabet ASC";
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
        <?php echo "Zeige Ergebnisse für ". $count . " sortiert nach Relevanz"; ?>
        </div>

        <div class="filter-section">
        <span class="show-filter" onclick="toggleFilter()">Year Filter</span>
        <div class="year-filter">
            <form action="" method="get">
                <label for="min_year">Minimum Year:</label>
                <input type="number" id="min_year" name="min_year" value="<?php echo $min_year; ?>">
                
                <label for="max_year">Maximum Year:</label>
                <input type="number" id="max_year" name="max_year" value="<?php echo $max_year; ?>">
                
                <button type="submit">Filter</button>
            </form>
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
                            <div><?php echo $row['jahrgang']; ?></div>
                            <div><?php echo $row['volumes']; ?></div>
                            <div><?php echo $row['standort']; ?></div>
                        </div>
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </div>
    </table>
    <script src="./js/books.js"></script>
    <div id="footnotes">
        <?php include 'footnotes.php' ?>
    </div>
</body>
</html>
