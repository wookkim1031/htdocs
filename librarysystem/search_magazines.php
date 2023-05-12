<?php 
$searchErr = '';
$magazines_details = '';


if(isset($_POST['save'])) {
    $search = !empty($_POST['search']) ? $_POST['search'] : '';
    $mysqli = require __DIR__ . "/database.php";
    $stmt = $mysqli->prepare("SELECT * FROM magazines WHERE title LIKE ? OR jahrgang LIKE ? OR volumes LIKE ?");
    $stmt->execute(["%$search%", "%$search", "%$search"]);
    $magazines_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="style/search_magazines.css">
<head>
    <title>magazines</title>
</head>
<?php include 'navbar.php' ?> 
<body>
    <div class= "container">
        <form action="#" method='POST' class="search-bar">
            <input type="text" name="search" id="search" placeholder="search by title, jahrgang, volumes">
            <button type="submit" name="save"> <img src="image/search.svg" alt="search">search </button>
        </form>
    </div>

    <table>
        <tbody>
            <?php 
            if(isset($_POST['save']) && !empty($_POST['search'])) {
                if(!$magazines_details) {
                    echo '<tr> No data found </tr>';
                } else {
                    foreach ($magazines_details as $magazine) {
                        ?>
                        <div class="container">
                        <tr>
                            <td> <img src="/librarysystem/image/newspaper-solid.svg" alt="magazine"></td>
                            <td>
                                <div class="magazine-details">
                                    <div> <?php echo $magazine['title']; ?> </div>
                                    <div> <?php echo $magazine['jahrgang']; ?> </div>
                                    <div> <?php echo $magazine['volumes']; ?> </div>
                                    <div> <?php echo $magazine['standort']; ?> </div>
                                </div>
                            </td>
                        </tr>
                        </div>
                    <?php
                    }
                }
            }
            ?>
        </tbody>
    </table>
</body>

</html>