<?php
session_start();

if (isset($_SESSION["user_id"])) {
    $mysqli = require __DIR__ . "/database.php";
    
    $user_id = $_SESSION["user_id"]; // Store user ID in a variable

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" type="text/css" href="style/profile.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- Font Awesome for icons -->
</head>
<body>
    <div class="profile-container">
        <div class="cover-photo">
            <a href="index.php"><img src="/librarysystem/image/IMSA-LOGO.png" alt="logo"> Home </a>
            <h2>  Bibilothek für Medizin Statistik </h2> 
        </div>
        <div class="profile-header">
            <div class="profile-picture"></div>
            <h2><?php echo $user['name']; ?></h2>
            <p><?php echo $user['email']; ?></p>
        </div>
        <div class="profile-tabs">
            <ul>
                <li><a href="#"><i class="fas fa-user"></i> About</a></li>
                <li><a href="#"><i class="fas fa-book"></i> Lend Books</a></li>
            </ul>
        </div>
        <div class="profile-content">
            <h3>About</h3>
            <p>Add your about information here.</p>
        </div>
    </div>
</body>
</html>