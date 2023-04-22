<?php 

    session_start();

    if(isset($_SESSION['user_id'])) {

        $mysqli = require __DIR__ . "/database.php";

        $sql = "SELECT * FROM users
                WHERE id = {$_SESSION["user_id"]}";

        $result = $mysqli->query($sql);

        $user = $result->fetch_assoc();
    }


?>