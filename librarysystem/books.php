<?php
    $results_per_page = 20; //number of results per page
    $mysqli = require __DIR__ . "/database.php";

    if(isset($_GET['page'])) {
        $page = $_GET["page"];
    } else {
        $page = 1;
    }
    $start = ($page -1 ) * $results_per_page;
    $sql = "SELECT books.*, mediatypes.type AS type, status.status, location.name AS location, location.room AS room
            FROM books
            JOIN mediatypes ON books.type = mediatypes.id
            JOIN status ON status.id = books.status 
            JOIN location ON location.id = books.location
            ORDER BY title ASC 
            LIMIT $start, ".$results_per_page ;
    $result = $mysqli -> query($sql);    
?>

<!DOCTYPE html>
<html>
<head>
    <title>Books</title>
    
    <link rel="stylesheet" type="text/css" href="style/books.css">
</head>
<body>
    <?php include 'navbar.php' ?>
    <table>
    <div class="container">
    <tbody>
        <?php 
            while($row = $result-> fetch_assoc()) {
                if(!empty($row['title'])) { ?> 
                    <tr>
                        <td> <img src="/librarysystem/image/newspaper-solid.svg" alt="magazine"></td>
                        <td>
                            <div class="books-details">
                                <div> <?php echo $row['type']; ?> </div>
                                <div> <?php echo $row['title']; ?> </div>
                                <div> <?php echo $row['author']; ?> </div>
                                <div> <?php echo $row['year']; ?> </div>
                                <div> <?php echo $row['publisher']; ?> </div>
                                <div> <?php echo $row['isbn']; ?> </div>
                                <div> <?php echo $row['edition']; ?> </div>
                                <div> <?php echo $row['location']; ?> </div>
                                <div> <?php echo $row['status']; ?> </div>
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