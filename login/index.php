<?php 
session_start();

?>

<!DOCTYPE html>
<html>
<head>
    <title> Home</title>
</head>
<body>
    <?php if(isset($_SESSION["user_id"])): ?>

        <p>You are logged in</p>
        
        <p><a href="logout.php">Log out</a></p>
    <?php else: ?>
        
        <p><a href="login.php">Login in</a> or <a href="signup.php">signup</a></p>
    
    <?php endif; ?>
</body>
</html>