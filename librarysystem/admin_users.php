<?php
session_start(); 

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit;
}

$searchType = $_POST['searchType'] ?? 'users';
$mysqli = require __DIR__ . "/database.php";

if(isset($_POST['save'])) {
    $search = !empty($_POST['search_users']) ? $_POST['search_users'] : '';
    // Assuming you want to search by role name, not role_id
    $stmt = $mysqli->prepare("SELECT users.id, users.name, users.email, user_roles.role_name FROM users LEFT JOIN user_roles ON users.role_id = user_roles.id WHERE users.name LIKE ? OR users.email LIKE ? OR user_roles.role_name LIKE ?");
    $searchTerm = "%$search%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $user_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

//SELECT users.id, users.name, users.email, user_roles.role_name FROM users JOIN user_roles ON users.role_id = user_roles.id
$query = "SELECT users.id, users.name, users.email, user_roles.role_name FROM users JOIN user_roles ON users.role_id = user_roles.id";
$result = $mysqli->query($query);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" type="text/css" href="style/search_books_index.css">
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
            <form action="admin_users.php" method="POST" class="search-bar">
                <input class="search-input" type="text" name="search_users" placeholder="Users" value="<?php echo isset($_POST['search_users']) ? htmlspecialchars($_POST['search_users']) : ''; ?>">
                <button type="submit" name="save"><img src="../librarysystem/image/search.svg" alt="search"></button>
            </form>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <!-- show default -->
            <?php foreach ($users as $user): ?>
                <tr id="user-<?php echo $user['id']; ?>" class="show-user-form">
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                    <td><button onclick="showEditForm(<?php echo $user['id']; ?>)">Edit</button></td>
                </tr>
                <tr id="edit-form-<?php echo $user['id']; ?>" style="display:none;">
                    <td colspan="5"> 
                        <form action="edit_users.php" method="POST" class="edit-user-form">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <input type="text" name="user_name" value="<?php echo htmlspecialchars($user['name']); ?>">
                            <input type="text" name="user_email" value="<?php echo htmlspecialchars($user['email']); ?>">
                            <select name="user_role_id">
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
                            </select>
                            <button type="submit" name="update_user">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>        
        </tbody>
    </table>

</body>

<script>
    function showEditForm(userId) {
        var form = document.getElementById('edit-form-' + userId);
        var displayInfo = document.getElementById('user-' + userId); // Corrected getElementById
        if (form.style.display == 'none') {
            form.style.display = 'block';
            displayInfo.style.display = 'none';
        } else {
            form.style.display = 'none';
            displayInfo.style.display = 'block';
        }
    }
</script>

</html>

