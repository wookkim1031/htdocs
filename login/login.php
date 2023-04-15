<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $mysqli = require __DIR__ . "/database.php";

    $sql = sprintf("SELECT * FROM users 
            WHERE email = '%s'",
            $mysqli->real_escape_string($_POST["email"])); //sql injection protection
    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();

    if($user) {

        if(password_verify($_POST["password"], $user["password_hash"])) {
            die ("Login successful");
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title> Login </title>
</head>
<body>
    <form method="post">
        <label for="email">email</label>
        <input type="email" name="email" id="email">

        <label for="password">Password</label>
        <input type="password" name="password" id="password">

        <button>Login</button>
    </form>
    
</body>
</html>