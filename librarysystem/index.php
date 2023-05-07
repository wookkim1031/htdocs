<?php 

session_start();

if (isset($_SESSION["user_id"])) { //check for the user_id
    
    $mysqli = require __DIR__ . "/database.php"; //get the databsae to get the connection

    $sql = "SELECT * FROM users
            WHERE  id = {$_SESSION["user_id"]}";
    
    $result = $mysqli->query($sql);

    $user = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title> Home</title>
</head>
<body>
    <?php if(isset($user)): ?>

        <?php include 'navbar.php' ?>
        <p>Hello <?= htmlspecialchars($user["name"]) ?> </p>
        <p><a href="profile.php">Profile</a></p>
        <p><a href="logout.php">Log out</a></p>
        <p><a href="books.php"></a></p>
    <?php else: ?>
        
        <p><a href="login.php">Login in</a> or <a href="signup.php">signup</a></p>
    
    <?php endif; ?>
</body>
</html>