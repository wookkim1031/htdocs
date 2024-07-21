<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include 'navbar.php';

$mysqli = require __DIR__ . "/database.php";

$min_year = isset($_GET['min_year']) ? $_GET['min_year'] : '';
$max_year = isset($_GET['max_year']) ? $_GET['max_year'] : '';

$magazinesPerPage = 8; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $magazinesPerPage;

$sql = "SELECT *, image_path, magazines.id AS magazine_id FROM magazines";
$conditions = [];

if (isset($_GET['alphabet'])) {
    $alphabet = $_GET['alphabet'];
    $conditions[] = "title LIKE '" . $alphabet . "%'";
}

if (!empty($min_year)) {
    $conditions[] = "jahrgang >= " . $min_year;
}
if (!empty($max_year)) {
    $conditions[] = "jahrgang <= " . $max_year;
}

if (!empty($_GET['standort'])) {
    $standort = $_GET['standort'];
    $conditions[] = "standort = '" . $standort . "'";
}

if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}

$sql .= " ORDER BY title ASC LIMIT $magazinesPerPage OFFSET $offset";

$totalMagazinesQuery = "SELECT COUNT(*) as total FROM magazines";
if (count($conditions) > 0) {
    $totalMagazinesQuery .= " WHERE " . implode(' AND ', $conditions);
}
$totalMagazinesResult = $mysqli->query($totalMagazinesQuery);
$totalMagazinesRow = $totalMagazinesResult->fetch_assoc();
$totalMagazines = $totalMagazinesRow['total'];
$totalPages = ceil($totalMagazines / $magazinesPerPage);

$result = $mysqli->query($sql);
if (!$result) {
    die("Query failed: " . $mysqli->error);
}
$count = $result->num_rows;

$sql_alphabets = "SELECT DISTINCT LEFT(title, 1) AS alphabet FROM magazines ORDER BY alphabet ASC";
$result_alphabets = $mysqli->query($sql_alphabets);
if (!$result_alphabets) {
    die("Query failed: " . $mysqli->error);
}

$sql_locations = "SELECT DISTINCT standort FROM magazines";
$result_location = $mysqli->query($sql_locations);
if (!$result_location) {
    die("Query failed: " . $mysqli->error);
}

// Function to generate pagination links
function generatePaginationURL($page) {
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}

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
    <div class="a-z">
        <h2>A-Z Datenbanken</h2>
        <p>Suche das Magazine in der Datenbank</p>
    </div>

    <div class="alphabet-navigation">
        <?php
        while ($row_alphabet = $result_alphabets->fetch_assoc()) {
            $alphabet = $row_alphabet['alphabet'];
            echo "<a href='?alphabet=" . $alphabet . "'";
            if (isset($_GET['alphabet']) && $_GET['alphabet'] == $alphabet) echo " class='selected'";
            echo ">" . $alphabet . "</a>";
        }
        ?>
    </div>

    <div class="found-books-count">
        <?php echo "Zeige Ergebnisse für " . $count . " sortiert nach Relevanz"; ?>
    </div>
    <div class="filter-container">
        <button class="filter-toggle-btn" id="filterToggleBtn" onclick="toggleFilters()">Expand Filters</button>
        <div class="filters" id="filters">
            <button class="filter-close-btn" id="filterCloseBtn" onclick="closeFilters()">&times;</button>
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
                                $isLocationSelected = (isset($_GET['standort']) &&  $_GET['standort'] == $location) ? 'selected' : '';
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
        </div>
    </div>
    <div class="table-container">
        <table>
            <div class="container">
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td>
                                <?php if(!empty($row['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Magazine Image">
                                <?php else: ?>
                                    <img src="/librarysystem/image/newspaper-solid.svg" alt="magazine">
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="books-details">
                                    <div class="book-title"><?php echo htmlspecialchars($row['title']); ?></div>
                                    <div class="book-info"><?php echo htmlspecialchars($row['jahrgang']); ?></div>
                                    <div class="book-info"><?php echo htmlspecialchars($row['volumes']); ?></div>
                                    <div class="book-info"><?php echo htmlspecialchars($row['standort']); ?></div>
                                    <div>
                                    <form action="save_magazine.php" method="post">
                                        <input type="hidden" name="magazine_id" value="<?php echo htmlspecialchars($row['magazine_id']); ?>">
                                        <button class="save-button" type="submit" name="save_magazine">Save Magazine</button>
                                    </form></div>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </div>
        </table>
    </div>

    <div class="pagination-container">
        <?php
            $range = 8;
            $start = ((floor(($page - 1) / $range)) * $range)  + 1;
            $end = $start + $range - 1;
            $end = ($totalPages < $end) ? $totalPages : $end;

            echo '<div class="pagination">'; 
            if ($start > 1) {
                echo '<a href="' . generatePaginationURL($start - 1) . '">&laquo; Previous</a>';
            }
            
            for ($i = $start; $i <= $end; $i++) {
                echo '<a href="' . generatePaginationURL($i) . '"';
                if ($i == $page) {
                    echo ' class="active"';
                }
                echo '>' . $i . '</a>';
            }
        
            if ($end < $totalPages) {
                echo '<a href="' . generatePaginationURL($end + 1) . '">Next &raquo;</a>';
            }
            echo '</div>';
        ?>
    </div>

    <script src="./js/books.js"></script>
    <div id="footnotes">
        <?php include 'footnotes.php' ?>
    </div>
</body>
</html>
