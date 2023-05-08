<?php 
$searchErr = '';
$magazines_details = '';


if(isset($_POST['save'])) {
    $search = !empty($_POST['search']) ? $_POST['search'] : '';
    $mysqli = require __DIR__ . "/../database.php";
    $stmt = $mysqli->prepare("SELECT * FROM magazines WHERE title LIKE ? OR jahrgang LIKE ? OR volumes LIKE ?");
    $stmt->execute(["%$search%", "%$search", "%$search"]);
    $magazines_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>magazines</title>
</head>

<body>
    <div>
        <form action="#" method='POST'>
            <input type="text" name="search" id="search" placeholder="search by title, jahrgang, volumes">
            <button type="submit" name="save"> <img src="../image/search.svg" alt="search">search </button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>title</th>
                <th>jahrgang</th>
                <th>volumes</th>
                <th>standort</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if(isset($_POST['save']) && !empty($_POST['search'])) {
                if(!$magazines_details) {
                    echo '<tr> No data found </tr>';
                } else {
                    foreach ($magazines_details as $magazine) {
                        ?>
                        <tr>
                            <td><?php echo $magazine['title']; ?></td>
                            <td><?php echo $magazine['jahrgang']; ?></td>
                            <td><?php echo $magazine['volumes']; ?></td>
                            <td><?php echo $magazine['standort']; ?></td>
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