<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$mysqli = require __DIR__ . "/database.php";

$min_year = isset($_GET['min_year']) ? $_GET['min_year'] : '';
$max_year = isset($_GET['max_year']) ? $_GET['max_year'] : '';

$booksPerPage = 10;
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($currentPage < 1) {
    $currentPage = 1;
}
$offset = ($currentPage - 1) * $booksPerPage;

$totalBooksQuery = "SELECT COUNT(*) AS total FROM books
JOIN mediatypes ON books.type = mediatypes.id
JOIN status ON status.id = books.status 
JOIN location ON location.id = books.location WHERE 1=1";
$booksQueryBase = "SELECT books.*, mediatypes.type AS type, status.status, location.name AS location, location.room AS room
FROM books
JOIN mediatypes ON books.type = mediatypes.id
JOIN status ON status.id = books.status 
JOIN location ON location.id = books.location WHERE 1=1";

$filters = '';
if (!empty($_GET['alphabet'])) {
    $alphabet = $mysqli->real_escape_string($_GET['alphabet']);
    $filters .= " AND title LIKE '" . $alphabet . "%'";
}
if (!empty($_GET['location'])) {
    $location = $mysqli->real_escape_string($_GET['location']);
    $filters .= " AND location.id = " . $location;
}
if (!empty($min_year)) {
    $filters .= " AND year >= " . $min_year;
}
if (!empty($max_year)) {
    $filters .= " AND year <= " . $max_year;
}
if (!empty($_GET['status'])) {
    $status = $_GET['status'];
    $filters .= " AND status.id = " . $status;
}
if (!empty($_GET['mediatypes'])) {
    $mediatypes = $_GET['mediatypes'];
    $filters .= " AND mediatypes.id = " . $mediatypes;
}

$totalBooksQuery = $totalBooksQuery . $filters;
$booksQuery = $booksQueryBase . $filters;

$totalBooksResult = $mysqli->query($totalBooksQuery);
if (!$totalBooksResult) {
    die("Query failed: " . $mysqli->error);
}
$totalBooksRow = $totalBooksResult->fetch_assoc();
$totalBooks = $totalBooksRow['total'];
$totalPages = ceil($totalBooks / $booksPerPage);

$booksQuery .= " ORDER BY title ASC LIMIT $booksPerPage OFFSET $offset";
$result = $mysqli->query($booksQuery);
if (!$result) {
    die("Query failed: " . $mysqli->error);
}
$count = $result->num_rows;

$sql_locations = "SELECT * FROM location";
$result_locations = $mysqli->query($sql_locations);
if (!$result_locations) {
    die("Query failed: " . $mysqli->error);
}

$sql_status = "SELECT * FROM status";
$result_status = $mysqli->query($sql_status);
if (!$result_status) {
    die("Query failed: " . $mysqli->error);
}

$sql_mediatypes = "SELECT * FROM mediatypes";
$result_mediatypes = $mysqli->query($sql_mediatypes);
if (!$result_mediatypes) {
    die("Query failed: " . $mysqli->error);
}

$coverImages = [];

while ($row = $result->fetch_assoc()) {
    if (!empty($row['isbn'])) {
        $isbn = $row['isbn'];
        $url = "https://www.googleapis.com/books/v1/volumes?q=isbn:$isbn";

        $response = @file_get_contents($url);
        if ($response !== FALSE) {
            $data = json_decode($response, true);
            if (!empty($data['items']) && isset($data['items'][0]['volumeInfo']['imageLinks']['thumbnail'])) {
                $coverImageUrl = $data['items'][0]['volumeInfo']['imageLinks']['thumbnail'];
                $coverImages[$isbn] = $coverImageUrl;
            }
        }
    }
}
mysqli_data_seek($result, 0);

function generatePaginationURL($page) {
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}

include 'navbar.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Books</title>
    <link rel="icon" href="/librarysystem/image/IMSA-LOGO.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital@1&family=PT+Serif:ital,wght@0,400;0,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style/books.css">
    <link rel="stylesheet" type="text/css" href="style/footnotes.css">
</head>
<body>
    <div id="loading-screen">
        <div class="circle-loading"></div>
    </div>
    <div id="non-loading-screen">
        <div class="a-z">
            <h2>A-Z Datenbanken</h2>
            <p>Suche das Buch in der Datenbank</p>
        </div>

        <div class="alphabet-navigation">
            <?php
            $sql_alphabets = "SELECT DISTINCT LEFT(title, 1) AS alphabet FROM books ORDER BY CASE WHEN LEFT(title, 1) REGEXP '^[0-9]' THEN 1 ELSE 0 END, alphabet ASC";
            $result_alphabets = $mysqli->query($sql_alphabets);

            $numberAlphabets = [];
            $letterAlphabets = [];

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

            function generateAlphabetURL($alphabet) {
                $params = $_GET;
                $params['alphabet'] = $alphabet;
                unset($params['page']); // reset to the first page
                return '?' . http_build_query($params);
            }

            echo "<a href='" . generateAlphabetURL('#') . "'";
            if (isset($_GET['alphabet']) && $_GET['alphabet'] == '#') echo " class='selected'";
            echo ">#</a>";

            foreach ($letterAlphabets as $alphabet) {
                echo "<a href='" . generateAlphabetURL($alphabet) . "'";
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
                        <div class="list-choice">
                            <div class="list-choice-title">
                                <button class="show-filter" id="check-button" onclick="toggleFilter('year')">Select Year <img src="/librarysystem/image/angledown.svg" alt="arrow"> </button>
                            </div>
                            <div class="year-filter">
                                <form action="" method="get" name="yearFilterForm" id="yearFilterForm">
                                    <div class="year-label1">
                                        <label for="min_year">
                                            <input type="number" placeholder="Start Year" id="min_year" name="min_year" class="filter-input" value="<?php echo $min_year; ?>">
                                        </label>
                                    </div>
                                    <br>
                                    <div class="year-label">
                                        <label for="max_year">
                                            <input type="number" placeholder="End Year" id="max_year" name="max_year" class="filter-input" value="<?php echo $max_year; ?>">
                                        </label>
                                    </div>
                                    <br>
                                    <input type="hidden" name="alphabet" value="<?php echo isset($_GET['alphabet']) ? $_GET['alphabet'] : ''; ?>">
                                    <input type="hidden" name="location" value="<?php echo isset($_GET['location']) ? $_GET['location'] : ''; ?>">
                                    <input type="hidden" name="status" value="<?php echo isset($_GET['status']) ? $_GET['status'] : ''; ?>">
                                    <input type="hidden" name="mediatypes" value="<?php echo isset($_GET['mediatypes']) ? $_GET['mediatypes'] : ''; ?>">
                                    <button type="submit" form="yearFilterForm" class="filter-button">Apply</button>
                                </form>
                            </div>
                        </div>
                        <div class="list-choice">
                            <button class="show-filter" onclick="toggleFilter('status')">Select Status <img src="/librarysystem/image/angledown.svg" alt="arrow"> </button>
                            <div class="status-filter">
                                <form action="" method="get" name="filterForm">
                                    <?php
                                    $isStatusSelected = isset($_GET['status']);
                                    while ($row_status = $result_status->fetch_assoc()) {
                                        $checked = ($isStatusSelected && $_GET['status'] == $row_status['id']) ? 'checked' : '';
                                    ?>
                                        <label>
                                            <input type="radio" name="status" value="<?php echo $row_status['id']; ?>" <?php echo $checked; ?> onclick="this.form.submit();">
                                            <?php echo $row_status['status']; ?>
                                        </label>
                                    <?php } ?>
                                    <input type="hidden" name="alphabet" value="<?php echo isset($_GET['alphabet']) ? $_GET['alphabet'] : ''; ?>">
                                    <input type="hidden" name="min_year" value="<?php echo isset($_GET['min_year']) ? $_GET['min_year'] : ''; ?>">
                                    <input type="hidden" name="max_year" value="<?php echo isset($_GET['max_year']) ? $_GET['max_year'] : ''; ?>">
                                    <input type="hidden" name="location" value="<?php echo isset($_GET['location']) ? $_GET['location'] : ''; ?>">
                                    <input type="hidden" name="mediatypes" value="<?php echo isset($_GET['mediatypes']) ? $_GET['mediatypes'] : ''; ?>">
                                </form>
                            </div>
                        </div>
                        <div class="list-choice">
                            <button class="show-filter" onclick="toggleFilter('location')">Select Location <img src="/librarysystem/image/angledown.svg" alt="arrow1"> </button>
                            <div class="location-filter">
                                <form action="" method="get" id="locationFilterForm">
                                    <label for="location">
                                        <select name="location" id="location" class="filter-select">
                                            <option value="">Option</option>
                                            <?php
                                            while ($row_location = $result_locations->fetch_assoc()) {
                                                $selected = (isset($_GET['location']) && $_GET['location'] == $row_location['id']) ? "selected" : '';
                                                echo "<option value='" . $row_location['id'] . "' " . $selected . ">" . $row_location['name'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </label>
                                    <br>
                                    <input type="hidden" name="alphabet" value="<?php echo isset($_GET['alphabet']) ? $_GET['alphabet'] : ''; ?>">
                                    <input type="hidden" name="min_year" value="<?php echo isset($_GET['min_year']) ? $_GET['min_year'] : ''; ?>">
                                    <input type="hidden" name="max_year" value="<?php echo isset($_GET['max_year']) ? $_GET['max_year'] : ''; ?>">
                                    <input type="hidden" name="mediatypes" value="<?php echo isset($_GET['mediatypes']) ? $_GET['mediatypes'] : ''; ?>">
                                    <button type="submit" form="locationFilterForm" class="filter-button">Filter</button>
                                </form>
                            </div>
                        </div>
                        <div class="list-choice">
                            <button class="show-filter" onclick="toggleFilter('mediatypes')">Select Media <img src="/librarysystem/image/angledown.svg" alt="arrow1"> </button>
                            <div class="mediatypes-filter">
                                <form action="" method="get" id="mediaFilterForm">
                                    <label for="mediatypes">
                                        <select name="mediatypes" id="mediatypes" class="filter-select">
                                            <option value="">Option</option>
                                            <?php
                                            while ($row_mediatypes = $result_mediatypes->fetch_assoc()) {
                                                $selected = (isset($_GET['mediatypes']) && $_GET['mediatypes'] == $row_mediatypes['id']) ? "selected" : '';
                                                echo "<option value='" . $row_mediatypes['id'] . "' " . $selected . ">" . $row_mediatypes['type'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </label>
                                    <br>
                                    <input type="hidden" name="alphabet" value="<?php echo isset($_GET['alphabet']) ? $_GET['alphabet'] : ''; ?>">
                                    <input type="hidden" name="location" value="<?php echo isset($_GET['location']) ? $_GET['location'] : ''; ?>">
                                    <input type="hidden" name="status" value="<?php echo isset($_GET['status']) ? $_GET['status'] : ''; ?>">
                                    <input type="hidden" name="min_year" value="<?php echo isset($_GET['min_year']) ? $_GET['min_year'] : ''; ?>">
                                    <input type="hidden" name="max_year" value="<?php echo isset($_GET['max_year']) ? $_GET['max_year'] : ''; ?>">
                                    <button type="submit" form="mediaFilterForm" class="filter-button">Filter</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="reset">
                            <button class="reset-button" type="button" id="resetFiltersButton">Reset Selections</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table id="tb">
                <tbody>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        if (!empty($row['title'])) { ?>
                            <tr>
                                <td>
                                    <?php
                                    if (array_key_exists($row['isbn'], $coverImages)) {
                                        echo '<img src="' . htmlspecialchars($coverImages[$row['isbn']]) . '" alt="Cover Image" style="height: 200px; width: 150px;">';
                                    } else {
                                        echo '  <img src="/librarysystem/image/book-solid.svg" alt="Default Cover" style="height: 200px; width: 150px;">';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="books-details" onclick="openPopup(<?php echo $row['id'] ?>)">
                                        <?php if (($row['type']) !== "Unbekannt") { ?>
                                            <div class="book-type"> <?php echo $row['type']; ?> </div>
                                        <?php } ?>
                                        <div class="book-title"> <?php echo $row['title']; ?> </div>
                                        <?php if (($row['publisher']) !== "") { ?>
                                            <div class="book-publisher"> <?php echo "Publisher : " . $row['publisher']; ?> </div>
                                        <?php } ?>
                                        <div class="book-author"> <?php echo $row['author']; ?> </div>
                                        <?php if (($row['year']) !== "0000") { ?>
                                            <div> <?php echo "Jahr : " . $row['year']; ?> </div>
                                        <?php } ?>
                                        <?php if (($row['room']) !== "Unbekannt") { ?>
                                            <div> <?php echo "Standort : " . $row['location'] ?> </div>
                                            <div> <?php echo ' Raum : ' . $row['room']; ?> </div>
                                        <?php } ?>
                                        <?php if (($row['status']) == "Verfügbar") { ?>
                                            <div class="book-status"> Verfügbar </div>
                                        <?php } else { ?>
                                            <div class="book-status-not"> Entliehen</div>
                                        <?php } ?>
                                    </div>
                                    <div class="popup" id="popup-<?php echo $row['id']; ?>">
                                        <h2 class="book-title-popup"> <?php echo $row['title']; ?> </h2>
                                        <div class="book-container">
                                            <div class="book-cover">
                                                <?php
                                                if (array_key_exists($row['isbn'], $coverImages)) {
                                                    echo '<img src="' . htmlspecialchars($coverImages[$row['isbn']]) . '" alt="Cover Image">';
                                                } else {
                                                    echo ' <img src="/librarysystem/image/book-solid.svg" alt="Default Cover">';
                                                }
                                                ?>
                                            </div>
                                            <div class="book-info">
                                                <p class="book-author"><?php echo $row['author']; ?></p>
                                                <p class="book-publisher"><?php echo $row['publisher']; ?></p>
                                                <p class="book-isbn"><?php echo $row['isbn']; ?></p>
                                                <p>
                                                    <form action="save_book.php" method="post">
                                                        <input type="hidden" name="book_id" value="<?= $row['id'] ?>">
                                                        <button class="save-button" type="submit" name="save_book">Save Book</button>
                                                    </form>
                                                </p>
                                            </div>
                                        </div>
                                        <h3 class="bib-info">Bibilographic Information</h3>
                                        <hr>
                                        <div class="popup-container">
                                            <div class="info-row">
                                                <div class="info-title">Title:</div>
                                                <div class="info-detail"><?php echo $row['title']; ?></div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-title">Author:</div>
                                                <div class="info-detail"><?php echo $row['author']; ?></div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-title">Year:</div>
                                                <div class="info-detail"><?php echo $row['year']; ?></div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-title">Publisher:</div>
                                                <div class="info-detail"><?php echo $row['publisher']; ?></div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-title">Edition:</div>
                                                <div class="info-detail"><?php echo $row['edition']; ?></div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-title">Location:</div>
                                                <div class="info-detail"><?php echo $row['location']; ?></div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-title">Room:</div>
                                                <div class="info-detail"><?php echo $row['room']; ?></div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-title">ISBN:</div>
                                                <div class="info-detail"><?php echo $row['isbn']; ?></div>
                                            </div>
                                        </div>
                                        <button class="close-button" type="button" onclick="closePopup(<?php echo $row['id'] ?>)">&times;</button>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            <?php
            $range = 8;
            $start = ((floor(($currentPage - 1) / $range)) * $range) + 1;
            $end = $start + $range - 1;
            $end = ($totalPages < $end) ? $totalPages : $end;

            echo '<div class="pagination">';
            if ($start > 1) {
                echo '<a href="' . generatePaginationURL($start - 1) . '">&laquo; Previous</a>';
            }

            for ($page = $start; $page <= $end; $page++) {
                echo '<a href="' . generatePaginationURL($page) . '"';
                if ($page == $currentPage) {
                    echo ' class="active"';
                }
                echo '>' . $page . '</a>';
            }

            if ($end < $totalPages) {
                echo '<a href="' . generatePaginationURL($end + 1) . '">Next &raquo;</a>';
            }
            echo '</div>';
            ?>
        </div>

        <script src="./js/books.js"></script>
        <script>
            document.getElementById('filterToggleBtn').addEventListener('click', function () {
                var filtersSection = document.getElementById('filters');
                if (filtersSection.style.display === 'block' || filtersSection.style.display === '') {
                    filtersSection.style.display = 'none';
                } else {
                    filtersSection.style.display = 'block';
                }
            });

            function toggleFilter(filterType) {
                var filterElement = document.querySelector('.' + filterType + '-filter');
                if (filterElement.style.display === 'block') {
                    filterElement.style.display = 'none';
                } else {
                    filterElement.style.display = 'block';
                }
            }
        </script>
        <button onclick="scrollToBottom()" id="scrollToBottomBtn" title="Go to Bottom">Scroll to Bottom</button>
        <button onclick="scrollToTop()" id="scrollToTopBtn" title="Go to Top">Scroll to Top</button>
        <div class="footnotes">
            <?php include 'footnotes.php' ?>
        </div>
    </div>
</body>
</html>
