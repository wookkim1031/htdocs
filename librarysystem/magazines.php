<?php 
    $per_page = 15;

    $mysqli = require __DIR__ . "/database.php";

    if(isset($_GET['page'])){
        $page = $_GET["page"];
    } else {
        $page = 1;
    }
    $start = ($page -1) * $per_page;

    $sql = "SELECT *
            FROM magazines 
            ORDER BY title ASC
            LIMIT $start,".$per_page;
    $result = $mysqli-> query($sql);

?>

<!DOCTYPE html>
<html>
    <head>
        <title> magazines </title>
    </head>
    <body>
        <table>
            <thead>
                <tr>
                    <th>title</th>
                    <th>Jahr</th>
                    <th>volumes</th>
                    <th>standort</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    while($row = $result -> fetch_assoc()) {
                        echo "
                            <tr>
                                <td>".$row['title']."</td>
                                <td>".$row['jahrgang']."</td>
                                <td>".$row['volumes']."</td>
                                <td>".$row['standort']."</td>
                            </tr>
                        ";
                    }
                ?>
            </tbody>
        </table>

        <?php
        $sql = "SELECT COUNT(ID) AS total FROM magazines";
        $result = $mysqli -> query($sql);
        $row = $result-> fetch_assoc();
        $total_pages = ceil($row["total"] /$per_page);

        for($i = 1; $i<$total_pages; $i++) {
            echo "<a href='?page=".$i."'";
                if($i==$page) echo " class='curPage'";
                echo ">".$i."</a>";
        }

        ?>
    </body>
</html>
