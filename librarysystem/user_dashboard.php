<?php
session_start();
include 'navbar.php';

if (isset($_SESSION["user_id"])) {
    $mysqli = require __DIR__ . "/database.php";
    
    $user_id = $_SESSION["user_id"]; // Store user ID in a variable

    $stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $_SESSION['role_id'] = $user['role_id'];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" type="text/css" href="style/profile.css">
    <link rel="icon" href="/librarysystem/image/IMSA-LOGO.png">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> <!-- Font Awesome for icons -->
</head>
<body>
    <?php if(isset($user)) :?>
    <div class="profile-container">
        <div class="cover-photo">
            <h2>  User Profile </h2> 

        </div>
        <div class="profile-header">
            <div class="profile-picture"></div>
            <h2><?php echo $user['name']; ?></h2>
            <p><?php echo $user['email']; ?></p>
        </div>
        <div class="profile-tabs">
            <ul>
                <li><a href="#"><i class="fas fa-user"></i> About</a></li>
            </ul>
        </div>
        <div class="profile-content">
            <div class="non-hidden">
            <table>
                <tr>
                    <th>Name:</th>
                    <td><?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <tr>
                    <th>Telefonnummer:</th>
                    <td><?php echo htmlspecialchars($user['telephone'] ?? 'Nicht vorhanden', ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
            </table>
            </div>
            <div class="edit-section hidden">
                <form action="update_profile.php" method="post">
                    <div class="form-group">
                        <label for="new_name">Update Name:</label>
                        <input type="text" id="new_name" name="new_name" value="<?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="new_telephone">Update Telephone:</label>
                        <input type="text" id="new_telephone" name="new_telephone" value="<?php echo htmlspecialchars($user['telephone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <button type="submit" class="profile-button">Save</button>
                </form>
            </div>
            <button id="editButton" class="profile-button non-hidden">Edit</button> <br><br>
            <?php 
                    if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 2) {
                        echo '<a href="admin_searchbooks.php" class="admin_button">Edit Books and Magazines</a><br><br>';
                        echo '<a href="admin_users.php" class="admin_button"> Mangae Users</a>';
                    }
            ?>
        </div>
    </div>

    <script src="./js/dashboard.js"></script>

    <?php else: ?>

        <?php include 'login.php' ?>
    
    <?php endif; ?>
</body>
</html>