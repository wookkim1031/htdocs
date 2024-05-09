<?php
session_start(); 

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit;
}

include "navbar.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" type="text/css" href="style/search_books_index.css">
    <link rel="stylesheet" type="text/css" href="style/add_user_page.css">
</head>
<body>
    <div class="flexbox">
        <div class="search-options">
            <div class="add-user-btn-container">
                <a href="admin_users.php" class="add-user-btn">Edit User</a>
            </div>
        </div>

        <form action="admin_users.php" method="POST" class="search-bar">
                <input class="search-input" type="text" name="search_users" placeholder="Users durchsuchen" value="<?php echo isset($_POST['search_users']) ? htmlspecialchars($_POST['search_users']) : ''; ?>">
                <button type="submit" name="save"><img src="../librarysystem/image/search.svg" alt="search"></button>
        </form>
    </div>
    <div class="form-container">
        <h2>Add New User</h2>
        
    <form action="add_user.php" method="POST" class="form">
        <div class="form-field">
            <input type="text" id="name" name="name" placeholder=" " required>
            <label for="name">Username</label>
        </div>
        <div class="form-field">
            <input type="email" id="email" name="email" placeholder=" " required>
            <label for="email">Email</label>
        </div>
        <div class="form-field">
            <select name="role_id" id="role_id" required>
                <option value="" disabled selected></option>
                <option value="1">User</option>
                <option value="2">Admin</option>
            </select>
            <label for="role_id" class="select-label">Role</label>
        </div>
        <div class="form-field">
            <input type="password" id="password" name="password" placeholder=" " required>
            <label for="password">Password</label>
        </div>
        <button type="submit" name="add_user">User hinzufügen</button>
    </form>
</div>
    </div>
</body>
</html>