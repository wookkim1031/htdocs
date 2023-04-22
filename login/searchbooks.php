<? include 'includes/session.php'; ?>
<?php
$searchErr= '';
$books_details='';

if(isset($_POST['save'])) {
    if(!empty($_POST['search'])) {
        $search = $_POST['search'];
        $mysqli = require __DIR__ . "/database.php";
        $stmt= $mysqli->prepare("SELECT * from books
                                WHERE title like '%$search' or author like '%$search'");
        $stmt->execute();
        $books_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Search</title>
    </head>

    <body>
        <div> Search</div>
        <form action="#" method='POST'>
            <div>
                <input type="text" name="search" placeholder="search here">
            </div>
            <div>
                <button type="submit" name="save">Submit</button>
            </div>
        </form>
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
                    if(!$books_details) {
                        echo '<tr> No data found </tr>';
                    } else {
                        foreach($books_details as $key=>$value) {
                            ?>
                            <tr>
                                <td><?php echo $value['title'];?></td>
                                <td><?php echo $value['author'];?></td>
                                <td><?php echo $value['year'];?></td>
                                <td><?php echo $value['publisher'];?></td>
                            </tr>

                            <?php   
                        }
                    }
                ?>
            </tbody>
        </table>
    </body>
</html>