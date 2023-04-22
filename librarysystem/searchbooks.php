<? include 'includes/session.php'; ?>
<?php
$searchErr= '';
$books_details='';

if(isset($_POST['save'])) {
    if(!empty($_POST['search'])) {
        $search = $_POST['search'];
        $mysqli = require __DIR__ . "/database.php";
        $stmt= $mysqli->prepare("SELECT * from books
                                WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ?");
        $stmt->execute(["%$search%", "%$search%", "%$search%"]);
        $books_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
                    <th>status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (isset($_POST['save']) && !empty($_POST['search'])) {
                    if(!$books_details) {
                        echo '<tr> No data found </tr>';
                    } else {
                        foreach($books_details as $book) {
                            ?>
                            <tr>
                                <td><?php echo $book['title'];?></td>
                                <td><?php echo $book['author'];?></td>
                                <td><?php echo $book['year'];?></td>
                                <td><?php echo $book['publisher'];?></td>
                                <td><?php echo $book['status'];?></td>
                            </tr>
                            <?php   
                        }
                    }
                }
                ?>
            </tbody>
        </table>
    </body>
</html>