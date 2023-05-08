<?php 

session_start();

if (isset($_SESSION["user_id"])) { //check for the user_id
    
    $mysqli = require __DIR__ . "/../database.php"; //get the databsae to get the connection

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
    <h1>Welcome, <?php echo $user['name']; ?>!</h1>
    <p>Your email address is <?php echo $user['email']; ?></p>

    <form method="post">
        <div>
            <label>Email Address</label>
            <input type="text" name="email" id="email" value="<?php echo $row['email']; ?>">
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" id="password" value="<?php echo $row['password_hash']; ?>">
        </div>
        <div>
            <input type="submit" name="edit_user" value="Edit">
        </div>
    </form>
</body>
</html>