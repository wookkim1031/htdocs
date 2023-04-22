<?php
    $results_per_page = 20; //number of results per page
    $mysqli = require __DIR__ . "/database.php";

    if(isset($_GET['page'])) {
        $page = $_GET["page"];
    } else {
        $page = 1;
    }
    $start = ($page -1 ) * $results_per_page;
    $sql = "SELECT books.*, status.status, location.name AS location, location.room AS room
            FROM books
            JOIN status ON status.id = books.status 
            JOIN location ON location.id = books.location
            ORDER BY title ASC 
            LIMIT $start, ".$results_per_page ;
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
            <th>location</th>
            <th>isbn</th>
            <th>status</th>
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
                        <td>".$row['location']." ".$row['room']."</td>
                        <td>".$row['isbn']."</td>
                        <td>".$row['status']."</td>
                    </tr>";
            }
        ?>

    </tbody>
    </table>

    <?php
    $sql = "SELECT COUNT(ID) AS total FROM books";
    $result = $mysqli -> query($sql);
    $row = $result->fetch_assoc();
    $total_pages = ceil($row["total"] /$results_per_page);

    for ($i =1; $i<$total_pages; $i++) {
        echo "<a href='?page=".$i."'";
            if ($i==$page)  echo " class='curPage'";
            echo ">".$i."</a> ";
    }
    ?>
</body>
</html>