<?php
$per_page = 15;

$mysqli = require __DIR__ . "/database.php";

if (isset($_GET['page'])) {
    $page = $_GET["page"];
} else {
    $page = 1;
}
$start = ($page - 1) * $per_page;

if (isset($_GET['alphabet'])) {
    $alphabet = $_GET['alphabet'];
    $sql = "SELECT *
            FROM magazines 
            WHERE title LIKE '" . $alphabet . "%'
            ORDER BY title ASC
            LIMIT $start," . $per_page;
} else {
    $sql = "SELECT *
            FROM magazines 
            ORDER BY title ASC
            LIMIT $start," . $per_page;
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
            <?php echo $count . " Database found"; ?>
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
    <div class="pagination">
        <?php
        $sql = "SELECT COUNT(ID) AS total FROM magazines";
        $result = $mysqli->query($sql);
        $row = $result->fetch_assoc();
        $total_pages = ceil($row["total"] / $per_page);

        for ($i = 1; $i <= $total_pages; $i++) {
            echo "<a href='?page=" . $i . "'";
            if (isset($_GET['page']) && $i == $page) echo " class='curPage'";
            echo ">" . $i . "</a>";
        }
        ?>
    </div>
    <?php include 'footnotes.php' ?>
</body>
</html>
