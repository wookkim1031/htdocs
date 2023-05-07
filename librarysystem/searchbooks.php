<? include 'includes/session.php'; ?>
<?php
$searchErr= '';
$books_details='';

if(isset($_POST['save'])) {
        $title = !empty($_POST['title']) ? $_POST['title'] : '';
        $author = !empty($_POST['author']) ? $_POST['author'] : '';
        $year = !empty($_POST['year']) ? $_POST['year'] : '';
        $mysqli = require __DIR__ . "/database.php";
        $stmt= $mysqli->prepare("SELECT * from books
                                WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ?");
        $stmt->execute(["%$title%", "%$author", "%$year"]);
        $books_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
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
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" placeholder="search by title">
            </div>
            <div>
                <label for="author">Author:</label>
                <input type="text" name="author" id="author" placeholder="search by author">
            </div>
            <div>
                <label for="year">Year:</label>
                <input type="text" name="year" id="year" placeholder="search by year">
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
                if (isset($_POST['save']) && (!empty($_POST['title']) || !empty($_POST['author']) || !empty($_POST['year']))) {
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