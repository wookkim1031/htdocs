<? 
include 'includes/session.php'; 
?>
<? 
include 'index.php';
?>
<?php
    $mysqli = require __DIR__ . "/database.php";
    $sql = "SELECT * FROM books";
    $result = $mysqli -> query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title> Home</title>
    <link rel="stylesheet" type="text/css" href="style/books.css">
</head>
<body>
    <table>
    <thead>
        <tr>
            <th>title</th>
            <th>author</th>
            <th>year</th>
            <th>publisher</th>
        </tr>
    </thead>
    <tbody>
        <?php 
            while($row = $result-> fetch_assoc()) {
                echo "
                    <tr>
                        <td>".$row['title']."</td>
                        <td>".$row['author']."</td>
                        <td>".$row['year']."</td>
                        <td>".$row['publisher']."</td>
                    </tr>";
            }
        ?>

    </tbody>
    </table>
</body>
</html>