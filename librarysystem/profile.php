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
    <link rel="stylesheet" type="text/css" href="style/profile.css">
</head>
<body>

    <div class="profile-container">
        <h2>Profile</h2>
        <div class="profile-picture"></div>
        <div class="profile-details">
            <p><strong>Name:</strong> <?php echo $user['name']; ?></h1></p>
            <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
            <p><strong>Location:</strong> New York City</p>
            <p><strong>Interests:</strong> Travel, Photography, Music</p>
            <p><a href="#">Edit Profile</a></p>
        </div>
    </div>
</body>
</html>