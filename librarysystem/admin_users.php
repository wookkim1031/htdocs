<?php
session_start(); 

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit;
}

$mysqli = require __DIR__ . "/database.php";

$displayUsers = [];

$query = "SELECT users.id, users.name, users.email, user_roles.role_name FROM users JOIN user_roles ON users.role_id = user_roles.id";
$result = $mysqli->query($query);
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
    $displayUsers = $users;
}

if(isset($_POST['save'])) {
    $search = !empty($_POST['search_users']) ? trim($_POST['search_users']) : '';
    $stmt = $mysqli->prepare("SELECT users.id, users.name, users.email, user_roles.role_name FROM users LEFT JOIN user_roles ON users.role_id = user_roles.id WHERE users.name LIKE ? OR users.email LIKE ? OR user_roles.role_name LIKE ?");
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $user_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    if (!empty($user_details)) {
        $displayUsers = $user_details;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type="text/css" href="style/search_users_admin.css">
<link rel="icon" href="/librarysystem/image/IMSA-LOGO.png">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Page</title>
</head>
<body>
    <?php include "navbar.php"; ?>
    <div class="flexbox">
        <div class="search-options">
            <div class="add-user-btn-container">
                <a href="add_user_page.php" class="add-user-btn">Add User</a>
            </div>
        </div>

        <form action="admin_users.php" method="POST" class="search-bar">
                <input class="search-input" type="text" name="search_users" placeholder="Search users" value="<?php echo isset($_POST['search_users']) ? htmlspecialchars($_POST['search_users']) : ''; ?>">
                <button type="submit" name="save"><img src="../librarysystem/image/search.svg" alt="search"></button>
        </form>
    </div>

    <table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Edit/Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($displayUsers as $user): ?>
            <tr id="user-<?php echo $user['id']; ?>" class="show-user-form">
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                <td>
                    <button type="button" class="button" onclick="showEditForm(<?php echo $user['id']; ?>)">Edit</button>
                    <form action="delete_user.php" method="POST" onsubmit="return confirm('Sind Sie sicher, dass Sie diesen Benutzer löschen möchten?');" style="display: inline;">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <button type="submit" name="delete_user" class="delete-button">Delete</button>
                    </form>
                </td>
            </tr>
            <tr id="edit-form-<?php echo $user['id']; ?>" class="edit-form-row" style="display:none;">
                <td colspan="5">
                    <div class="edit-form-popup">
                        <form action="edit_users.php" method="POST" class="edit-user-form">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            Username: <input type="text" name="user_name" value="<?php echo htmlspecialchars($user['name']); ?>"><br>
                            Email: <input type="text" name="user_email" value="<?php echo htmlspecialchars($user['email']); ?>"><br>
                            Roles: <select name="user_role_id">
                                <?php
                                
                                if (!isset($roles)) {
                                    $role_query = "SELECT id, role_name FROM user_roles";
                                    $role_result = $mysqli->query($role_query);
                                    $roles = $role_result->fetch_all(MYSQLI_ASSOC);
                                }

                                foreach ($roles as $role) {
                                    $selected = ($role['role_name'] == $user['role_name']) ? 'selected' : '';
                                    echo "<option value=\"" . htmlspecialchars($role['id']) . "\" $selected>" . htmlspecialchars($role['role_name']) . "</option>";
                                }
                                ?>
                            </select><br>
                            <button type="submit" name="update_user" class="button">Update</button>
                            <button type="button" onclick="hideEditForm(<?php echo $user['id']; ?>)" class="button">Cancel</button>
                        </form>
                        
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<script src="./js/admin_users.js"> </script>

</html>

